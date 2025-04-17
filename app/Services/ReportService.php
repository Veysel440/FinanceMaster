<?php

namespace App\Services;

use App\Repositories\ReportRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class ReportService
{
    protected $reportRepository;

    public function __construct(ReportRepositoryInterface $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    public function getSummary(string $period, ?string $startDate = null, ?string $endDate = null): array
    {
        return $this->reportRepository->getSummary(Auth::id(), $period, $startDate, $endDate);
    }

    public function getCategoryBreakdown(string $period, ?string $startDate = null, ?string $endDate = null): array
    {
        $data = $this->reportRepository->getCategoryBreakdown(Auth::id(), $period, $startDate, $endDate);
        return [
            'labels' => array_column($data, 'category'),
            'data' => array_column($data, 'total'),
        ];
    }

    public function getTrendData(string $period, ?string $startDate = null, ?string $endDate = null): array
    {
        return $this->reportRepository->getTrendData(Auth::id(), $period, $startDate, $endDate);
    }
}
