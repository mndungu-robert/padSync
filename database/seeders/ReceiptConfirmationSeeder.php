<?php

namespace Database\Seeders;

use App\Models\ReceiptConfirmation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReceiptConfirmationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $distributionIds = DB::table('distributions')->orderBy('distribution_id')->pluck('distribution_id');
        $coordinatorIds = DB::table('users')
            ->where('role', 'Coordinator')
            ->orderBy('id')
            ->pluck('id');

        $distributionOneId = $distributionIds->get(0);
        $distributionTwoId = $distributionIds->get(1) ?? $distributionOneId;
        $distributionThreeId = $distributionIds->get(2) ?? $distributionTwoId;

        $coordinatorOneId = $coordinatorIds->get(0);
        $coordinatorTwoId = $coordinatorIds->get(1) ?? $coordinatorOneId;

        ReceiptConfirmation::updateOrCreate(
            ['distribution_id' => $distributionOneId],
            [
                'coordinator_id' => $coordinatorOneId,
                'received_quantity' => 175,
                'confirmation_date' => now(),
            ]
        );

        ReceiptConfirmation::updateOrCreate(
            ['distribution_id' => $distributionTwoId],
            [
                'coordinator_id' => $coordinatorTwoId,
                'received_quantity' => 210,
                'confirmation_date' => now()->subHours(6),
            ]
        );

        ReceiptConfirmation::updateOrCreate(
            ['distribution_id' => $distributionThreeId],
            [
                'coordinator_id' => $coordinatorOneId,
                'received_quantity' => 198,
                'confirmation_date' => now()->subHours(12),
            ]
        );
    }
}
