<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use Illuminate\Http\Request;

class DonorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $donors = Donor::query()->orderBy('created_at', 'desc')->get();

        return view('admin.donors.index', compact('donors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.donors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:donors,email',
            'phone' => 'nullable|string|max:20',
            'donor_type' => 'required|in:Individual,Organization',
            'organization_name' => 'nullable|required_if:donor_type,Organization|string|max:255',
        ]);

        Donor::create($request->all());

        return redirect()->route('admin.donors.index')->with('success', 'Donor added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Donor $donor)
    {
        return view('admin.donors.show', compact('donor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Donor $donor)
    {
        return view('admin.donors.edit', compact('donor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Donor $donor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:donors,email,'.$donor->id,
            'phone' => 'nullable|string|max:20',
            'donor_type' => 'required|in:Individual,Organization',
            'organization_name' => 'nullable|required_if:donor_type,Organization|string|max:255',
        ]);

        $donor->update($request->all());

        return redirect()->route('admin.donors.index')
            ->with('success', 'Donor updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Donor $donor)
    {
        Donor::destroy($donor->id);

        return redirect()->route('admin.donors.index')->with('success', 'Donor deleted successfully.');
    }
}
