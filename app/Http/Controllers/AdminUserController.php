<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index()
    {
        $allUsers = User::query()
            ->with('school')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.users.index', compact('allUsers'));
    }

    public function updateCoordinatorStatus(Request $request, User $user)
    {
        if ($user->role !== 'Coordinator') {
            return redirect()->route('admin.users.index')->with('error', 'Only coordinator accounts can be approved or rejected here.');
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in(['Approved', 'Rejected'])],
        ]);

        $user->update([
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Coordinator status updated successfully.');
    }

    public function storeProgramManager(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'Program Manager',
            'status' => 'Approved',
            'school_id' => null,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Program Manager account created successfully.');
    }
}
