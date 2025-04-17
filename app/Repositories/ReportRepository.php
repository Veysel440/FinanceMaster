<?php

namespace App\Repositories;

use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportRepository implements ReportRepositoryInterface
{
    public function getSummary(int $userId, string $period, ?string $startDate = null, ?string $endDate = null): array
    {
        $query = Transaction::where('user_id', $userId);

        $this->applyDateFilter($query, $period, $startDate, $endDate);

        $income = $query->clone()->where('type', 'income')->sum('amount');
        $expense = $query->clone()->where('type', 'expense')->sum('amount');

        return [
            'income' => $income,
            'expense' => $expense,
            'balance' => $income - $expense,
        ];
    }

    public function getCategoryBreakdown(int $userId, string $period, ?string $startDate = null, ?string $endDate = null): array
    {
        $query = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(transactions.amount) as total'));

        $this->applyDateFilter($query, $period, $startDate, $endDate);

        return $query->groupBy('categories.name')->get()->map(function ($item) {
            return [
                'category' => $item->name,
                'total' => $item->total,
            ];
        })->toArray();
    }

    public function getTrendData(int $userId, string $period, ?string $startDate = null, ?string $endDate = null): array
    {
        $query = Transaction::where('user_id', $userId)
            ->select(
                DB::raw("DATE_FORMAT(date, '%Y-%m') as month"),
                DB::raw("SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income"),
                DB::raw("SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense")
            );

        $this->applyDateFilter($query, $period, $startDate, $endDate);

        $results = $query->groupBy('month')->orderBy('month')->get();

        return [
            'labels' => $results->pluck('month')->toArray(),
            'income' => $results->pluck('income')->toArray(),
            'expense' => $results->pluck('expense')->toArray(),
        ];
    }

    protected function applyDateFilter($query, string $period, ?string $startDate, ?string $endDate): void
    {
        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        } elseif ($period === 'monthly') {
            $query->whereMonth('date', now()->month)->whereYear('date', now()->year);
        } elseif ($period === 'yearly') {
            $query->whereYear('date', now()->year);
        }
    }
}
