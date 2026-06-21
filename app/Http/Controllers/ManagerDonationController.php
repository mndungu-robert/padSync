<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Donation;

class ManagerDonationController extends Controller
{
    /**
     * Display the public donation pledges tracking registry.
     */
    public function index()
    {
        // Fetch public pledges joined with donor profiling details
        $pledges = Donation::select(
                        'donations.*', 
                        'donors.name as donor_name',
                        'donors.email as donor_email',
                        'donors.donor_type'
                     )
                     ->join('donors', 'donations.donor_id', '=', 'donors.id')
                     ->orderBy('donations.created_at', 'desc')
                     ->get();

        return view('manager.donations.index', [
            'pledges' => $pledges,
            'active'  => 'donations' // Highlights correct navigation bar link
        ]);
    }
}
