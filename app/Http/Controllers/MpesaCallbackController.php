<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Support\PhonePrivacy;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MpesaCallbackController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();
        $sanitizedPayload = $this->sanitizeCallbackPayload($payload);

        Log::info('Daraja callback received', ['payload' => $sanitizedPayload]);

        $callback = data_get($payload, 'Body.stkCallback', []);
        $checkoutRequestId = data_get($callback, 'CheckoutRequestID');
        $merchantRequestId = data_get($callback, 'MerchantRequestID');
        $resultCode = (int) data_get($callback, 'ResultCode', -1);
        $resultDesc = (string) data_get($callback, 'ResultDesc', 'Unknown callback result');

        $donation = Donation::query()
            ->where('checkout_request_id', $checkoutRequestId)
            ->orWhere('merchant_request_id', $merchantRequestId)
            ->latest('donation_id')
            ->first();

        if (!$donation) {
            Log::warning('Daraja callback did not match a donation', [
                'checkout_request_id' => $checkoutRequestId,
                'merchant_request_id' => $merchantRequestId,
            ]);

            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
        }

        $metadataItems = data_get($callback, 'CallbackMetadata.Item', []);
        $meta = $this->metadataToMap($metadataItems);
        $wasCompleted = $donation->payment_status === 'Completed';

        if ($resultCode === 0) {
            $donation->update([
                'payment_status' => 'Completed',
                'payment_reference' => $meta['MpesaReceiptNumber'] ?? null,
                'payer_phone' => isset($meta['PhoneNumber'])
                    ? PhonePrivacy::hash((string) $meta['PhoneNumber'])
                    : $donation->payer_phone,
                'amount_kes' => isset($meta['Amount']) ? (float) $meta['Amount'] : $donation->amount_kes,
                'paid_at' => $this->parseTransactionDate($meta['TransactionDate'] ?? null),
                'callback_payload' => $sanitizedPayload,
                'notes' => 'Payment completed via M-Pesa.',
            ]);

            $this->recordPaymentAudit(
                action: 'M-Pesa payment completed',
                details: sprintf(
                    'Donation #%d completed. Amount %.2f KES. Receipt %s.',
                    $donation->donation_id,
                    (float) ($donation->amount_kes ?? 0),
                    (string) ($donation->payment_reference ?? 'N/A')
                ),
                ipAddress: $request->ip()
            );

            if (!$wasCompleted && $donation->donor) {
                $donation->donor->update([
                    'pad_count' => Donation::query()
                        ->where('donor_id', $donation->donor_id)
                        ->where('payment_status', 'Completed')
                        ->sum('pad_count'),
                ]);
            }
        } else {
            $donation->update([
                'payment_status' => 'Failed',
                'callback_payload' => $sanitizedPayload,
                'notes' => 'Payment failed: '.$resultDesc,
            ]);

            $this->recordPaymentAudit(
                action: 'M-Pesa payment failed',
                details: sprintf(
                    'Donation #%d failed. Reason: %s',
                    $donation->donation_id,
                    $resultDesc
                ),
                ipAddress: $request->ip()
            );
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }

    private function metadataToMap(array $items): array
    {
        $result = [];

        foreach ($items as $item) {
            $name = data_get($item, 'Name');
            if (!$name) {
                continue;
            }

            $result[(string) $name] = data_get($item, 'Value');
        }

        return $result;
    }

    private function sanitizeCallbackPayload(array $payload): array
    {
        $items = data_get($payload, 'Body.stkCallback.CallbackMetadata.Item', []);

        if (!is_array($items)) {
            return $payload;
        }

        foreach ($items as $index => $item) {
            $name = data_get($item, 'Name');
            if ($name === 'PhoneNumber') {
                data_set(
                    $payload,
                    'Body.stkCallback.CallbackMetadata.Item.'.$index.'.Value',
                    PhonePrivacy::hash((string) data_get($item, 'Value'))
                );
            }
        }

        return $payload;
    }

    private function parseTransactionDate(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $stringValue = (string) $value;
        if ($stringValue === '' || strlen($stringValue) !== 14) {
            return null;
        }

        try {
            return Carbon::createFromFormat('YmdHis', $stringValue)->toDateTimeString();
        } catch (\Throwable) {
            return null;
        }
    }

    private function recordPaymentAudit(string $action, string $details, ?string $ipAddress): void
    {
        $systemUser = User::query()->where('role', '=', 'Admin')->orderBy('id', 'asc')->first()
            ?? User::query()->orderBy('id', 'asc')->first();

        if (!$systemUser) {
            return;
        }

        DB::table('audit_logs')->insert([
            'user_id' => $systemUser->id,
            'user_role' => 'System',
            'action_performed' => $action.' - '.$details,
            'ip_address' => $ipAddress,
            'created_at' => now(),
        ]);
    }
}
