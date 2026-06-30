<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Donation;
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
                ->where('payment_status', 'Completed')
                ->sum('amount_kes'),
            'money_pending' => (float) Donation::query()
                ->where('contribution_type', 'Donate Money')
                ->where('payment_status', 'Pending')
                ->sum('amount_kes'),
        ];

        $recentUsers = User::query()->orderBy('created_at', 'desc')->take(3)->get();

        $recentLogs = DB::table('audit_logs')
            ->orderBy('created_at', 'desc')
            ->take(2)
            ->get();

        return view('admin.dashboard', [
            'metrics' => $metrics,
            'recentUsers' => $recentUsers,
            'recentLogs' => $recentLogs,
        ]);
    }
}
