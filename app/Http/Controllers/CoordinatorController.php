<?php

namespace App\Http\Controllers;

use App\Models\Distribution;
use App\Models\Enrollment;
use App\Models\ReceiptConfirmation;
use App\Models\School;
use App\Models\ShortfallReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CoordinatorController extends Controller
{
    public function dashboard(): View
    {
        $schoolId = Auth::user()?->school_id;
        $school = $schoolId ? School::find($schoolId) : null;

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

        $latestEnrollment = (int) optional($recentEnrollments->first())->girl_count;

        return view('coordinator.dashboard', [
            'school' => $school,
            'metrics' => [
                'enrollment_logs' => Enrollment::query()->where('school_id', $school->school_id)->count(),
                'latest_enrollment' => $latestEnrollment,
                'open_shortfalls' => ShortfallReport::query()
                    ->where('school_id', $school->school_id)
                    ->whereIn('status', ['Submitted', 'Dispatched'])
                    ->count(),
                'total_shortfall' => (int) ShortfallReport::query()->where('school_id', $school->school_id)->sum('shortfall'),
            ],
            'recentEnrollments' => $recentEnrollments,
            'recentShortfalls' => $recentShortfalls,
        ]);
    }

    public function enrollmentsIndex(): View
    {
        $schoolId = Auth::user()?->school_id;
        $school = $schoolId ? School::find($schoolId) : null;

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
        $school = $schoolId ? School::find($schoolId) : null;

        if (! $school) {
            return redirect()->route('coordinator.enrollments.index')
                ->with('error', 'Your account is not linked to a school yet. Contact a Program Manager.');
        }

        $currentMonth = Carbon::now()->format('F');

        $validated = request()->validate([
            'academic_year' => ['required', 'string', 'max:10'],
            'month' => [
                'required',
                'string',
                Rule::in([$currentMonth]),
            ],
            'girl_count' => ['required', 'integer', 'min:0'],
            'government_pads_received' => ['required', 'integer', 'min:0'],
        ]);

        if ((int) $validated['government_pads_received'] > (int) $validated['girl_count']) {
            return redirect()->route('coordinator.enrollments.index')
                ->withInput()
                ->with('error', 'Government pads received cannot exceed total girls enrolled for the same month.');
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

    public function shortfallsIndex(): View
    {
        $schoolId = Auth::user()?->school_id;
        $school = $schoolId ? School::find($schoolId) : null;

        $reports = collect();
        if ($school) {
            $reports = ShortfallReport::query()
                ->where('school_id', $school->school_id)
                ->orderBy('report_date', 'desc')
                ->get();
        }

        return view('coordinator.shortfalls.index', [
            'school' => $school,
            'reports' => $reports,
            'active' => 'shortfalls',
        ]);
    }

    public function storeShortfall(): RedirectResponse
    {
        $schoolId = Auth::user()?->school_id;
        $school = $schoolId ? School::find($schoolId) : null;

        if (! $school) {
            return redirect()->route('coordinator.shortfalls.index')
                ->with('error', 'Your account is not linked to a school yet. Contact a Program Manager.');
        }

        $validated = request()->validate([
            'report_date' => ['required', 'date'],
            'required_pads' => ['required', 'integer', 'min:0'],
            'available_pads' => ['required', 'integer', 'min:0'],
            'government_pads_received' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:Draft,Submitted,Dispatched,Received'],
        ]);

        $totalAvailablePads = (int) $validated['available_pads'] + (int) $validated['government_pads_received'];
        $shortfall = max(0, (int) $validated['required_pads'] - $totalAvailablePads);

        ShortfallReport::query()->create([
            'school_id' => $school->school_id,
            'report_date' => $validated['report_date'],
            'required_pads' => $validated['required_pads'],
            'available_pads' => $validated['available_pads'],
            'government_pads_received' => $validated['government_pads_received'],
            'shortfall' => $shortfall,
            'status' => $validated['status'],
        ]);

        return redirect()->route('coordinator.shortfalls.index')
            ->with('success', 'Shortfall report submitted successfully.');
    }

    public function distributionsIndex(): View
    {
        $schoolId = Auth::user()?->school_id;
        $school = $schoolId ? School::find($schoolId) : null;

        $pendingDispatches = collect();
        $receivedDispatches = collect();

        if ($school) {
            $pendingDispatches = Distribution::query()
                ->where('school_id', $school->school_id)
                ->where('status', 'Dispatched')
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

        if ($distribution->status !== 'Dispatched') {
            return redirect()->route('coordinator.distributions.index')
                ->with('error', 'Only dispatched records can be confirmed.');
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
        });

        return redirect()->route('coordinator.distributions.index')
            ->with('success', 'Dispatch receipt confirmed successfully.');
    }
}