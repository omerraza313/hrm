<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Policies\DepartmentPolicy;
use App\Policies\DesignationPolicy;
use App\Policies\EmployeePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Department::class => DepartmentPolicy::class,
        Designation::class => DesignationPolicy::class,
        Employee::class => EmployeePolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
