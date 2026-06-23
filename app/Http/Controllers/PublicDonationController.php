<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Donor;
use App\Models\Enrollment;
use App\Models\School;
use App\Models\ShortfallReport;
use Illuminate\Http\Request;

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
            'quantity_pledged' => ['required', 'integer', 'min:1'],
        ]);

        $donorType = $this->inferDonorType($request->name);
        $organizationName = $donorType === 'Organization' ? $request->name : null;

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
        return [
            'schools_supported' => School::query()->count(),
            'girls_enrolled' => (int) Enrollment::query()->sum('girl_count'),
            'pads_still_needed' => (int) ShortfallReport::query()
                ->whereIn('status', ['Submitted', 'Dispatched'])
                ->sum('shortfall'),
        ];
    }

    private function inferDonorType(string $name): string
    {
        $normalizedName = strtolower(trim($name));

        $organizationKeywords = [
            'ltd', 'limited', 'llc', 'inc', 'company', 'co.', 'foundation',
            'ngo', 'church', 'school', 'university', 'college', 'association',
            'group', 'trust', 'ministry', 'agency', 'bank', 'hospital',
        ];

        foreach ($organizationKeywords as $keyword) {
            if (str_contains($normalizedName, $keyword)) {
                return 'Organization';
            }
        }

        return 'Individual';
    }
}
