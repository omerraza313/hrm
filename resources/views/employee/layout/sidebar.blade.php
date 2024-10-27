<!-- Sidebar -->
<?php
$route = request()->route();

//dd( ($route->uri()=="employee/manager_view"? "yes":"no") );

//$class_active = ( Route::currentRouteName() == ' employee.employee.manager.attendence.regular.view') ? 'active':'';
//dd(Route::currentRouteName());
?>
<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                {{-- <li class="menu-title">
                    <span>Main</span>
                </li> --}}
                <li class="{{ Route::is('employee.dashboard') ? 'active' : '' }}"><a
                        href="{{ route('employee.dashboard') }}"><i class="la la-dashboard"></i><span>
                            Dashboard</span></a></li>
                {{-- <li class="submenu">
                    <a href="#"><i class="la la-dashboard"></i> <span> Dashboard</span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li><a class="{{ Route::is('employee.dashboard') ? 'active' : '' }}"
                                href="{{ route('employee.dashboard') }}">Employee
                                Dashboard</a></li>
                    </ul>
                </li> --}}
                <li class="submenu">
                    <a href="#" class=""><i class="la la-user"></i> <span> Applications</span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li><a class="{{ Route::is('employee.leave.application.view') ? 'active' : '' }}"
                                href="{{ route('employee.leave.application.view') }}">Leave Applications</a></li>
                        @if (auth()->user()->hasRole(\App\Enums\RolesEnum::Manager->value))
                            <li><a class="{{ Route::is('employee.other.leave.application.view') ? 'active' : '' }}"
                                    href="{{ route('employee.other.leave.application.view') }}">Employee Leave
                                    Applications</a>
                            </li>
                        @endif
                    </ul>
                </li>

                {{-- Attendence --}}
                <li class="{{ Route::is('employee.attendence.view') ? 'active' : '' }}">
                    <a href="{{ route('employee.attendence.view') }}"><i class="la la-table"></i><span>Attendence</span></a></li>
                </li>
                @if (auth()->user()->hasRole(\App\Enums\RolesEnum::Manager->value))
                    <li class="submenu">
                        <a href="#" class="{{($route->uri()=="employee/manager_view"? "active":"")}}"><i class="la la-user"></i> <span> Employee Attendence</span> <span
                        class="menu-arrow"></span></a>
                        <ul style="display: none;">
                            <li>
                                <a class="{{($route->uri()=="employee/attendence/manager_view"? "active":"")}}"
                                   href="{{ route('employee.attendence.view') }}/manager_view?employee_id=&from_date={{ \Carbon\Carbon::now()->format('m/d/Y') }}&to_date=">
                                    View Attendence</a>
                            </li>
                            <li>
                                <a class="{{($route->uri()=="employee/attendence/late_view"? "active":"")}}"
                                   href="{{ route('employee.attendence.view') }}/late_view?employee_id=&from_date={{ \Carbon\Carbon::now()->format('m/d/Y') }}&to_date=">
                                    Late Comers</a>
                            </li>
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>
<!-- /Sidebar -->
