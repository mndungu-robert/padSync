<?php

namespace App\Http\Controllers;

class CoordinatorDashboardController extends Controller
{
    public function index()
    {
        return view('coordinator.dashboard');
    }
}
