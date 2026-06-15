<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Donor;
use Illuminate\Http\Request;

class PublicDonationController extends Controller
{
    public function create()
    {
        return view('donations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string'],
            'donor_type' => ['required', 'in:Individual,Organization'],
            'organization_name' => ['nullable', 'string'],
            'quantity_pledged' => ['required', 'integer', 'min:1'],
        ]);
        $organizationName = $request->donor_type === 'Organization'
          ? $request->organization_name
          : null;

        // 1. Create or find donor
        $donor = Donor::firstOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->name,
                'phone' => $request->phone,
                'donor_type' => $request->donor_type,
                'organization_name' => $organizationName,
            ]
        );

        // 2. Create donation record
        // Donation::create([
        //     'donor_id' => $donor->id,
        //     'pad_count' => $request->quantity_pledged,
        //     'pledge_date' => now(),
        //     'pledge_status' => 'Pledged',
        //     'received_count' => 0,
        // ]);

        return redirect()->back()->with('success', 'Your donation has been recorded. Thank you for your support.');
    }
}
