<script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>

<!-- Bootstrap Core JS -->
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>

<!-- Slimscroll JS -->
<script src="{{ asset('assets/js/jquery.slimscroll.min.js') }}"></script>

<!-- Chart JS -->
<script src="{{ asset('assets/plugins/morris/morris.min.js') }}"></script>
<script src="{{ asset('assets/plugins/raphael/raphael.min.js') }}"></script>
<script src="{{ asset('assets/js/chart.js') }}"></script>

<!-- Select2 JS -->
<script src="{{ asset('assets/js/select2.min.js') }}"></script>

<!-- Datatable JS -->
<script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/dataTables.bootstrap4.min.js') }}"></script>


<!-- Custom JS -->
<script src="{{ asset('assets/js/app.js?v=24.7.25-a') }}"></script>

<!-- Datetimepicker JS -->
<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap-datetimepicker.min.js') }}"></script>
<script src="{{ asset('assets/js/butterup.min.js') }}"></script>

@stack('modal-script')

<!-- Pusher Configuration -->
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
@include('js.pusher.leaveapplication.leaveapplication')
