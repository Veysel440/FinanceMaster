<?php

namespace App\Repositories;

use App\Interface\ReportRepositoryInterface;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class ReportRepository implements ReportRepositoryInterface
{
    public function getSummary(int $userId, string $period, ?string $startDate = null, ?string $endDate = null): array
    {
        $query = Transaction::where('user_id', $userId);

        $this->applyDateFilter($query, $period, $startDate, $endDate);

        $income = (clone $query)->where('type', 'income')->sum('amount');
        $expense = (clone $query)->where('type', 'expense')->sum('amount');

        return [
            'income'  => $income,
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
                'total' => (float) $item->total,
            ];
        })->toArray();
    }

    public function getTrendData(int $userId, string $period, ?string $startDate = null, ?string $endDate = null): array
    {
        $yearMonth = $this->yearMonthExpression('date');

        $query = Transaction::where('user_id', $userId)
            ->select(
                DB::raw("{$yearMonth} as period"),
                DB::raw("SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income"),
                DB::raw("SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense")
            );

        $this->applyDateFilter($query, $period, $startDate, $endDate);

        $results = $query->groupBy('period')->orderBy('period')->get();

        return [
            'labels' => $results->pluck('period')->toArray(),
            'income' => $results->pluck('income')->map(fn($v) => (float)$v)->toArray(),
            'expense' => $results->pluck('expense')->map(fn($v) => (float)$v)->toArray(),
        ];
    }

    /**
     * Build a year-month grouping SQL expression for the configured DB driver.
     * Falls back to MySQL-style DATE_FORMAT for unknown drivers.
     */
    private function yearMonthExpression(string $column = 'date'): string
    {
        return match (config('database.default')) {
            'mysql', 'mariadb' => "DATE_FORMAT({$column}, '%Y-%m')",
            'sqlite'           => "strftime('%Y-%m', {$column})",
            'pgsql'            => "TO_CHAR({$column}, 'YYYY-MM')",
            default            => "DATE_FORMAT({$column}, '%Y-%m')",
        };
    }

    protected function applyDateFilter($query, string $period, ?string $startDate, ?string $endDate): void
    {
        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        } elseif ($period === 'monthly') {
            $query->whereMonth('date', now()->month)->whereYear('date', now()->year);
        } elseif ($period === 'yearly') {
            $query->whereYear('date', now()->year);
        } elseif ($period === 'weekly') {
            $query->whereBetween('date', [
                now()->startOfWeek(), now()->endOfWeek()
            ]);
        } elseif ($period === 'daily') {
            $query->whereDate('date', now());
        }
    }
}
