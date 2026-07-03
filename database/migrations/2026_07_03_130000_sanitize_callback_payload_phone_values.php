<?php

use App\Support\PhonePrivacy;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('donations', 'callback_payload')) {
            return;
        }

        DB::table('donations')
            ->select(['donation_id', 'callback_payload'])
            ->whereNotNull('callback_payload')
            ->orderBy('donation_id')
            ->chunkById(200, function ($rows): void {
                foreach ($rows as $row) {
                    $decoded = json_decode((string) $row->callback_payload, true);
                    if (!is_array($decoded)) {
                        continue;
                    }

                    $sanitized = $this->sanitizeArray($decoded);

                    DB::table('donations')
                        ->where('donation_id', $row->donation_id)
                        ->update(['callback_payload' => json_encode($sanitized)]);
                }
            }, 'donation_id');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Redaction is irreversible.
    }

    private function sanitizeArray(array $payload): array
    {
        foreach ($payload as $key => $value) {
            if (is_array($value)) {
                $payload[$key] = $this->sanitizeArray($value);
                continue;
            }

            if (is_string($key) && in_array($key, ['PhoneNumber', 'MSISDN', 'PartyA', 'PartyB'], true)) {
                $payload[$key] = PhonePrivacy::hash((string) $value);
            }
        }

        if (isset($payload['Name'], $payload['Value']) && is_string($payload['Name'])) {
            if (in_array($payload['Name'], ['PhoneNumber', 'MSISDN', 'PartyA', 'PartyB'], true)) {
                $payload['Value'] = PhonePrivacy::hash((string) $payload['Value']);
            }
        }

        return $payload;
    }
};
