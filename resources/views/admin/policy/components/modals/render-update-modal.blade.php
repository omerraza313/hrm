<form method="POST" action="{{ route('admin.policy.update', $policy) }}">
    @method('PUT')
    @csrf
    <div class="row">

        @include('admin.policy.components.modals.views.policymap')

        @include('admin.policy.components.modals.views.payroll')

        @include('admin.policy.components.modals.views.workinghours')

        @include('admin.policy.components.modals.views.policyhours')

        @include('admin.policy.components.modals.views.overtime_leave')


    </div>
    <div class="submit-section">
        <button class="btn btn-primary submit-btn">Submit</button>
    </div>
</form>