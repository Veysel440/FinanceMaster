<?php

namespace App\Repositories;

interface ReportRepositoryInterface
{
    public function getSummary(int $userId, string $period, ?string $startDate = null, ?string $endDate = null): array;

    public function getCategoryBreakdown(int $userId, string $period, ?string $startDate = null, ?string $endDate = null): array;

    public function getTrendData(int $userId, string $period, ?string $startDate = null, ?string $endDate = null): array;
}
