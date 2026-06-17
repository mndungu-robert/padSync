<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Donor;
use Illuminate\Http\Request;

class DonationController extends Controller
{
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
            'pledge_status' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        Donation::create($validated);

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
            'pledge_status' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $donation->update($validated);

        return redirect()->route('admin.donations.index')->with('success', 'Donation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Donation $donation)
    {
        Donation::destroy($donation->donation_id);

        return redirect()->route('admin.donations.index')->with('success', 'Donation deleted successfully.');
    }
}
