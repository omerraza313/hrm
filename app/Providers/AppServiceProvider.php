<?php

namespace App\Providers;

use App\Rules\DateRange;
use App\Services\ProfileService;
use App\Services\EmployeeService;
use App\Services\DepartmentService;
use App\Services\DesignationService;
use App\Services\EmployeeBankService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Department Singleton
        $this->app->singleton(DepartmentService::class, function ($app) {
            return new DepartmentService();
        });

        $this->app->singleton(DesignationService::class, function ($app) {
            return new DesignationService();
        });

        $this->app->singleton(EmployeeService::class, function ($app) {
            return new EmployeeService();
        });

        $this->app->singleton(ProfileService::class, function ($app) {
            return new ProfileService();
        });
        $this->app->singleton(EmployeeBankService::class, function ($app) {
            return new EmployeeBankService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Validator::extend('date_range', DateRange::class);
    }
}
