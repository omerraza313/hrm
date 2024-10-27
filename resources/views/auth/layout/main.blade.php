{{-- <script>
    window.onload = function() {
        var element = document.getElementById('rootReactVNB');

        if (element) {
            // Remove class and id
            element.removeAttribute('class');
            element.removeAttribute('id');
        }
    };
</script> --}}
@include('auth.layout.head')
<!-- Main Wrapper -->
@yield('auth-main')
@include('auth.layout.foot')
@include('auth.layout.footer')
