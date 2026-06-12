<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Branch your layout response cleanly based on the database column string
        return match($user->role) {
            'Admin'           => view('dashboards.admin'),
            'Program Manager' => view('dashboards.manager'),
            'Coordinator'     => view('dashboards.coordinator'),
            default           => redirect('/'),
        };
    }
}
