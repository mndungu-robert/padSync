<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ManagerCoordinatorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all coordinators alongside their selected school site assignment info
        $coordinators = User::with('school')
            ->where('role', 'Coordinator')
            ->orderByRaw("FIELD(status, 'Pending', 'Approved', 'Rejected')")
            ->orderBy('created_at', 'asc')
            ->get();

        return view('manager.coordinators.index', [
            'coordinators' => $coordinators,
            'active'       => 'coordinators' // Highlights the matching sidebar element
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|in:Approved,Rejected'
        ]);

        $user = User::query()
            ->where('role', 'Coordinator')
            ->findOrFail($id);
        $user->update([
            'status' => $request->status
        ]);

        $message = $request->status === 'Approved' 
            ? "Account for {$user->name} has been successfully activated." 
            : "Registration for {$user->name} has been declined.";

        return redirect()->route('manager.coordinators.index')->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
