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
        // Fetch public pledges joined with donor profiling details
        $pledges = Donation::select(
                        'donations.*', 
                        'donors.name as donor_name',
                        'donors.email as donor_email',
                        'donors.donor_type'
                     )
                     ->join('donors', 'donations.donor_id', '=', 'donors.id')
                ->selectRaw("CASE WHEN donations.fulfillment_date IS NULL THEN 'Pledged' ELSE 'Fully Received' END as fulfillment_state")
                     ->orderBy('donations.created_at', 'desc')
                     ->get();

        return view('manager.donations.index', [
            'pledges' => $pledges,
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

        if (($donation->payment_status ?? 'Completed') !== 'Completed') {
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
