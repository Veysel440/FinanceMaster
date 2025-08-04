<?php

namespace App\Http\Controllers\Api\Report;

use App\Http\Controllers\Controller;
use App\Http\Requests\Report\ReportFilterRequest;
use App\Http\Resources\Report\ReportSummaryResource;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {
        $this->middleware('auth:api');
    }

    public function summary(ReportFilterRequest $request): JsonResponse
    {
        $data = $this->reportService->getSummary(
            $request->input('period', 'monthly'),
            $request->input('start_date'),
            $request->input('end_date')
        );
        return response()->json([
            'success' => true,
            'data' => new ReportSummaryResource($data)
        ]);
    }

    public function categoryBreakdown(ReportFilterRequest $request): JsonResponse
    {
        $data = $this->reportService->getCategoryBreakdown(
            $request->input('period', 'monthly'),
            $request->input('start_date'),
            $request->input('end_date')
        );
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function trend(ReportFilterRequest $request): JsonResponse
    {
        $data = $this->reportService->getTrendData(
            $request->input('period', 'monthly'),
            $request->input('start_date'),
            $request->input('end_date')
        );
        return response()->json(['success' => true, 'data' => $data]);
    }
}
