<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDistributionRequest;
use App\Http\Requests\UpdateDistributionRequest;
use App\Models\Distribution;
use App\Models\Inventory;
use App\Models\ShortfallReport;
use Illuminate\Support\Facades\DB;

class DistributionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 1. Fetch pending school shortfalls reported by coordinators
        $pendingReports = ShortfallReport::with('school')
            ->where('status', 'Submitted')
            ->orderBy('shortfall', 'desc') // Rank schools with the highest shortfall first
            ->get();

        // 2. Fetch central available warehouse stock pool balance
        $warehouse = Inventory::query()->first() ?? new Inventory(['quantity_available' => 0]);
        $availableStock = $warehouse->quantity_available;

        // 3. Fetch recent dispatch history log records
        $dispatches = Distribution::with('school')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('manager.distributions.index', [
            'pendingReports' => $pendingReports,
            'availableStock' => $availableStock,
            'dispatches'     => $dispatches,
            'active'         => 'distributions' // Highlights sidebar element
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDistributionRequest $request)
    {
        $request->validate([
            'report_id' => 'required|exists:shortfall_reports,report_id',
        ]);

        $report = ShortfallReport::findOrFail($request->report_id);
        $warehouse = Inventory::query()->firstOrCreate([], ['quantity_available' => 0, 'allocated_stock' => 0]);

        // 1. Verify warehouse availability limits before allowing a dispatch action
        if ($warehouse->quantity_available < $report->shortfall) {
            return redirect()->route('manager.distributions.index')
                ->withErrors(['stock_error' => "Insufficient central warehouse stock. Required: {$report->shortfall} pads, Available: {$warehouse->quantity_available} pads."]);
        }

        DB::transaction(function () use ($report, $warehouse) {
            // 2. Create the distribution record trace entry log
            Distribution::create([
                'school_id'            => $report->school_id,
                'quantity_distributed' => $report->shortfall,
                'distribution_date'    => now()->format('Y-m-d'),
                'status'               => 'Dispatched',
            ]);

            // 3. Subtract from available warehouse stock balance, add to allocated buffer pool
            $warehouse->quantity_available -= $report->shortfall;
            $warehouse->allocated_stock += $report->shortfall;
            $warehouse->save();

            // 4. Update the coordinator's shortfall ticket report status block
            $report->update(['status' => 'Dispatched']);
        });

        return redirect()->route('manager.distributions.index')
            ->with('success', "Sanitary towels successfully dispatched to target school site.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Distribution $distribution)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Distribution $distribution)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDistributionRequest $request, Distribution $distribution)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Distribution $distribution)
    {
        //
    }
}
