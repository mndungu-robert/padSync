<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\ShortfallReport;
use App\Models\User;
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
            'reorder_threshold' => $warehouse->reorder_level,
            'schools_count' => DB::table('schools')->count('school_id'),
            'active_shortfalls' => ShortfallReport::query()->where('status', 'Submitted')->count(),
            'pending_profiles' => User::query()->where('role', 'Coordinator')->where('status', 'Pending')->count(),
        ];

        $criticalNeeds = ShortfallReport::query()
            ->with('school')
            ->where('status', 'Submitted')
            ->orderBy('shortfall', 'desc')
            ->take(3)
            ->get();

        return view('manager.dashboard', [
            'metrics' => $managerMetrics,
            'criticalNeeds' => $criticalNeeds,
            'active' => 'dashboard',
        ]);
    }
}
