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
                <li class="menu-title">
                    <span>Employee</span>
                </li>
                <li class="submenu">
                    <a href="#" class=""><i class="la la-user"></i> <span> Employees</span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li><a class="{{ Route::is('admin.employee.all') ? 'active' : '' }}"
                                href="{{ route('admin.employee.all') }}">Employees</a></li>
                        <li><a class="{{ Route::is('admin.department.all') ? 'active' : '' }}"
                                href="{{ route('admin.department.all') }}">Departments</a></li>
                        <li><a class="{{ Route::is('admin.designation.all') ? 'active' : '' }}"
                                href="{{ route('admin.designation.all') }}">Designations</a></li>
                    </ul>
                </li>
                <li class="menu-title">
                    <span>HR</span>
                </li>
                <li class="submenu">
                    <a href="#" class=""><i class="la la-user"></i> <span> Leave</span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li><a class="{{ Route::is('admin.leave.application.view') ? 'active' : '' }}"
                                href="{{ route('admin.leave.application.view') }}">Leave Applications</a></li>
                        <li><a class="{{ Route::is('admin.leave.view') ? 'active' : '' }}"
                                href="{{ route('admin.leave.view') }}">Leave Category</a></li>
                    </ul>
                </li>
                <li class="submenu">
                    <a href="#" class=""><i class="la la-user"></i> <span> Policies</span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li><a class="{{ Route::is('admin.policy.view') ? 'active' : '' }}"
                                href="{{ route('admin.policy.view') }}">Policy Settings</a></li>
                    </ul>
                </li>
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
                    </ul>
                </li>
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
