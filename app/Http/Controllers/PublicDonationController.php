<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Donor;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicDonationController extends Controller
{
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
            'phone' => ['nullable', 'string'],
            'donor_type' => ['required', 'in:Individual,Organization'],
            'quantity_pledged' => ['required', 'integer', 'min:1'],
        ]);

        $donorType = (string) $request->input('donor_type');
        $organizationName = $donorType === 'Organization'
            ? (string) $request->input('name')
            : null;

        // 1. Create or find donor
        $donor = Donor::firstOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->name,
                'phone' => $request->phone,
                'donor_type' => $donorType,
                'organization_name' => $organizationName,
            ]
        );

        $donor->update([
            'name' => $request->name,
            'donor_type' => $donorType,
            'organization_name' => $organizationName,
        ]);

        // 2. Create donation record
        Donation::create([
            'donor_id' => $donor->id,
            'pad_count' => $request->quantity_pledged,
            'pledge_date' => now(),
        ]);

        $donor->update([
            'pad_count' => Donation::query()->where('donor_id', '=', $donor->id)->sum('pad_count'),
        ]);

        return redirect()->back()->with('success', 'Your donation has been recorded. Thank you for your support.');
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
