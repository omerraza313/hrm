@include('admin.layout.head')
<!-- Main Wrapper -->
<div class="main-wrapper">
    {{-- Header --}}
    @include('admin.layout.header')

    {{-- Side Nav Bar --}}
    @include('admin.layout.sidebar')

    <!-- Page Wrapper -->
    <div class="page-wrapper">

        <!-- Page Content -->
        <div class="content container-fluid">
            <x-alert-notification />
            @yield('main-container')
        </div>
    </div>
</div>

@include('admin.layout.foot')

@include('admin.layout.footer')
