@extends('admin.layout.main')
@section('main-container')
    @push('title')
        Device Logs - Vibeh
    @endpush
    @push('header-assets')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@10.10.1/dist/sweetalert2.min.css'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.all.min.js"></script>

    @endpush
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Device Logs</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.employee.all') }}">Employees</a></li>
                    <li class="breadcrumb-item active">Device Logs</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                <a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#addModal"><i
                        class="fa fa-plus"></i> Add Device Log</a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table id="dataTable" class="table table-striped custom-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 30px;">#</th>
                            <th>User name </th>
                            <th>Device ID</th>
                            <th>Time </th>
                            <th>Type </th>
                            <th>Remarks </th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<!-- Add Brand -->
<div id="addModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Add Brand</h4>
            </div>
            <form id="addForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">Device</label>
                        <select name="device_id" class="form-control" required>
                            <option>Floor 1</option>
                            <option>Floor 2</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Type</label>
                        <select name="type" class="form-control" required>
                            <option>CheckIn</option>
                            <option>CheckOut</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Employee</label>
                        <select name="user_id" class="form-control" required>
                            <option>Select</option>
                            @foreach($employees as $employee) 
                            <option value="{{$employee->id}}">{{$employee->full_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Time</label>
                        <input type="datetime-local" name="time" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="">Remarks</label>
                        <textarea name="remarks" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="submitAddForm()" class="btn btn-primary">Add</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Brand -->
<div class="modal fade" id="updateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Update Brand</h4>
            </div>
            <form id="updateForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">Device</label>
                        <select name="device_id" class="form-control" id="update_device_id" required>
                            <option>Floor 1</option>
                            <option>Floor 2</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Type</label>
                        <select name="type" class="form-control" id="update_type" required>
                            <option>CheckIn</option>
                            <option>CheckOut</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Employee</label>
                        <select name="user_id" class="form-control" id="update_user_id" required>
                            <option>Select</option>
                            @foreach($employees as $employee) 
                            <option value="{{$employee->id}}">{{$employee->full_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Time</label>
                        <input type="datetime-local" name="time" id="update_time" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="">Remarks</label>
                        <textarea name="remarks" id="update_remarks" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="submitEditForm()" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-simple" data-bs-dismiss="modal">CLOSE</button>
                </div>
            </form>
        </div>
    </div>
</div>
    <script>
    var table = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('device-logs.datatable') }}",
            method: "GET",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function(d) {
                // d.airport_id = $('#data-table').data('airport_id');
            }
        },
        columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'user_name',
                name: 'user_name'
            },
            {
                data: 'time',
                name: 'time'
            },
            {
                data: 'device_id',
                name: 'device_id'
            },
            {
                data: 'type',
                name: 'type'
            },
            {
                data: 'remarks',
                name: 'remarks'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            },
        ]
    });

    // Record Add Form Submit
    function submitAddForm() {
        var formData = $('#addForm').serialize();
        $.ajax({
            url: "{{route('device-logs.store')}}",
            type: "POST",
            data: formData,
            success: function(response) {
                table.ajax.reload(null, false);
                $('#addModal').modal('hide');
                $('#addForm')[0].reset();
                Swal.fire('Success', 'Device Log has been added successfully!', 'success')

            },
            error: function(xhr) {
                Swal.fire('Error', 'An error occurred while creating the device log', 'error');
            }
        });
    }

    // Update Modal Show
    function editItem(itemId) {
        $('#updateForm').data('id', itemId);
        console.log(itemId);
        $.ajax({
            url: `{{ url('admin/device-logs') }}/${itemId}/edit`,
            type: "GET",
            success: function(response) {
                $('#update_name').val(response.device_log.name);
                $('#update_device_id').val(response.device_log.device_id);
                $('#update_type').val(response.device_log.type);
                $('#update_user_id').val(response.device_log.user_id);
                $('#update_time').val(response.device_log.time);
                $('#update_remarks').text(response.device_log.remarks);
                $('#updateModal').modal('show');
            },
            error: function(xhr) {
                Swal.fire('Error', 'An error occurred while creating the device log', 'error');
            }
        });
    }

    // Record Update Form Submit
    function submitEditForm() {
        var formData = $('#updateForm').serialize();
        var id = $('#updateForm').data("id");
        $.ajax({
            url: "{{url('admin/device-logs')}}/" + id,
            type: "PUT",
            data: formData,
            success: function(response) {
                table.ajax.reload(null, false);
                $('#updateModal').modal('hide');
                Swal.fire('Success', 'Device Log has been updated successfully!', 'success')

            },
            error: function(xhr) {
                Swal.fire('Error', 'An error occurred while creating the device log', 'error');
            }
        });
    }

    // Sweet Alert on delete
    function deleteItem(itemId) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'You will not be able to recover this item!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{url('admin/device-logs')}}/" + itemId,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(data) {
                        table.ajax.reload(null, false);
                        Swal.fire('Success', 'Device Log has been deleted successfully!', 'success')
                    },
                    error: function(xhr) {

                    }
                });
            }
        });
    }
</script>
@endsection
