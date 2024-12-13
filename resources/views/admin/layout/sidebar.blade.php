<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title">
                    <span>Main</span>
                </li>
                <li class="{{ Route::is('admin.dashboard') ? 'active' : '' }}"><a href="{{ route('admin.dashboard') }}"><i
                            class="la la-dashboard"></i><span>
                            Admin Dashboard</span></a></li>
                {{-- <li class="submenu">
                    <a href="#"><i class="la la-dashboard"></i> <span> Dashboard</span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li><a class="{{ Route::is('admin.dashboard') ? 'active' : '' }}"
                                href="{{ route('admin.dashboard') }}">Admin
                                Dashboard</a></li>
                    </ul>
                </li> --}}
                @can(['employees.index','departments.index','designations.index'])
                <li class="menu-title">
                    <span>Employee</span>
                </li>
                <li class="submenu">
                    <a href="#" class=""><i class="la la-user"></i> <span> Employees</span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        @can('employees.index') 
                        <li><a class="{{ Route::is('admin.employee.all') ? 'active' : '' }}"
                                href="{{ route('admin.employee.all') }}">Employees</a></li>
                        @endcan
                        @can('departments.index') 
                        <li><a class="{{ Route::is('admin.department.all') ? 'active' : '' }}"
                                href="{{ route('admin.department.all') }}">Departments</a></li>
                        @endcan
                        @can('designations.index')
                        <li><a class="{{ Route::is('admin.designation.all') ? 'active' : '' }}"
                                href="{{ route('admin.designation.all') }}">Designations</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan
                @can(['leaves.index', 'leave_types.index'])
                <li class="menu-title">
                    <span>HR</span>
                </li>
                <li class="submenu">

                    <a href="#" class=""><i class="la la-user"></i> <span> Leave</span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        @can('leaves.index')
                        <li><a class="{{ Route::is('admin.leave.application.view') ? 'active' : '' }}"
                                href="{{ route('admin.leave.application.view') }}">Leave Applications</a></li>
                        @endcan
                        @can('leave_types.index')
                        <li><a class="{{ Route::is('admin.leave.view') ? 'active' : '' }}"
                                href="{{ route('admin.leave.view') }}">Leave Category</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan
                @can('policies.index')
                <li class="submenu">
                    <a href="#" class=""><i class="la la-user"></i> <span> Policies</span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li><a class="{{ Route::is('admin.policy.view') ? 'active' : '' }}"
                                href="{{ route('admin.policy.view') }}">Policy Settings</a></li>
                    </ul>
                </li>
                @endcan
                @can('attendances.index')
                <li class="submenu">
                    <a href="#" class=""><i class="la la-user"></i> <span> Attendence</span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li><a class="{{ Route::is('admin.attendence.view') ? 'active' : '' }}"
                                href="{{ route('admin.attendence.view') }}?employee_id=&from_date={{ \Carbon\Carbon::now()->format('m/d/Y') }}&to_date=">View
                                Attendence</a></li>
                        <li><a class="{{ Route::is('admin.attendence.late.view') ? 'active' : '' }}"
                                href="{{ route('admin.attendence.late.view') }}?employee_id=&from_date={{ \Carbon\Carbon::now()->format('m/d/Y') }}&to_date=">Late
                                Comers</a></li>

                        <li><a class="{{ Route::is('device-logs.index') ? 'active' : '' }}"
                                href="{{ route('device-logs.index') }}"> Device Logs</a></li>
                    </ul>
                </li>
                @endcan
                @can('roles.index')
                <li class="submenu">
                    <a href="#" class=""><i class="la la-user"></i> <span> Role & Permissions</span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li><a class="{{ Route::is('roles.index') ? 'active' : '' }}"
                                href="{{ route('roles.index') }}">View Roles</a></li>

                    </ul>
                </li>
                @endcan
                {{-- <li class="submenu">
                    <a href="#"><i class="la la-money"></i> <span> Payroll</span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li><a class="{{ Route::is('admin.payroll.employee') || Route::is('admin.payroll.employee.view') ? 'active' : '' }}"
                                href="{{ route('admin.payroll.employee') }}">Employee Salary</a></li>
                    </ul>
                </li> --}}
                {{-- <li class="menu-title">
                    <span>Manager</span>
                </li>
                <li class="{{ Route::is('admin.manager.all') ? 'active' : '' }}"><a
                        href="{{ route('admin.manager.all') }}"><i class="la la-user"></i><span>
                            All Managers</span></a></li> --}}
            </ul>
        </div>
    </div>
</div>
<!-- /Sidebar -->
