<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Donor;
use App\Models\Distribution;
use App\Models\Enrollment;
use App\Support\PhonePrivacy;
use App\Services\DarajaService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PublicDonationController extends Controller
{
    public function __construct(private readonly DarajaService $darajaService)
    {
    }

    public function home()
    {
        $supportJourney = $this->girlsSupportJourney();

        return view('welcome', [
            'stats' => $this->publicImpactStats(),
            'monthlyDistributions' => $this->monthlyDistributionStats(),
            'girlsSupportStart' => $supportJourney['started'],
            'girlsSupportCurrent' => $supportJourney['current'],
        ]);
    }

    public function index()
    {
        return $this->home();
    }

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
            'contribution_type' => ['required', 'in:Donate Pads,Donate Money'],
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string'],
            'donor_type' => ['nullable', 'in:Individual,Organization'],
            'quantity_pledged' => ['nullable', 'integer', 'min:1'],
            'amount_kes' => ['nullable', 'numeric', 'min:1'],
        ]);

        $contributionType = (string) $request->input('contribution_type');

        $request->validate([
            'donor_type' => ['required', 'in:Individual,Organization'],
        ]);

        if ($contributionType === 'Donate Pads') {
            return $this->storePadsPledge($request);
        }

        return $this->storeMoneyDonation($request);
    }

    private function storePadsPledge(Request $request)
    {
        $request->validate([
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
                'donor_type' => $donorType,
                'organization_name' => $organizationName,
            ]
        );

        $donor->update([
            'name' => $request->name,
            'donor_type' => $donorType,
            'organization_name' => $organizationName,
        ]);

        Donation::create([
            'donor_id' => $donor->id,
            'pad_count' => $request->quantity_pledged,
            'pledge_date' => now(),
            'contribution_type' => 'Donate Pads',
            'amount_kes' => 0,
            'payment_method' => 'In-Kind',
            'payment_status' => 'Not Required',
            'notes' => 'Public in-kind pads pledge.',
        ]);

        $donor->update([
            'pad_count' => Donation::query()
                ->where('donor_id', '=', $donor->id)
                ->where('contribution_type', 'Donate Pads')
                ->sum('pad_count'),
        ]);

        return redirect()->back()->with('success', 'Pads pledge recorded successfully. Thank you for supporting the program.');
    }

    private function storeMoneyDonation(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string'],
            'amount_kes' => ['required', 'numeric', 'min:1'],
        ]);

        $normalizedPhone = $this->normalizeKenyanPhone((string) $request->input('phone'));
        if ($normalizedPhone === null) {
            return back()->withErrors([
                'phone' => 'Enter a valid Kenyan phone number (for example 07XXXXXXXX or 2547XXXXXXXX).',
            ])->withInput();
        }

        $donorType = (string) $request->input('donor_type');
        $organizationName = $donorType === 'Organization'
            ? (string) $request->input('name')
            : null;

        $donor = Donor::firstOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->name,
                'donor_type' => $donorType,
                'organization_name' => $organizationName,
            ]
        );

        $donor->update([
            'name' => $request->name,
            'donor_type' => $donorType,
            'organization_name' => $organizationName,
        ]);

        $donation = Donation::create([
            'donor_id' => $donor->id,
            'pad_count' => 0,
            'pledge_date' => now(),
            'contribution_type' => 'Donate Money',
            'amount_kes' => (float) $request->input('amount_kes'),
            'payment_method' => 'M-Pesa',
            'payment_status' => 'Pending',
            'payer_phone' => PhonePrivacy::hash($normalizedPhone),
            'notes' => 'Awaiting M-Pesa confirmation.',
        ]);

        try {
            $stkResponse = $this->darajaService->stkPush([
                'amount' => (int) round((float) $request->input('amount_kes')),
                'phone' => $normalizedPhone,
                'account_reference' => 'DON-'.$donation->donation_id,
                'transaction_desc' => 'Donation for menstrual health program',
            ]);

            $donation->update([
                'merchant_request_id' => $stkResponse['MerchantRequestID'] ?? null,
                'checkout_request_id' => $stkResponse['CheckoutRequestID'] ?? null,
                'notes' => $stkResponse['CustomerMessage'] ?? 'STK push sent. Complete payment on your phone.',
            ]);

            return redirect()->back()->with('success', 'M-Pesa prompt sent. Enter your M-Pesa PIN on your phone to complete payment.');
        } catch (\Throwable $exception) {
            Log::error('Daraja STK push failed', [
                'error' => $exception->getMessage(),
                'donation_id' => $donation->donation_id,
            ]);

            $donation->update([
                'payment_status' => 'Failed',
                'notes' => 'M-Pesa request failed: '.$exception->getMessage(),
            ]);

            return redirect()->back()->withErrors([
                'phone' => 'Could not start M-Pesa payment. Confirm Daraja credentials and callback URL, then try again.',
            ])->withInput();
        }
    }

    private function normalizeKenyanPhone(string $input): ?string
    {
        $digits = preg_replace('/\D+/', '', $input) ?? '';

        if (str_starts_with($digits, '0') && strlen($digits) === 10) {
            return '254'.substr($digits, 1);
        }

        if (str_starts_with($digits, '254') && strlen($digits) === 12) {
            return $digits;
        }

        return null;
    }

    private function publicImpactStats(): array
    {
        $girlsEnrolled = (int) Enrollment::query()->sum('girl_count');
        $packetsNeededMonthly = $girlsEnrolled * 2;

        $monthBuckets = collect(range(5, 1))->map(fn (int $offset) => Carbon::now()->subMonths($offset));
        $monthBuckets = $monthBuckets->push(Carbon::now());
        $startDate = $monthBuckets->first()->copy()->startOfMonth();

        $moneyByMonth = Donation::query()
            ->select([
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as bucket"),
                DB::raw('SUM(amount_kes) as total'),
            ])
            ->where('contribution_type', 'Donate Money')
            ->where('payment_status', 'Completed')
            ->whereDate('created_at', '>=', $startDate)
            ->groupBy('bucket')
            ->pluck('total', 'bucket');

        $padsByMonth = Donation::query()
            ->select([
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as bucket"),
                DB::raw('SUM(pad_count) as total'),
            ])
            ->where('contribution_type', 'Donate Pads')
            ->whereDate('created_at', '>=', $startDate)
            ->groupBy('bucket')
            ->pluck('total', 'bucket');

        return [
            'schools_supported' => (int) DB::table('schools')->count('school_id'),
            'girls_enrolled' => $girlsEnrolled,
            'packets_needed_monthly' => $packetsNeededMonthly,
            'pads_needed_monthly' => $packetsNeededMonthly,
            'trend_month_labels' => $monthBuckets->map(fn (Carbon $month) => $month->format('M Y'))->values(),
            'money_received_monthly' => $monthBuckets
                ->map(fn (Carbon $month) => (float) ($moneyByMonth[$month->format('Y-m')] ?? 0))
                ->values(),
            'pads_pledged_monthly' => $monthBuckets
                ->map(fn (Carbon $month) => (int) ($padsByMonth[$month->format('Y-m')] ?? 0))
                ->values(),
        ];
    }

    private function monthlyDistributionStats(): array
    {
        $monthBuckets = collect(range(5, 1))->map(fn (int $offset) => Carbon::now()->subMonths($offset));
        $monthBuckets = $monthBuckets->push(Carbon::now());
        $startDate = $monthBuckets->first()->copy()->startOfMonth();

        $totalsByMonth = Distribution::query()
            ->select([
                DB::raw("DATE_FORMAT(distribution_date, '%Y-%m') as bucket"),
                DB::raw('SUM(quantity_distributed) as total'),
            ])
            ->whereDate('distribution_date', '>=', $startDate)
            ->groupBy('bucket')
            ->pluck('total', 'bucket');

        return $monthBuckets
            ->map(fn (Carbon $month) => [
                'month' => $month->format('M'),
                'total' => (int) ($totalsByMonth[$month->format('Y-m')] ?? 0),
            ])
            ->values()
            ->all();
    }

    private function girlsSupportJourney(): array
    {
        $currentGirlsSupported = (int) DB::table('schools')->sum('enrollment');

        $firstEnrollment = Enrollment::query()
            ->orderBy('created_at', 'asc')
            ->first();

        $startedGirlsSupported = 0;
        if ($firstEnrollment) {
            $firstMonthStart = Carbon::parse($firstEnrollment->created_at)->startOfMonth()->toDateString();
            $firstMonthEnd = Carbon::parse($firstEnrollment->created_at)->endOfMonth()->toDateString();

            $startedGirlsSupported = (int) Enrollment::query()
                ->where('created_at', '>=', $firstMonthStart)
                ->where('created_at', '<=', $firstMonthEnd)
                ->sum('girl_count');
        }

        if ($startedGirlsSupported <= 0) {
            $startedGirlsSupported = $currentGirlsSupported;
        }

        return [
            'started' => $startedGirlsSupported,
            'current' => $currentGirlsSupported,
        ];
    }

}
