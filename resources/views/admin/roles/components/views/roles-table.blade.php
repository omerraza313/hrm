<div class="table-responsive">
    <table class="datatable table table-stripped mb-0">
        <thead>
            <tr>
                <th>Id</th>
                <th>Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($roles))
                @foreach ($roles as $role)
                    <tr>
                        <td>{{ $role->id }}</td>
                        <td>{{ $role->name }}</td>
                        <td>
                            @can('role.edit')
                            <a class="btn btn-primary btn-sm text-white" href="{{route('roles.edit', $role->id)}}">Edit</a>
                            @endcan
                            <!-- <a class="btn btn-danger btn-sm text-white">Delete</a> -->
                        </td>

                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>

@push('modal-script')

@endpush
