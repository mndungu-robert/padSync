<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Donation;
use App\Models\Distribution;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class ManagerReportController extends Controller
{
    /**
     * Display the Reports Central Hub workspace panel.
     */
    public function index()
    {
        // Gather summary parameters to display as quick report scope stats
        $reportSummary = [
            'total_schools'       => DB::table('schools')->count('school_id'),
            'current_stock_pool'  => Inventory::query()->value('quantity_available') ?? 0,
            'cumulative_pledges'  => Donation::query()->sum('pad_count'),
            'money_received'      => (float) Donation::query()
                ->where('contribution_type', 'Donate Money')
                ->where('payment_status', 'Successful')
                ->sum('amount_kes'),
            'total_dispatched'    => Distribution::query()->where('status', 'Dispatched')->sum('quantity_distributed'),
            'total_delivered'     => Distribution::query()->where('status', 'Received')->sum('quantity_distributed'),
        ];

        return view('manager.reports.index', [
            'summary' => $reportSummary,
            'active'  => 'reports' // Highlights the matching sidebar element
        ]);
    }

    /**
     * Handle report export logic. All reports are generated as PDF only.
     */
    public function export(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:inventory,shortfalls,donations,distributions,money',
        ]);

        $reportType = (string) $request->input('report_type');

        if ($reportType === 'money') {
            return $this->exportMoneyReport();
        }

        return $this->exportGenericReport($reportType);
    }

    private function exportMoneyReport()
    {
        $moneyRows = DB::table('donations')
            ->join('donors', 'donations.donor_id', '=', 'donors.id')
            ->select([
                'donations.donation_id',
                'donors.name as donor_name',
                'donors.email as donor_email',
                'donations.amount_kes',
                'donations.payment_status',
                'donations.payment_reference',
                'donations.paid_at',
                'donations.created_at',
            ])
            ->where('donations.contribution_type', '=', 'Donate Money')
            ->orderByDesc('donations.created_at')
            ->get();

        $summary = [
            'total_count' => $moneyRows->count(),
            'total_received' => (float) $moneyRows->where('payment_status', 'Successful')->sum('amount_kes'),
            'total_failed' => (float) $moneyRows->where('payment_status', 'Failed')->sum('amount_kes'),
        ];

        $pdf = Pdf::loadView('manager.reports.money-pdf', [
            'rows' => $moneyRows,
            'summary' => $summary,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('money-donation-ledger-'.now()->format('Ymd_His').'.pdf');

    }

    private function exportGenericReport(string $reportType)
    {
        $dataset = $this->buildGenericDataset($reportType);
        $rows = $dataset['rows'];
        $headers = $dataset['headers'];
        $title = $dataset['title'];

        $pdf = Pdf::loadView('manager.reports.generic-pdf', [
            'title' => $title,
            'rows' => $rows,
            'headers' => $headers,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download(strtolower(str_replace(' ', '-', $title)).'-'.now()->format('Ymd_His').'.pdf');
    }

    private function buildGenericDataset(string $reportType): array
    {
        if ($reportType === 'inventory') {
            $rows = DB::table('inventories')
                ->orderByDesc('updated_at')
                ->get()
                ->map(fn ($row) => [
                    'inventory_id' => (string) ($row->inventory_id ?? $row->id ?? ''),
                    'quantity_available' => (string) ($row->quantity_available ?? ''),
                    'allocated_stock' => (string) ($row->allocated_stock ?? ''),
                    'updated_at' => (string) ($row->updated_at ?? ''),
                ])
                ->values()
                ->all();

            return [
                'title' => 'Inventory Report',
                'headers' => ['inventory_id', 'quantity_available', 'allocated_stock', 'updated_at'],
                'rows' => $rows,
            ];
        }

        if ($reportType === 'shortfalls') {
            $rows = DB::table('shortfall_reports')
                ->leftJoin('schools', 'shortfall_reports.school_id', '=', 'schools.school_id')
                ->orderByDesc('shortfall_reports.created_at')
                ->select([
                    'shortfall_reports.report_id',
                    'schools.school_name',
                    'shortfall_reports.required_pads',
                    'shortfall_reports.available_pads',
                    'shortfall_reports.shortfall',
                    'shortfall_reports.status',
                    'shortfall_reports.created_at',
                ])
                ->get()
                ->map(fn ($row) => [
                    'report_id' => (string) ($row->report_id ?? ''),
                    'school_name' => (string) ($row->school_name ?? ''),
                    'required_pads' => (string) ($row->required_pads ?? ''),
                    'available_pads' => (string) ($row->available_pads ?? ''),
                    'shortfall' => (string) ($row->shortfall ?? ''),
                    'status' => (string) ($row->status ?? ''),
                    'created_at' => (string) ($row->created_at ?? ''),
                ])
                ->values()
                ->all();

            return [
                'title' => 'Shortfalls Report',
                'headers' => ['report_id', 'school_name', 'required_pads', 'available_pads', 'shortfall', 'status', 'created_at'],
                'rows' => $rows,
            ];
        }

        if ($reportType === 'donations') {
            $rows = DB::table('donations')
                ->leftJoin('donors', 'donations.donor_id', '=', 'donors.id')
                ->orderByDesc('donations.created_at')
                ->select([
                    'donations.donation_id',
                    'donors.name as donor_name',
                    'donations.pad_count',
                    'donations.payment_status',
                    'donations.pledge_date',
                    'donations.fulfillment_date',
                ])
                ->where('donations.contribution_type', '=', 'Donate Pads')
                ->get()
                ->map(fn ($row) => [
                    'donation_id' => (string) ($row->donation_id ?? ''),
                    'donor_name' => (string) ($row->donor_name ?? ''),
                    'pad_count' => (string) ($row->pad_count ?? ''),
                    'payment_status' => (string) ($row->payment_status ?? ''),
                    'pledge_date' => (string) ($row->pledge_date ?? ''),
                    'fulfillment_date' => (string) ($row->fulfillment_date ?? ''),
                ])
                ->values()
                ->all();

            return [
                'title' => 'Physical Donations Report',
                'headers' => ['donation_id', 'donor_name', 'pad_count', 'payment_status', 'pledge_date', 'fulfillment_date'],
                'rows' => $rows,
            ];
        }

        $rows = DB::table('distributions')
            ->leftJoin('schools', 'distributions.school_id', '=', 'schools.school_id')
            ->orderByDesc('distributions.created_at')
            ->select([
                'distributions.distribution_id',
                'schools.school_name',
                'distributions.quantity_distributed',
                'distributions.distribution_date',
                'distributions.status',
                'distributions.created_at',
            ])
            ->get()
            ->map(fn ($row) => [
                'distribution_id' => (string) ($row->distribution_id ?? ''),
                'school_name' => (string) ($row->school_name ?? ''),
                'quantity_distributed' => (string) ($row->quantity_distributed ?? ''),
                'distribution_date' => (string) ($row->distribution_date ?? ''),
                'status' => (string) ($row->status ?? ''),
                'created_at' => (string) ($row->created_at ?? ''),
            ])
            ->values()
            ->all();

        return [
            'title' => 'Distributions Report',
            'headers' => ['distribution_id', 'school_name', 'quantity_distributed', 'distribution_date', 'status', 'created_at'],
            'rows' => $rows,
        ];
    }
}
