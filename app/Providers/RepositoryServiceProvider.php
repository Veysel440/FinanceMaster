<?php

namespace App\Providers;

use App\Interface\BudgetRepositoryInterface;
use App\Interface\CategoryRepositoryInterface;
use App\Interface\GoalRepositoryInterface;
use App\Interface\ReportRepositoryInterface;
use App\Interface\TransactionRepositoryInterface;
use App\Interface\UserRepositoryInterface;
use App\Repositories\BudgetRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\GoalRepository;
use App\Repositories\ReportRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->app->bind(TransactionRepositoryInterface::class, TransactionRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(BudgetRepositoryInterface::class, BudgetRepository::class);
        $this->app->bind(GoalRepositoryInterface::class, GoalRepository::class);
        $this->app->bind(ReportRepositoryInterface::class, ReportRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }


    public function boot(): void
    {
        //
    }
}
