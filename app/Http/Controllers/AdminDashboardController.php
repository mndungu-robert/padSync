<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Donation;
use App\Models\Enrollment;
use App\Models\Distribution;
use App\Models\ShortfallReport;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $metrics = [
            'program_managers' => User::query()->where('role', '=', 'Program Manager')->count(),
            'school_coordinators' => User::query()->where('role', '=', 'Coordinator')->count(),
            'schools_registered' => DB::table('schools')->count('school_id'),
            'pending_approvals' => User::query()->where('role', '=', 'Coordinator')->where('status', '=', 'Pending')->count(),
            'money_received' => (float) Donation::query()
                ->where('contribution_type', 'Donate Money')
                ->where('payment_status', 'Successful')
                ->sum('amount_kes'),
            'money_failed' => (float) Donation::query()
                ->where('contribution_type', 'Donate Money')
                ->where('payment_status', 'Failed')
                ->sum('amount_kes'),
        ];

        $recentUsers = User::query()->orderBy('created_at', 'desc')->take(3)->get();

        $recentLogs = DB::table('audit_logs')
            ->orderBy('created_at', 'desc')
            ->take(2)
            ->get();

        $monthBuckets = collect(range(5, 1))->map(fn (int $offset) => Carbon::now()->subMonths($offset));
        $monthBuckets = $monthBuckets->push(Carbon::now());
        $startDate = $monthBuckets->first()->copy()->startOfMonth();

        $requiredByMonth = Enrollment::query()
            ->select([
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as bucket"),
                DB::raw('SUM(girl_count * 2) as total'),
            ])
            ->whereDate('created_at', '>=', $startDate)
            ->groupBy('bucket')
            ->pluck('total', 'bucket');

        $coveredByMonth = Distribution::query()
            ->select([
                DB::raw("DATE_FORMAT(distribution_date, '%Y-%m') as bucket"),
                DB::raw('SUM(quantity_distributed) as total'),
            ])
            ->whereDate('distribution_date', '>=', $startDate)
            ->groupBy('bucket')
            ->pluck('total', 'bucket');

        $coverageStats = $monthBuckets
            ->map(function (Carbon $month) use ($requiredByMonth, $coveredByMonth) {
                $bucket = $month->format('Y-m');
                $required = (int) ($requiredByMonth[$bucket] ?? 0);
                $covered = (int) ($coveredByMonth[$bucket] ?? 0);

                return [
                    'month' => $month->format('M'),
                    'required' => $required,
                    'covered' => $covered,
                    'gap' => max(0, $required - $covered),
                ];
            })
            ->values()
            ->all();

        $schoolRiskRaw = DB::table('schools as schools')
            ->leftJoin('shortfall_reports as reports', function ($join) {
                $join->on('reports.school_id', '=', 'schools.school_id')
                    ->where(function ($query) {
                        $query->where('reports.status', 'Submitted')
                            ->orWhere('reports.status', 'Dispatched');
                    });
            })
            ->select([
                'schools.school_id',
                DB::raw('COALESCE(SUM(reports.shortfall), 0) as total_shortfall'),
            ])
            ->groupBy('schools.school_id')
            ->get();

        $riskSnapshot = [
            'on_track' => 0,
            'at_risk' => 0,
            'critical' => 0,
        ];

        foreach ($schoolRiskRaw as $row) {
            $shortfall = (int) $row->total_shortfall;

            if ($shortfall <= 0) {
                $riskSnapshot['on_track']++;
            } elseif ($shortfall <= 300) {
                $riskSnapshot['at_risk']++;
            } else {
                $riskSnapshot['critical']++;
            }
        }

        $allSchoolNames = DB::table('schools')
            ->orderBy('school_name')
            ->pluck('school_name')
            ->values();

        $dispatchRows = DB::table('distributions as distributions')
            ->join('schools as schools', 'schools.school_id', '=', 'distributions.school_id')
            ->select([
                DB::raw("DATE_FORMAT(distributions.distribution_date, '%Y-%m') as bucket"),
                'schools.school_name as school_name',
                DB::raw('SUM(distributions.quantity_distributed) as total'),
            ])
            ->whereDate('distributions.distribution_date', '>=', $startDate)
            ->groupBy('bucket', 'schools.school_name')
            ->get();

        $dispatchMonths = $monthBuckets
            ->map(fn (Carbon $month) => [
                'key' => $month->format('Y-m'),
                'label' => $month->format('M Y'),
            ])
            ->values();

        $schoolDispatchMonthly = [];
        foreach ($dispatchMonths as $month) {
            $monthKey = $month['key'];
            $totalsBySchool = $dispatchRows
                ->where('bucket', $monthKey)
                ->pluck('total', 'school_name');

            $schoolDispatchMonthly[$monthKey] = $allSchoolNames
                ->map(fn (string $schoolName) => (int) ($totalsBySchool[$schoolName] ?? 0))
                ->values()
                ->all();
        }

        return view('admin.dashboard', [
            'metrics' => $metrics,
            'recentUsers' => $recentUsers,
            'recentLogs' => $recentLogs,
            'coverageStats' => $coverageStats,
            'riskSnapshot' => $riskSnapshot,
            'dispatchMonths' => $dispatchMonths,
            'schoolDispatchSchools' => $allSchoolNames,
            'schoolDispatchMonthly' => $schoolDispatchMonthly,
        ]);
    }
}
