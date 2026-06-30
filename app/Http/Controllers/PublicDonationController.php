<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Donor;
use App\Models\Enrollment;
use App\Services\DarajaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PublicDonationController extends Controller
{
    public function __construct(private readonly DarajaService $darajaService)
    {
    }

    public function home()
    {
        return view('welcome', [
            'stats' => $this->publicImpactStats(),
        ]);
    }

    public function create()
    {
        return view('donations.create', [
            'stats' => $this->publicImpactStats(),
        ]);
    }

    public function learnMore()
    {
        return view('public.learn-more', [
            'stats' => $this->publicImpactStats(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string'],
            'donor_type' => ['required', 'in:Individual,Organization'],
            'quantity_pledged' => ['required', 'integer', 'min:1'],
            'amount_kes' => ['required', 'numeric', 'min:1'],
        ]);

        $normalizedPhone = $this->normalizeKenyanPhone((string) $request->input('phone'));
        if ($normalizedPhone === null) {
            return back()->withErrors([
                'phone' => 'Enter a valid Kenyan phone number (for example 07XXXXXXXX or 2547XXXXXXXX).',
            ])->withInput();
        }

        $donorType = (string) $request->input('donor_type');
        $organizationName = $donorType === 'Organization'
            ? (string) $request->input('name')
            : null;

        // 1. Create or find donor
        $donor = Donor::firstOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->name,
                'donor_type' => $donorType,
                'organization_name' => $organizationName,
            ]
        );

        $donor->update([
            'name' => $request->name,
            'donor_type' => $donorType,
            'organization_name' => $organizationName,
        ]);

        // 2. Create donation record in pending payment state
        $donation = Donation::create([
            'donor_id' => $donor->id,
            'pad_count' => $request->quantity_pledged,
            'pledge_date' => now(),
            'amount_kes' => (float) $request->input('amount_kes'),
            'payment_method' => 'M-Pesa',
            'payment_status' => 'Pending',
            'payer_phone' => $normalizedPhone,
            'notes' => 'Awaiting M-Pesa confirmation.',
        ]);

        try {
            $stkResponse = $this->darajaService->stkPush([
                'amount' => (int) round((float) $request->input('amount_kes')),
                'phone' => $normalizedPhone,
                'account_reference' => 'DON-'.$donation->donation_id,
                'transaction_desc' => 'Donation for menstrual health program',
            ]);

            $donation->update([
                'merchant_request_id' => $stkResponse['MerchantRequestID'] ?? null,
                'checkout_request_id' => $stkResponse['CheckoutRequestID'] ?? null,
                'notes' => $stkResponse['CustomerMessage'] ?? 'STK push sent. Complete payment on your phone.',
            ]);

            return redirect()->back()->with('success', 'M-Pesa prompt sent. Enter your M-Pesa PIN on your phone to complete payment.');
        } catch (\Throwable $exception) {
            Log::error('Daraja STK push failed', [
                'error' => $exception->getMessage(),
                'donation_id' => $donation->donation_id,
            ]);

            $donation->update([
                'payment_status' => 'Failed',
                'notes' => 'M-Pesa request failed: '.$exception->getMessage(),
            ]);

            return redirect()->back()->withErrors([
                'phone' => 'Could not start M-Pesa payment. Confirm Daraja credentials and callback URL, then try again.',
            ])->withInput();
        }
    }

    private function normalizeKenyanPhone(string $input): ?string
    {
        $digits = preg_replace('/\D+/', '', $input) ?? '';

        if (str_starts_with($digits, '0') && strlen($digits) === 10) {
            return '254'.substr($digits, 1);
        }

        if (str_starts_with($digits, '254') && strlen($digits) === 12) {
            return $digits;
        }

        return null;
    }

    private function publicImpactStats(): array
    {
        $girlsEnrolled = (int) Enrollment::query()->sum('girl_count');
        $packetsNeededMonthly = $girlsEnrolled * 2;

        return [
            'schools_supported' => (int) DB::table('schools')->count('school_id'),
            'girls_enrolled' => $girlsEnrolled,
            'packets_needed_monthly' => $packetsNeededMonthly,
            'pads_needed_monthly' => $packetsNeededMonthly,
        ];
    }

}
