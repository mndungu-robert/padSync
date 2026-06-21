<?php

namespace App\Http\Controllers;

use App\Models\User;
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
