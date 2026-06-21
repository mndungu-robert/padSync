<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Donor;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    private function refreshDonorPadCount(int $donorId): void
    {
        $totalPads = Donation::query()
            ->where('donor_id', '=', $donorId)
            ->sum('pad_count');

        Donor::query()->where('id', '=', $donorId)->update([
            'pad_count' => $totalPads,
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $donations = Donation::with('donor')->latest()->get();

        return view('admin.donations.index', compact('donations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $donors = Donor::query()->orderBy('name', 'asc')->get();

        return view('admin.donations.create', compact('donors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'donor_id' => 'required|exists:donors,id',
            'pad_count' => 'required|integer|min:1',
            'pledge_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:pledge_date',
            'notes' => 'nullable|string',
        ]);

        $donation = Donation::create($validated);
        $this->refreshDonorPadCount((int) $donation->donor_id);

        return redirect()->route('admin.donations.index')->with('success', 'Donation added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Donation $donation)
    {
        $donation->load('donor');

        return view('admin.donations.show', compact('donation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Donation $donation)
    {
        $donors = Donor::query()->orderBy('name', 'asc')->get();

        return view('admin.donations.edit', compact('donation', 'donors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Donation $donation)
    {
        $validated = $request->validate([
            'donor_id' => 'required|exists:donors,id',
            'pad_count' => 'required|integer|min:1',
            'pledge_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:pledge_date',
            'notes' => 'nullable|string',
        ]);

        $oldDonorId = (int) $donation->donor_id;

        $donation->update($validated);

        $this->refreshDonorPadCount((int) $donation->donor_id);

        if ($oldDonorId !== (int) $donation->donor_id) {
            $this->refreshDonorPadCount($oldDonorId);
        }

        return redirect()->route('admin.donations.index')->with('success', 'Donation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Donation $donation)
    {
        $donorId = (int) $donation->donor_id;
        Donation::destroy($donation->donation_id);
        $this->refreshDonorPadCount($donorId);

        return redirect()->route('admin.donations.index')->with('success', 'Donation deleted successfully.');
    }
}
