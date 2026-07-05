<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
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

    public function destroy(User $user)
    {
        if (Auth::id() === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account.');
        }

        try {
            $name = $user->name;
            User::query()->whereKey($user->id)->delete();

            return redirect()->route('admin.users.index')->with('success', "User account for {$name} deleted successfully.");
        } catch (QueryException $exception) {
            return redirect()->route('admin.users.index')->with('error', 'This user cannot be deleted because related records exist in the system.');
        }
    }

    public function resetPassword(Request $request, User $user)
    {
        if (Auth::id() === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'Use your profile page to change your own password.');
        }

        $validated = $request->validateWithBag('resetPassword', [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'reset_user_id' => ['nullable', 'integer'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Password reset successfully for '.$user->name.'.');
    }

    /**
     * Display the paginated system audit trails logs.
     */
    public function indexLogs()
    {
        // Pull logs, joining user information if necessary, or extracting raw traces
        $logs = DB::table('audit_logs')
            ->orderBy('created_at', 'desc')
            ->paginate(15); // Paginated to keep loading fast

        $paymentActivity = DB::table('donations')
            ->join('donors', 'donations.donor_id', '=', 'donors.id')
            ->select([
                'donations.donation_id',
                'donations.amount_kes',
                'donations.payment_status',
                'donations.payment_reference',
                'donations.paid_at',
                'donations.updated_at',
                'donors.name as donor_name',
                'donors.email as donor_email',
            ])
            ->where('donations.contribution_type', 'Donate Money')
            ->orderByDesc('donations.updated_at')
            ->limit(20)
            ->get();

        return view('admin.logs.index', [
            'logs'   => $logs,
            'paymentActivity' => $paymentActivity,
            'active' => 'logs' // Highlights the audit logs tab in the sidebar
        ]);
    }
}
