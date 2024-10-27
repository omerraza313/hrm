@include('employee.layout.head')
<!-- Main Wrapper -->
<div class="main-wrapper">
    {{-- Header --}}
    @include('employee.layout.header')

    {{-- Side Nav Bar --}}
    @include('employee.layout.sidebar')

    <!-- Page Wrapper -->
    <div class="page-wrapper">

        <!-- Page Content -->
        <div class="content container-fluid">
            <x-alert-notification />
            @yield('main-container')
        </div>
    </div>
</div>

@include('employee.layout.foot')

@include('employee.layout.footer')
