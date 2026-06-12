<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicDonationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'quantity_pledged' => ['required', 'integer', 'min:1'],
        ]);

        // Find or create a shell user record for this donor email
        $donor = User::firstOrCreate(
            ['email' => $request->email],
            [
                'name' => 'Anonymous Donor',
                'username' => explode('@', $request->email)[0] . rand(10,99),
                'password' => bcrypt(Str::random(16)),
                'role' => 'Donor',
                'status' => 'Approved'
            ]
        );

        // Store the donation pledge mapping
        Donation::create([
            'donor_id' => $donor->id,
            'pad_count' => $request->quantity_pledged,
            'pledge_status' => 'Pledged',
            'pledge_date' => now(),
        ]);

        return back()->with('success', 'Thank you! Your donation pledge has been recorded.');
    }
}
