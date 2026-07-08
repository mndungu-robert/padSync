<?php

namespace App\Http\Controllers;

use App\Models\Distribution;
use App\Models\Enrollment;
use App\Models\ReceiptConfirmation;
use App\Models\School;
use App\Models\ShortfallReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CoordinatorController extends Controller
{
    private const PACKETS_PER_GIRL_PER_MONTH = 2;

    public function dashboard(): View
    {
        $schoolId = Auth::user()?->school_id;
        $school = $schoolId ? School::query()->whereKey($schoolId)->first() : null;

        if (! $school) {
            return view('coordinator.dashboard', [
                'school' => null,
                'metrics' => [
                    'enrollment_logs' => 0,
                    'latest_enrollment' => 0,
                    'open_shortfalls' => 0,
                    'total_shortfall' => 0,
                ],
                'recentEnrollments' => collect(),
                'recentShortfalls' => collect(),
                'fulfilmentPercent' => 0,
                'fulfilmentLabel' => '0 of 0 pads covered',
                'insights' => [
                    'required_pads' => 0,
                    'covered_pads' => 0,
                    'remaining_pads' => 0,
                    'pending_confirmations' => 0,
                    'last_enrollment_date' => null,
                    'last_shortfall_date' => null,
                ],
            ]);
        }

        $recentEnrollments = Enrollment::query()
            ->where('school_id', $school->school_id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentShortfalls = ShortfallReport::query()
            ->where('school_id', $school->school_id)
            ->orderBy('report_date', 'desc')
            ->take(5)
            ->get();

        $latestEnrollmentCount = (int) optional($recentEnrollments->first())->girl_count;

        $now = Carbon::now();
        $currentMonth = $now->format('F');
        $currentAcademicYear = $now->format('Y');

        $currentEnrollment = Enrollment::query()
            ->where('school_id', $school->school_id)
            ->where('month', $currentMonth)
            ->where('academic_year', $currentAcademicYear)
            ->orderByDesc('created_at')
            ->first();

        $latestEnrollmentRecord = Enrollment::query()
            ->where('school_id', $school->school_id)
            ->orderByDesc('created_at')
            ->first();

        $fulfilmentSource = $currentEnrollment ?? $latestEnrollmentRecord;

        $monthStart = $now->copy()->startOfMonth()->toDateString();
        $monthEnd = $now->copy()->endOfMonth()->toDateString();

        $monthlyShortfall = ShortfallReport::query()
            ->where('school_id', $school->school_id)
            ->whereBetween('report_date', [$monthStart, $monthEnd])
            ->orderByDesc('report_date')
            ->first();

        if ($monthlyShortfall) {
            $requiredPads = (int) $monthlyShortfall->required_pads;
            $baseCovered = max(0, $requiredPads - (int) $monthlyShortfall->shortfall);

            $distributionCovered = (int) Distribution::query()
                ->where('school_id', $school->school_id)
                ->where('status', 'Received')
                ->whereBetween('distribution_date', [$monthStart, $monthEnd])
                ->whereDate('distribution_date', '>=', $monthlyShortfall->report_date)
                ->sum('quantity_distributed');
        } else {
            $requiredPads = $fulfilmentSource ? ((int) $fulfilmentSource->girl_count * self::PACKETS_PER_GIRL_PER_MONTH) : 0;
            $baseCovered = $fulfilmentSource ? (int) $fulfilmentSource->government_pads_received : 0;

            $distributionCovered = (int) Distribution::query()
                ->where('school_id', $school->school_id)
                ->where('status', 'Received')
                ->whereBetween('distribution_date', [$monthStart, $monthEnd])
                ->sum('quantity_distributed');
        }

        $totalCovered = max(0, $baseCovered + $distributionCovered);
        $effectiveCovered = min($requiredPads, $totalCovered);
        $fulfilmentPercent = $requiredPads > 0
            ? (int) round(($effectiveCovered / $requiredPads) * 100)
            : 0;
        $fulfilmentLabel = number_format($totalCovered).' of '.number_format($requiredPads).' pads covered';

        $pendingConfirmations = Distribution::query()
            ->where('school_id', $school->school_id)
            ->where(function ($query) {
                $query->where('status', 'Pending')
                    ->orWhere('status', 'Dispatched');
            })
            ->count();

        $lastEnrollmentDate = $recentEnrollments->isNotEmpty()
            ? Carbon::parse($recentEnrollments->first()->created_at)->format('d M Y')
            : null;

        $lastShortfallDate = $recentShortfalls->isNotEmpty()
            ? Carbon::parse($recentShortfalls->first()->report_date)->format('d M Y')
            : null;

        return view('coordinator.dashboard', [
            'school' => $school,
            'metrics' => [
                'enrollment_logs' => Enrollment::query()->where('school_id', $school->school_id)->count(),
                'latest_enrollment' => $latestEnrollmentCount,
                'open_shortfalls' => ShortfallReport::query()
                    ->where('school_id', $school->school_id)
                    ->whereIn('status', ['Submitted', 'Dispatched'])
                    ->count(),
                'total_shortfall' => (int) ShortfallReport::query()->where('school_id', $school->school_id)->sum('shortfall'),
            ],
            'recentEnrollments' => $recentEnrollments,
            'recentShortfalls' => $recentShortfalls,
            'fulfilmentPercent' => $fulfilmentPercent,
            'fulfilmentLabel' => $fulfilmentLabel,
            'insights' => [
                'required_pads' => $requiredPads,
                'covered_pads' => $totalCovered,
                'remaining_pads' => max(0, $requiredPads - $effectiveCovered),
                'pending_confirmations' => $pendingConfirmations,
                'last_enrollment_date' => $lastEnrollmentDate,
                'last_shortfall_date' => $lastShortfallDate,
            ],
        ]);
    }

    public function enrollmentsIndex(): View
    {
        $schoolId = Auth::user()?->school_id;
        $school = $schoolId ? School::query()->whereKey($schoolId)->first() : null;

        $enrollments = collect();
        if ($school) {
            $enrollments = Enrollment::query()
                ->where('school_id', $school->school_id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('coordinator.enrollments.index', [
            'school' => $school,
            'enrollments' => $enrollments,
            'active' => 'enrollments',
        ]);
    }

    public function storeEnrollment(): RedirectResponse
    {
        $schoolId = Auth::user()?->school_id;
        $school = $schoolId ? School::query()->whereKey($schoolId)->first() : null;

        if (! $school) {
            return redirect()->route('coordinator.enrollments.index')
                ->with('error', 'Your account is not linked to a school yet. Contact a Program Manager.');
        }

        $currentMonth = Carbon::now()->format('F');
        $currentAcademicYear = Carbon::now()->format('Y');

        $validated = request()->validate([
            'academic_year' => [
                'required',
                'string',
                Rule::in([$currentAcademicYear]),
            ],
            'month' => [
                'required',
                'string',
                Rule::in([$currentMonth]),
            ],
            'girl_count' => ['required', 'integer', 'min:0'],
            'government_pads_received' => ['required', 'integer', 'min:0'],
        ]);

        $maxGovernmentPackets = (int) $validated['girl_count'] * self::PACKETS_PER_GIRL_PER_MONTH;

        if ((int) $validated['government_pads_received'] > $maxGovernmentPackets) {
            return redirect()->route('coordinator.enrollments.index')
                ->withInput()
            ->with('error', 'Government packets received cannot exceed the monthly requirement of 2 packets per girl.');
        }

        $alreadySubmitted = Enrollment::query()
            ->where('school_id', $school->school_id)
            ->where('academic_year', $validated['academic_year'])
            ->where('month', $validated['month'])
            ->exists();

        if ($alreadySubmitted) {
            return redirect()->route('coordinator.enrollments.index')
                ->withInput()
                ->with('error', 'Monthly enrollment already submitted for this academic year. Only one entry per month is allowed.');
        }

        $enrollment = Enrollment::query()->create([
            'school_id' => $school->school_id,
            'academic_year' => $validated['academic_year'],
            'month' => $validated['month'],
            'girl_count' => $validated['girl_count'],
            'government_pads_received' => $validated['government_pads_received'],
        ]);

        // Keep school-level enrollment in sync for manager screens using schools.enrollment.
        $school->update([
            'enrollment' => (int) $enrollment->girl_count,
        ]);

        return redirect()->route('coordinator.enrollments.index')
            ->with('success', 'Enrollment log submitted successfully.');
    }

    public function shortfallsIndex(Request $request): View
    {
        $schoolId = Auth::user()?->school_id;
        $school = $schoolId ? School::query()->whereKey($schoolId)->first() : null;

        $reports = collect();
        $prefillEnrollment = null;
        if ($school) {
            $reports = ShortfallReport::query()
                ->where('school_id', $school->school_id)
                ->orderBy('report_date', 'desc')
                ->get();

            $latestEnrollment = Enrollment::query()
                ->where('school_id', $school->school_id)
                ->orderBy('created_at', 'desc')
                ->first();

            $prefillEnrollment = $latestEnrollment;

            $enrollmentId = $request->integer('enrollment_id');
            if ($enrollmentId > 0) {
                $selectedEnrollment = Enrollment::query()
                    ->where('school_id', $school->school_id)
                    ->where('enrollment_id', $enrollmentId)
                    ->first();

                if ($selectedEnrollment) {
                    $prefillEnrollment = $selectedEnrollment;
                }
            }
        }

        return view('coordinator.shortfalls.index', [
            'school' => $school,
            'reports' => $reports,
            'prefillEnrollment' => $prefillEnrollment,
            'prefillReportDate' => $request->query('report_date', now()->toDateString()),
            'active' => 'shortfalls',
        ]);
    }

    public function storeShortfall(): RedirectResponse
    {
        $schoolId = Auth::user()?->school_id;
        $school = $schoolId ? School::query()->whereKey($schoolId)->first() : null;

        if (! $school) {
            return redirect()->route('coordinator.shortfalls.index')
                ->with('error', 'Your account is not linked to a school yet. Contact a Program Manager.');
        }

        $validated = request()->validate([
            'report_date' => ['required', 'date'],
            'enrollment_id' => ['nullable', 'integer'],
            'required_pads' => ['nullable', 'integer', 'min:0'],
            'available_pads' => ['required', 'integer', 'min:0'],
            'government_pads_received' => ['nullable', 'integer', 'min:0'],
        ]);

        $reportMonth = Carbon::parse($validated['report_date'])->format('F');

        $sourceEnrollment = null;

        if (! empty($validated['enrollment_id'])) {
            $sourceEnrollment = Enrollment::query()
                ->where('school_id', $school->school_id)
                ->where('enrollment_id', (int) $validated['enrollment_id'])
                ->first();

            if (! $sourceEnrollment) {
                return redirect()->route('coordinator.shortfalls.index')
                    ->withInput()
                    ->with('error', 'Selected enrollment record was not found for your school.');
            }
        }

        if (! $sourceEnrollment) {
            $matchingEnrollment = Enrollment::query()
                ->where('school_id', $school->school_id)
                ->where('month', $reportMonth)
                ->orderBy('created_at', 'desc')
                ->first();

            $latestEnrollment = Enrollment::query()
                ->where('school_id', $school->school_id)
                ->orderBy('created_at', 'desc')
                ->first();

            $sourceEnrollment = $matchingEnrollment ?? $latestEnrollment;
        }

        if (! $sourceEnrollment) {
            return redirect()->route('coordinator.shortfalls.index')
                ->withInput()
                ->with('error', 'Submit an enrollment record first so shortfall can auto-load required and government packet values.');
        }

        $requiredPads = $validated['required_pads'] ?? ((int) $sourceEnrollment->girl_count * self::PACKETS_PER_GIRL_PER_MONTH);
        $governmentPadsReceived = $validated['government_pads_received'] ?? (int) $sourceEnrollment->government_pads_received;

        $existingReport = ShortfallReport::query()
            ->where('school_id', $school->school_id)
            ->whereBetween('report_date', [
                Carbon::parse($validated['report_date'])->startOfMonth()->toDateString(),
                Carbon::parse($validated['report_date'])->endOfMonth()->toDateString(),
            ])
            ->exists();

        if ($existingReport) {
            return redirect()->route('coordinator.shortfalls.index')
                ->withInput()
                ->with('error', 'Shortfall report already exists for this month. Only one shortfall entry is allowed per month.');
        }

        $totalAvailablePads = (int) $validated['available_pads'] + (int) $governmentPadsReceived;
        $shortfall = max(0, (int) $requiredPads - $totalAvailablePads);

        ShortfallReport::query()->create([
            'school_id' => $school->school_id,
            'report_date' => $validated['report_date'],
            'required_pads' => $requiredPads,
            'available_pads' => $validated['available_pads'],
            'government_pads_received' => $governmentPadsReceived,
            'shortfall' => $shortfall,
            'status' => 'Submitted',
        ]);

        return redirect()->route('coordinator.shortfalls.index')
            ->with('success', 'Shortfall report submitted successfully.');
    }

    public function distributionsIndex(): View
    {
        $schoolId = Auth::user()?->school_id;
        $school = $schoolId ? School::query()->whereKey($schoolId)->first() : null;

        $pendingDispatches = collect();
        $receivedDispatches = collect();

        if ($school) {
            $pendingDispatches = Distribution::query()
                ->where('school_id', $school->school_id)
                ->whereIn('status', ['Pending', 'Dispatched'])
                ->orderBy('distribution_date', 'desc')
                ->get();

            $receivedDispatches = Distribution::query()
                ->with('receiptConfirmation')
                ->where('school_id', $school->school_id)
                ->where('status', 'Received')
                ->orderBy('distribution_date', 'desc')
                ->get();
        }

        return view('coordinator.distributions.index', [
            'school' => $school,
            'pendingDispatches' => $pendingDispatches,
            'receivedDispatches' => $receivedDispatches,
            'active' => 'distributions',
        ]);
    }

    public function confirmDistribution(Distribution $distribution): RedirectResponse
    {
        $schoolId = Auth::user()?->school_id;

        if (! $schoolId) {
            return redirect()->route('coordinator.distributions.index')
                ->with('error', 'Your account is not linked to a school yet. Contact a Program Manager.');
        }

        if ((int) $distribution->school_id !== (int) $schoolId) {
            abort(403, 'You are not authorized to confirm this dispatch.');
        }

        if ($distribution->status === 'Received') {
            return redirect()->route('coordinator.distributions.index')
                ->with('error', 'This dispatch has already been confirmed as received.');
        }

        if (! in_array($distribution->status, ['Pending', 'Dispatched'], true)) {
            return redirect()->route('coordinator.distributions.index')
            ->with('error', 'Only pending or dispatched records can be confirmed.');
        }

        DB::transaction(function () use ($distribution) {
            ReceiptConfirmation::query()->firstOrCreate(
                ['distribution_id' => $distribution->distribution_id],
                [
                    'coordinator_id' => Auth::id(),
                    'received_quantity' => $distribution->quantity_distributed,
                    'confirmation_date' => now(),
                ]
            );

            $distribution->update(['status' => 'Received']);

            $distributionMonth = Carbon::parse($distribution->distribution_date);
            $linkedShortfall = ShortfallReport::query()
                ->where('school_id', $distribution->school_id)
                ->where('status', 'Dispatched')
                ->whereBetween('report_date', [
                    $distributionMonth->copy()->startOfMonth()->toDateString(),
                    $distributionMonth->copy()->endOfMonth()->toDateString(),
                ])
                ->orderByDesc('report_date')
                ->first();

            if ($linkedShortfall) {
                $linkedShortfall->update(['status' => 'Received']);
            }
        });

        return redirect()->route('coordinator.distributions.index')
            ->with('success', 'Dispatch receipt confirmed successfully.');
    }
}