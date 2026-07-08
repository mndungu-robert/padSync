<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\ShortfallReport;
use App\Models\User;
use App\Models\Donation;
use App\Models\Distribution;
use App\Models\Enrollment;
use App\Models\School;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ManagerDashboardController extends Controller
{
    private const PACKETS_PER_GIRL_PER_MONTH = 2;

    public function index()
    {
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();

        $warehouse = Inventory::query()->first() ?? new Inventory([
            'quantity_available' => 0,
            'reorder_level' => 100,
        ]);

        $openShortfalls = ShortfallReport::query()
            ->with('school')
            ->where('status', 'Submitted')
            ->where('shortfall', '>', 0)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('distributions')
                    ->whereColumn('distributions.school_id', 'shortfall_reports.school_id')
                    ->whereRaw("DATE_FORMAT(distributions.distribution_date, '%Y-%m') = DATE_FORMAT(shortfall_reports.report_date, '%Y-%m')");
            })
            ->orderByDesc('report_date')
            ->orderByDesc('created_at')
            ->get()
            ->unique(function (ShortfallReport $report) {
                return $report->school_id.'-'.Carbon::parse($report->report_date)->format('Y-m');
            })
            ->values();

        $networkCoverage = $this->buildNetworkCoverage();

        $managerMetrics = [
            'available_stock' => $warehouse->quantity_available,
            'money_received' => (float) Donation::query()
                ->where('contribution_type', 'Donate Money')
                ->where('payment_status', 'Successful')
                ->sum('amount_kes'),
            'schools_count' => DB::table('schools')->count('school_id'),
            'active_shortfalls' => $openShortfalls->count(),
            'pending_profiles' => User::query()->where('role', 'Coordinator')->where('status', 'Pending')->count(),
            'required_pads' => $networkCoverage['required_pads'],
            'covered_pads' => $networkCoverage['covered_pads'],
            'remaining_pads' => $networkCoverage['remaining_pads'],
            'coverage_percent' => $networkCoverage['coverage_percent'],
        ];

        $criticalNeeds = $openShortfalls
            ->sortByDesc('shortfall')
            ->take(3)
            ->values();

        $schoolStats = collect($networkCoverage['per_school'])
            ->sortByDesc('remaining')
            ->map(fn (array $row) => [
                'name' => (string) $row['name'],
                'received' => (int) $row['covered'],
                'shortfall' => (int) $row['remaining'],
            ])
            ->values()
            ->all();

        $dispatchedThisMonth = (int) Distribution::query()
            ->where(function ($query) {
                $query->where('status', 'Dispatched')
                    ->orWhere('status', 'Received');
            })
            ->whereBetween('distribution_date', [$monthStart, $monthEnd])
            ->sum('quantity_distributed');

        $inventoryStats = [
            'available' => (int) ($warehouse->quantity_available ?? 0),
            'allocated' => (int) ($warehouse->allocated_stock ?? 0),
            'dispatched_this_month' => $dispatchedThisMonth,
        ];

        return view('manager.dashboard', [
            'metrics' => $managerMetrics,
            'criticalNeeds' => $criticalNeeds,
            'schoolStats' => $schoolStats,
            'inventoryStats' => $inventoryStats,
            'active' => 'dashboard',
        ]);
    }

    private function buildNetworkCoverage(): array
    {
        $now = Carbon::now();
        $currentMonth = $now->format('F');
        $currentAcademicYear = $now->format('Y');
        $monthStart = $now->copy()->startOfMonth()->toDateString();
        $monthEnd = $now->copy()->endOfMonth()->toDateString();

        $schools = School::query()
            ->select(['school_id', 'school_name'])
            ->orderBy('school_name')
            ->get();

        if ($schools->isEmpty()) {
            return [
                'required_pads' => 0,
                'covered_pads' => 0,
                'remaining_pads' => 0,
                'coverage_percent' => 0,
                'per_school' => [],
            ];
        }

        $schoolIds = $schools->pluck('school_id')->all();

        $enrollmentsBySchool = Enrollment::query()
            ->where(function ($query) use ($schoolIds) {
                foreach ($schoolIds as $index => $schoolId) {
                    if ($index === 0) {
                        $query->where('school_id', '=', $schoolId);
                    } else {
                        $query->orWhere('school_id', '=', $schoolId);
                    }
                }
            })
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('school_id');

        $shortfallsBySchool = ShortfallReport::query()
            ->where(function ($query) use ($schoolIds) {
                foreach ($schoolIds as $index => $schoolId) {
                    if ($index === 0) {
                        $query->where('school_id', '=', $schoolId);
                    } else {
                        $query->orWhere('school_id', '=', $schoolId);
                    }
                }
            })
            ->whereBetween('report_date', [$monthStart, $monthEnd])
            ->orderByDesc('report_date')
            ->get()
            ->groupBy('school_id');

        $receivedBySchool = Distribution::query()
            ->where(function ($query) use ($schoolIds) {
                foreach ($schoolIds as $index => $schoolId) {
                    if ($index === 0) {
                        $query->where('school_id', '=', $schoolId);
                    } else {
                        $query->orWhere('school_id', '=', $schoolId);
                    }
                }
            })
            ->where('status', 'Received')
            ->whereBetween('distribution_date', [$monthStart, $monthEnd])
            ->orderBy('distribution_date')
            ->get()
            ->groupBy('school_id');

        $perSchool = $schools->map(function (School $school) use (
            $enrollmentsBySchool,
            $shortfallsBySchool,
            $receivedBySchool,
            $currentMonth,
            $currentAcademicYear
        ) {
            $enrollmentRows = $enrollmentsBySchool->get($school->school_id, collect());

            $currentEnrollment = $enrollmentRows->first(function (Enrollment $enrollment) use ($currentMonth, $currentAcademicYear) {
                return $enrollment->month === $currentMonth
                    && (string) $enrollment->academic_year === (string) $currentAcademicYear;
            });

            $latestEnrollment = $enrollmentRows->first();
            $coverageEnrollment = $currentEnrollment ?? $latestEnrollment;

            $monthlyShortfall = $shortfallsBySchool->get($school->school_id, collect())->first();
            $receivedRows = $receivedBySchool->get($school->school_id, collect());

            if ($monthlyShortfall) {
                $requiredPads = (int) $monthlyShortfall->required_pads;
                $baseCovered = max(0, $requiredPads - (int) $monthlyShortfall->shortfall);
                $distributionCovered = (int) $receivedRows
                    ->filter(fn (Distribution $distribution) => $distribution->distribution_date >= $monthlyShortfall->report_date)
                    ->sum('quantity_distributed');
            } else {
                $requiredPads = $coverageEnrollment
                    ? ((int) $coverageEnrollment->girl_count * self::PACKETS_PER_GIRL_PER_MONTH)
                    : 0;

                $baseCovered = $coverageEnrollment
                    ? (int) $coverageEnrollment->government_pads_received
                    : 0;

                $distributionCovered = (int) $receivedRows->sum('quantity_distributed');
            }

            $coveredPads = min($requiredPads, $baseCovered + $distributionCovered);
            $remainingPads = max(0, $requiredPads - $coveredPads);

            return [
                'school_id' => (int) $school->school_id,
                'name' => (string) $school->school_name,
                'required' => $requiredPads,
                'covered' => $coveredPads,
                'remaining' => $remainingPads,
            ];
        })->values();

        $requiredPads = (int) $perSchool->sum('required');
        $coveredPads = (int) $perSchool->sum('covered');
        $remainingPads = (int) $perSchool->sum('remaining');

        return [
            'required_pads' => $requiredPads,
            'covered_pads' => $coveredPads,
            'remaining_pads' => $remainingPads,
            'coverage_percent' => $requiredPads > 0 ? (int) round(($coveredPads / $requiredPads) * 100) : 0,
            'per_school' => $perSchool->all(),
        ];
    }
}
