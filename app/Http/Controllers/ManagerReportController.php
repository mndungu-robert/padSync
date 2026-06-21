<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Donation;
use App\Models\Distribution;
use Illuminate\Support\Facades\DB;

class ManagerReportController extends Controller
{
    /**
     * Display the Reports Central Hub workspace panel.
     */
    public function index()
    {
        // Gather summary parameters to display as quick report scope stats
        $reportSummary = [
            'total_schools'       => DB::table('schools')->count('school_id'),
            'current_stock_pool'  => Inventory::query()->value('quantity_available') ?? 0,
            'cumulative_pledges'  => Donation::query()->sum('pad_count'),
            'total_dispatched'    => Distribution::query()->where('status', 'Dispatched')->sum('quantity_distributed'),
            'total_delivered'     => Distribution::query()->where('status', 'Received')->sum('quantity_distributed'),
        ];

        return view('manager.reports.index', [
            'summary' => $reportSummary,
            'active'  => 'reports' // Highlights the matching sidebar element
        ]);
    }

    /**
     * Placeholder method for handling file export logic triggers (CSV/PDF)
     */
    public function export(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:inventory,shortfalls,donations,distributions',
            'file_format' => 'required|in:csv,pdf',
        ]);

        // In a live system, you would compile the matching Eloquent data rows here
        // and return Excel::download() or PDF::loadView()->download().
        return back()->with('success', "The requested " . strtoupper($request->report_type) . " export dataset has been generated successfully as a " . strtoupper($request->file_format) . " file.");
    }
}
