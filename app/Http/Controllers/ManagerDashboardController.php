<?php

namespace App\Http\Controllers;

class ManagerDashboardController extends Controller
{
    public function index()
    {
        return view('manager.dashboard', [
            'active' => 'dashboard',
        ]);
    }
}
