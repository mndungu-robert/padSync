<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\ShortfallReport;
use App\Models\User;
use App\Models\Donation;
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

        return view('manager.dashboard', [
            'metrics' => $managerMetrics,
            'criticalNeeds' => $criticalNeeds,
            'active' => 'dashboard',
        ]);
    }
}
