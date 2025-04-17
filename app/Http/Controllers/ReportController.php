<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReportService;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->middleware('auth');
        $this->reportService = $reportService;
    }

    public function index(Request $request)
    {
        $period = $request->input('period', 'monthly');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $summary = $this->reportService->getSummary($period, $startDate, $endDate);
        $categoryBreakdown = $this->reportService->getCategoryBreakdown($period, $startDate, $endDate);
        $trendData = $this->reportService->getTrendData($period, $startDate, $endDate);

        return view('reports.index', compact('summary', 'categoryBreakdown', 'trendData', 'period'));
    }
}
