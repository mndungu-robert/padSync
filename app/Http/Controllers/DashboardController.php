<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Force the Admin role to load the premium admin dashboard view
        if ($user->role === 'Admin') {
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

            // FIX: Pointing explicitly to your admin dashboard folder view
            return view('admin.dashboard', compact('metrics', 'recentUsers', 'recentLogs'));
        }

        // 2. Program Manager routing safety
        if ($user->role === 'Program Manager') {
            return redirect()->route('manager.dashboard');
        }

        // 3. Coordinator routing safety
        if ($user->role === 'Coordinator') {
            return view('coordinator.dashboard');
        }

        abort(403, 'Invalid system role detected.');
    }
}
