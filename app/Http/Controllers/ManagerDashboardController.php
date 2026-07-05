<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\ShortfallReport;
use App\Models\User;
use App\Models\Donation;
use App\Models\Distribution;
use Illuminate\Support\Facades\DB;

class ManagerDashboardController extends Controller
{
    public function index()
    {
        $warehouse = Inventory::query()->first() ?? new Inventory([
            'quantity_available' => 0,
            'reorder_level' => 100,
        ]);

        $managerMetrics = [
            'available_stock' => $warehouse->quantity_available,
            'money_received' => (float) Donation::query()
                ->where('contribution_type', 'Donate Money')
                ->where('payment_status', 'Completed')
                ->sum('amount_kes'),
            'schools_count' => DB::table('schools')->count('school_id'),
            'active_shortfalls' => ShortfallReport::query()
                ->where(function ($query) {
                    $query->where('status', 'Submitted')
                        ->orWhere('status', 'Dispatched');
                })
                ->where('shortfall', '>', 0)
                ->count(),
            'pending_profiles' => User::query()->where('role', 'Coordinator')->where('status', 'Pending')->count(),
        ];

        $criticalNeeds = ShortfallReport::query()
            ->with('school')
            ->where(function ($query) {
                $query->where('status', 'Submitted')
                    ->orWhere('status', 'Dispatched');
            })
            ->where('shortfall', '>', 0)
            ->orderBy('shortfall', 'desc')
            ->orderByDesc('report_date')
            ->take(3)
            ->get();

        $schoolStats = DB::table('schools as schools')
            ->leftJoin('shortfall_reports as reports', function ($join) {
                $join->on('reports.school_id', '=', 'schools.school_id')
                    ->where(function ($query) {
                        $query->where('reports.status', 'Submitted')
                            ->orWhere('reports.status', 'Dispatched');
                    });
            })
            ->select([
                'schools.school_name as name',
                DB::raw('COALESCE(SUM(reports.government_pads_received), 0) as received'),
                DB::raw('COALESCE(SUM(reports.shortfall), 0) as shortfall'),
            ])
            ->groupBy('schools.school_id', 'schools.school_name')
            ->orderByDesc('shortfall')
            ->get()
            ->map(fn ($row) => [
                'name' => (string) $row->name,
                'received' => (int) $row->received,
                'shortfall' => (int) $row->shortfall,
            ])
            ->values()
            ->all();

        $dispatched = (int) Distribution::query()
            ->where(function ($query) {
                $query->where('status', 'Dispatched')
                    ->orWhere('status', 'Received');
            })
            ->sum('quantity_distributed');

        $inventoryStats = [
            'available' => (int) ($warehouse->quantity_available ?? 0),
            'allocated' => (int) ($warehouse->allocated_stock ?? 0),
            'dispatched' => $dispatched,
        ];

        return view('manager.dashboard', [
            'metrics' => $managerMetrics,
            'criticalNeeds' => $criticalNeeds,
            'schoolStats' => $schoolStats,
            'inventoryStats' => $inventoryStats,
            'active' => 'dashboard',
        ]);
    }
}
