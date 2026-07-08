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
            ->orderBy('created_at', 'asc')
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
        $mpesaTransactions = DB::table('donations')
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

        $dispatches = DB::table('distributions')
            ->join('schools', 'distributions.school_id', '=', 'schools.school_id')
            ->select([
                'distributions.distribution_id',
                'distributions.quantity_distributed',
                'distributions.distribution_date',
                'distributions.status',
                'distributions.updated_at',
                'schools.school_name',
            ])
            ->orderByDesc('distributions.updated_at')
            ->limit(20)
            ->get();

        $shortfallReports = DB::table('shortfall_reports')
            ->join('schools', 'shortfall_reports.school_id', '=', 'schools.school_id')
            ->select([
                'shortfall_reports.report_id',
                'shortfall_reports.report_date',
                'shortfall_reports.required_pads',
                'shortfall_reports.available_pads',
                'shortfall_reports.shortfall',
                'shortfall_reports.status',
                'shortfall_reports.created_at',
                'schools.school_name',
            ])
            ->orderByDesc('shortfall_reports.created_at')
            ->limit(20)
            ->get();

        $receiptConfirmations = DB::table('receipt_confirmations')
            ->join('distributions', 'receipt_confirmations.distribution_id', '=', 'distributions.distribution_id')
            ->join('schools', 'distributions.school_id', '=', 'schools.school_id')
            ->leftJoin('users', 'receipt_confirmations.coordinator_id', '=', 'users.id')
            ->select([
                'receipt_confirmations.confirmation_id',
                'receipt_confirmations.distribution_id',
                'receipt_confirmations.received_quantity',
                'receipt_confirmations.confirmation_date',
                'schools.school_name',
                'users.name as coordinator_name',
            ])
            ->orderByDesc('receipt_confirmations.confirmation_date')
            ->limit(20)
            ->get();

        $timeline = collect()
            ->merge($mpesaTransactions->map(function ($row) {
                return [
                    'happened_at' => $row->paid_at ?? $row->updated_at,
                    'category' => 'M-Pesa Transaction',
                    'reference' => '#'.$row->donation_id,
                    'details' => trim($row->donor_name.' paid KES '.number_format((float) $row->amount_kes, 2)),
                    'status' => $row->payment_status,
                ];
            }))
            ->merge($dispatches->map(function ($row) {
                return [
                    'happened_at' => $row->updated_at ?? $row->distribution_date,
                    'category' => 'Dispatch',
                    'reference' => '#'.$row->distribution_id,
                    'details' => trim($row->quantity_distributed.' pads sent to '.$row->school_name),
                    'status' => $row->status,
                ];
            }))
            ->merge($shortfallReports->map(function ($row) {
                return [
                    'happened_at' => $row->created_at ?? $row->report_date,
                    'category' => 'Shortfall Report',
                    'reference' => '#'.$row->report_id,
                    'details' => trim($row->school_name.' reported shortfall of '.number_format((int) $row->shortfall).' pads'),
                    'status' => $row->status,
                ];
            }))
            ->merge($receiptConfirmations->map(function ($row) {
                return [
                    'happened_at' => $row->confirmation_date,
                    'category' => 'Receipt Confirmation',
                    'reference' => '#'.$row->confirmation_id,
                    'details' => trim(($row->coordinator_name ?? 'Coordinator').' confirmed '.number_format((int) $row->received_quantity).' pads at '.$row->school_name),
                    'status' => 'Confirmed',
                ];
            }))
            ->filter(function ($row) {
                return !empty($row['happened_at']);
            })
            ->sortByDesc('happened_at')
            ->values()
            ->take(40);

        return view('admin.logs.index', [
            'timeline' => $timeline,
            'mpesaTransactions' => $mpesaTransactions,
            'dispatches' => $dispatches,
            'shortfallReports' => $shortfallReports,
            'receiptConfirmations' => $receiptConfirmations,
            'active' => 'logs' // Highlights the audit logs tab in the sidebar
        ]);
    }
}
