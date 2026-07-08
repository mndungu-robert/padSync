<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Models\Donation;
use Illuminate\Support\Facades\DB;

class ManagerDonationController extends Controller
{
    /**
     * Display the public donation pledges tracking registry.
     */
    public function index()
    {
        $baseQuery = Donation::query()
            ->select(
                'donations.*',
                'donors.name as donor_name',
                'donors.email as donor_email',
                'donors.donor_type'
            )
            ->join('donors', 'donations.donor_id', '=', 'donors.id')
            ->orderBy('donations.created_at', 'desc');

        $physicalPledges = (clone $baseQuery)
            ->where('donations.contribution_type', 'Donate Pads')
            ->selectRaw("CASE WHEN donations.fulfillment_date IS NULL THEN 'Pledged' ELSE 'Fully Received' END as fulfillment_state")
            ->get();

        $moneyDonations = (clone $baseQuery)
            ->where('donations.contribution_type', 'Donate Money')
            ->get();

        return view('manager.donations.index', [
            'physicalPledges' => $physicalPledges,
            'moneyDonations' => $moneyDonations,
            'active'  => 'donations' // Highlights correct navigation bar link
        ]);
    }

    /**
     * Mark an existing pledge as physically received and move quantity into inventory.
     */
    public function markReceived(Request $request, Donation $donation)
    {
        $validated = $request->validate([
            'received_date' => ['nullable', 'date', 'after_or_equal:pledge_date'],
        ]);

        if ($donation->fulfillment_date !== null) {
            return redirect()->route('manager.donations.index')
                ->with('success', 'This donation was already marked as received.');
        }

        if (($donation->contribution_type ?? 'Donate Pads') !== 'Donate Pads') {
            return redirect()->route('manager.donations.index')
                ->withErrors(['received_date' => 'Only pad pledges can be moved into inventory.']);
        }

        if (!in_array(($donation->payment_status ?? 'Successful'), ['Successful', 'Not Required'], true)) {
            return redirect()->route('manager.donations.index')
                ->withErrors(['received_date' => 'Only paid donations can be marked as received in inventory.']);
        }

        DB::transaction(function () use ($donation, $validated) {
            $donation->update([
                'fulfillment_date' => $validated['received_date'] ?? now()->toDateString(),
            ]);

            $inventory = Inventory::firstOrCreate([], [
                'quantity_available' => 0,
                'allocated_stock' => 0,
                'reorder_level' => 100,
            ]);

            $inventory->increment('quantity_available', (int) $donation->pad_count);
        });

        return redirect()->route('manager.donations.index')
            ->with('success', 'Donation marked as received and added to available inventory.');
    }
}
