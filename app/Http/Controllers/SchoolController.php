<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolRequest;
use App\Http\Requests\UpdateSchoolRequest;
use App\Models\School;

class SchoolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch schools along with a count of their linked coordinators
        $schools = School::withCount('coordinators')
            ->orderBy('school_name', 'asc')
            ->get();

        return view('manager.schools.index', [
            'schools' => $schools,
            'active' => 'schools',
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
    public function store(StoreSchoolRequest $request)
    {
        $request->validate([
            'school_name'     => 'required|string|max:255|unique:schools,school_name',
            'school_location' => 'required|string|max:255',
            'enrollment'      => 'required|integer|min:0',
        ]);

        School::create([
            'school_name'     => $request->school_name,
            'school_location' => $request->school_location,
            'enrollment'      => $request->enrollment,
        ]);

        return redirect()->route('manager.schools.index')
            ->with('success', 'Physical school site successfully registered in ' . config('app.name') . '.');
    }

    /**
     * Display the specified resource.
     */
    public function show(School $school)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(School $school)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSchoolRequest $request, School $school)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(School $school)
    {
        //
    }
}
