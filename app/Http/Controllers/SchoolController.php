<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolRequest;
use App\Http\Requests\UpdateSchoolRequest;
use App\Models\Enrollment;
use App\Models\School;
use Illuminate\Support\Carbon;

class SchoolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currentMonth = Carbon::now()->format('F');

        $latestEnrollmentSubquery = Enrollment::query()
            ->select('school_id', 'girl_count')
            ->whereIn('enrollment_id', function ($query) {
                $query->selectRaw('MAX(enrollment_id)')
                    ->from('enrollments')
                    ->groupBy('school_id');
            });

        $currentMonthEnrollmentSubquery = Enrollment::query()
            ->select('school_id', 'girl_count')
            ->where('month', $currentMonth)
            ->whereIn('enrollment_id', function ($query) use ($currentMonth) {
                $query->selectRaw('MAX(enrollment_id)')
                    ->from('enrollments')
                    ->where('month', $currentMonth)
                    ->groupBy('school_id');
            });

        // Fetch schools with coordinator counts and latest submitted enrollment per school.
        $schools = School::query()
            ->leftJoinSub($latestEnrollmentSubquery, 'latest_enrollments', function ($join) {
                $join->on('latest_enrollments.school_id', '=', 'schools.school_id');
            })
            ->leftJoinSub($currentMonthEnrollmentSubquery, 'current_month_enrollments', function ($join) {
                $join->on('current_month_enrollments.school_id', '=', 'schools.school_id');
            })
            ->select('schools.*')
            ->selectRaw('COALESCE(current_month_enrollments.girl_count, 0) as enrollment')
            ->selectRaw('COALESCE(latest_enrollments.girl_count, schools.enrollment) as latest_enrollment')
            ->withCount('coordinators')
            ->orderBy('school_name', 'asc')
            ->get();

        return view('manager.schools.index', [
            'schools' => $schools,
            'currentMonth' => $currentMonth,
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
