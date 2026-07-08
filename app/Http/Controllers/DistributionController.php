<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDistributionRequest;
use App\Http\Requests\UpdateDistributionRequest;
use App\Models\Distribution;
use App\Models\Inventory;
use App\Models\ShortfallReport;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DistributionController extends Controller
{
    private const DISPATCH_BUFFER_PACKETS = 20;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 1. Fetch pending school shortfalls reported by coordinators
        $pendingReports = ShortfallReport::with('school')
            ->where('status', 'Submitted')
            ->where('shortfall', '>', 0)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('distributions')
                    ->whereColumn('distributions.school_id', 'shortfall_reports.school_id')
                    ->whereRaw("DATE_FORMAT(distributions.distribution_date, '%Y-%m') = DATE_FORMAT(shortfall_reports.report_date, '%Y-%m')");
            })
            ->orderByDesc('report_date')
            ->orderByDesc('created_at')
            ->get()
            ->unique(function (ShortfallReport $report) {
                return $report->school_id.'-'.Carbon::parse($report->report_date)->format('Y-m');
            })
            ->sortByDesc('shortfall') // Rank schools with the highest shortfall first
            ->values();

        // 2. Fetch central available warehouse stock pool balance
        $warehouse = Inventory::query()->first() ?? new Inventory(['quantity_available' => 0]);
        $availableStock = $warehouse->quantity_available;

        $pendingReports = $pendingReports->map(function (ShortfallReport $report) {
            $report->dispatch_quantity = (int) $report->shortfall + self::DISPATCH_BUFFER_PACKETS;

            return $report;
        });

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
        $dispatchQuantity = (int) $report->shortfall + self::DISPATCH_BUFFER_PACKETS;

        if ($report->status !== 'Submitted') {
            return redirect()->route('manager.distributions.index')
                ->withErrors(['stock_error' => 'This shortfall report is not open for dispatch. It may already be dispatched or closed.']);
        }

        $reportMonth = Carbon::parse($report->report_date);
        $monthStart = $reportMonth->copy()->startOfMonth()->toDateString();
        $monthEnd = $reportMonth->copy()->endOfMonth()->toDateString();

        $existingMonthlyDispatch = Distribution::query()
            ->where('school_id', $report->school_id)
            ->whereBetween('distribution_date', [$monthStart, $monthEnd])
            ->first();

        if ($existingMonthlyDispatch) {
            return redirect()->route('manager.distributions.index')
                ->withErrors(['stock_error' => 'Only one distribution per school per month is allowed. This school already has a dispatch recorded for that month.']);
        }

        // 1. Verify warehouse availability limits before allowing a dispatch action
        if ($warehouse->quantity_available < $dispatchQuantity) {
            return redirect()->route('manager.distributions.index')
                ->withErrors(['stock_error' => "Insufficient central warehouse stock. Required: {$dispatchQuantity} packets (shortfall + 20 buffer), Available: {$warehouse->quantity_available} packets."]);
        }

        DB::transaction(function () use ($report, $warehouse, $dispatchQuantity) {
            // 2. Create the distribution record trace entry log
            Distribution::create([
                'school_id'            => $report->school_id,
                'quantity_distributed' => $dispatchQuantity,
                'distribution_date'    => now()->format('Y-m-d'),
                'status'               => 'Dispatched',
            ]);

            // 3. Subtract from available warehouse stock balance, add to allocated buffer pool
            $warehouse->quantity_available -= $dispatchQuantity;
            $warehouse->allocated_stock += $dispatchQuantity;
            $warehouse->save();

            // 4. Update the coordinator's shortfall ticket report status block
            $report->update(['status' => 'Dispatched']);
        });

        return redirect()->route('manager.distributions.index')
            ->with('success', "Sanitary towel packets successfully dispatched to target school site with a +20 buffer.");
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
