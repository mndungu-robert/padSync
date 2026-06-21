<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Route each authenticated role to its dedicated dashboard controller.
        if ($user->role === 'Admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'Program Manager') {
            return redirect()->route('manager.dashboard');
        }

        if ($user->role === 'Coordinator') {
            return redirect()->route('coordinator.dashboard');
        }

        abort(403, 'Invalid system role detected.');
    }
}
