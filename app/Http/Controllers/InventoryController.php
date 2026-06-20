<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInventoryRequest;
use App\Http\Requests\UpdateInventoryRequest;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 1. Fetch centralized warehouse metrics from the inventories table
        // We calculate total in stock as (quantity_available + allocated_stock)
        $warehouse = Inventory::query()->first() ?? new Inventory([
            'quantity_available' => 0,
            'allocated_stock' => 0
        ]);

        $metrics = [
            'total_stock' => $warehouse->quantity_available + $warehouse->allocated_stock,
            'allocated'   => $warehouse->allocated_stock,
            'available'   => $warehouse->quantity_available,
        ];

        // 2. Fetch the collection of historic donations joined with donor details
        $donations = Donation::select(
                        'donations.*', 
                        'donors.name as donor_name',
                        'donors.organization_name'
                     )
                     ->join('donors', 'donations.donor_id', '=', 'donors.id')
                     ->orderBy('donations.created_at', 'desc')
                     ->get();

        return view('manager.inventory.index', compact('metrics', 'donations'));
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
    public function store(StoreInventoryRequest $request)
    {
         $request->validate([
            'donor_name'     => 'required|string|max:255',
            'quantity_pads'  => 'required|integer|min:1',
            'date_received'  => 'required|date',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Find or create an individual donor entry to keep records clean
            $donor = Donor::firstOrCreate(
                ['name' => $request->donor_name],
                [
                    'email' => strtolower(str_replace(' ', '', $request->donor_name)) . '@example.com',
                    'donor_type' => 'Individual',
                    'pad_count' => 0
                ]
            );

            // 2. Log the raw donation record entry linked back to the donor
            Donation::create([
                'donor_id' => $donor->id,
                'pad_count' => $request->quantity_pads,
                'pledge_status' => 'Fulfilled',
                'pledge_date' => $request->date_received,
                'fulfillment_date' => $request->date_received,
            ]);

            // 3. Update the centralized inventory warehouse available stock pool balance
            $inventory = Inventory::firstOrCreate([], [
                'quantity_available' => 0, 
                'allocated_stock' => 0, 
                'reorder_level' => 100
            ]);
            
            $inventory->increment('quantity_available', $request->quantity_pads);
            $donor->increment('pad_count', $request->quantity_pads);
        });

        return redirect()->route('manager.inventory.index')
            ->with('success', 'New donation batch logged and central inventory updated successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Inventory $inventory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inventory $inventory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInventoryRequest $request, Inventory $inventory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventory $inventory)
    {
        //
    }
}
