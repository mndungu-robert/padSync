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
            'schools_count' => (int) ($networkCoverage['schools_count'] ?? 0),
            'active_shortfalls' => $openShortfalls->count(),
            'pending_profiles' => User::query()->where('role', 'Coordinator')->where('status', 'Pending')->count(),
            'girls_count' => (int) ($networkCoverage['girls_count'] ?? 0),
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
            ->groupBy(fn (array $row) => (string) $row['name'])
            ->map(fn ($rows, string $schoolName) => [
                'name' => $schoolName,
                'received' => (int) $rows->sum('covered'),
                'shortfall' => (int) $rows->sum('remaining'),
            ])
            ->sortByDesc('shortfall')
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
                'schools_count' => 0,
                'girls_count' => 0,
                'required_pads' => 0,
                'covered_pads' => 0,
                'remaining_pads' => 0,
                'coverage_percent' => 0,
                'per_school' => [],
            ];
        }

        $schoolsByName = $schools
            ->groupBy(fn (School $school) => trim((string) $school->school_name));

        $allSchoolIds = $schools->pluck('school_id')->all();

        $enrollmentRows = Enrollment::query()
            ->where(function ($query) use ($allSchoolIds) {
                foreach ($allSchoolIds as $index => $schoolId) {
                    if ($index === 0) {
                        $query->where('school_id', '=', $schoolId);
                    } else {
                        $query->orWhere('school_id', '=', $schoolId);
                    }
                }
            })
            ->orderByDesc('created_at')
            ->get();

        $shortfallRows = ShortfallReport::query()
            ->where(function ($query) use ($allSchoolIds) {
                foreach ($allSchoolIds as $index => $schoolId) {
                    if ($index === 0) {
                        $query->where('school_id', '=', $schoolId);
                    } else {
                        $query->orWhere('school_id', '=', $schoolId);
                    }
                }
            })
            ->whereBetween('report_date', [$monthStart, $monthEnd])
            ->orderByDesc('report_date')
            ->get();

        $receivedRows = Distribution::query()
            ->where(function ($query) use ($allSchoolIds) {
                foreach ($allSchoolIds as $index => $schoolId) {
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
            ->get();

        $perSchool = $schoolsByName->map(function ($schoolRows, string $schoolName) use (
            $enrollmentRows,
            $shortfallRows,
            $receivedRows,
            $currentMonth,
            $currentAcademicYear
        ) {
            $schoolIdList = $schoolRows->pluck('school_id')->all();

            $schoolEnrollmentRows = $enrollmentRows
                ->filter(fn (Enrollment $enrollment) => in_array($enrollment->school_id, $schoolIdList, true))
                ->values();

            $monthlyShortfall = $shortfallRows
                ->first(fn (ShortfallReport $shortfall) => in_array($shortfall->school_id, $schoolIdList, true));

            $schoolReceivedRows = $receivedRows
                ->filter(fn (Distribution $distribution) => in_array($distribution->school_id, $schoolIdList, true));

            $girlsCount = (int) $schoolEnrollmentRows->sum('girl_count');

            $requiredPads = $girlsCount * self::PACKETS_PER_GIRL_PER_MONTH;

            if ($monthlyShortfall) {
                $baseCovered = max(0, $requiredPads - (int) $monthlyShortfall->shortfall);
                $distributionCovered = (int) $schoolReceivedRows
                    ->filter(fn (Distribution $distribution) => $distribution->distribution_date >= $monthlyShortfall->report_date)
                    ->sum('quantity_distributed');
            } else {
                $baseCovered = (int) $schoolEnrollmentRows->sum('government_pads_received');

                $distributionCovered = (int) $schoolReceivedRows->sum('quantity_distributed');
            }

            $coveredPads = min($requiredPads, $baseCovered + $distributionCovered);
            $remainingPads = max(0, $requiredPads - $coveredPads);

            return [
                'school_id' => (int) ($schoolIdList[0] ?? 0),
                'name' => $schoolName,
                'girls' => $girlsCount,
                'required' => $requiredPads,
                'covered' => $coveredPads,
                'remaining' => $remainingPads,
            ];
        })->values();

        $girlsCount = (int) $perSchool->sum('girls');
        $requiredPads = (int) $perSchool->sum('required');
        $coveredPads = (int) $perSchool->sum('covered');
        $remainingPads = (int) $perSchool->sum('remaining');

        return [
            'schools_count' => (int) $perSchool->count(),
            'girls_count' => $girlsCount,
            'required_pads' => $requiredPads,
            'covered_pads' => $coveredPads,
            'remaining_pads' => $remainingPads,
            'coverage_percent' => $requiredPads > 0 ? (int) round(($coveredPads / $requiredPads) * 100) : 0,
            'per_school' => $perSchool->all(),
        ];
    }
}
