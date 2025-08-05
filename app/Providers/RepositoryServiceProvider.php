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
        $repositories = [
            TransactionRepositoryInterface::class => TransactionRepository::class,
            CategoryRepositoryInterface::class    => CategoryRepository::class,
            BudgetRepositoryInterface::class      => BudgetRepository::class,
            GoalRepositoryInterface::class        => GoalRepository::class,
            ReportRepositoryInterface::class      => ReportRepository::class,
            UserRepositoryInterface::class        => UserRepository::class,
        ];

        foreach ($repositories as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }
    }


    public function boot(): void
    {
        //
    }
}
