@extends('admin.layout.main')
@section('main-container')
    @push('title')
        Leave Setting - Vibeh
    @endpush

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Roles</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Create Role</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-12">
            <div class="card leave-box" id="leave_annual">
                <form action="{{route('roles.store')}}" method="post">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Role Name</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <span class="text-muted">Permissions</span>
                        <hr>
                        <div class="row">
                        @foreach ($permissions as $group => $permissionGroup)
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <h3>{{ ucfirst(str_replace('_', ' ', $group)) }}</h3>
                                        @foreach ($permissionGroup as $permission)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="permission-{{ $permission->id }}" {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permission-{{ $permission->id }}">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-success" type="submit">Save Role</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

