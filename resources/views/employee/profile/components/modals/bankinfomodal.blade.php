<div id="bank_modal" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bank Information</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.employee.bank.store') }}" method="POST">
                    @csrf
                    @method('post')
                    <input type="hidden" name="bank_id" value="{{ old('bank_id') ?? ($employee->bank->id ?? '') }}">
                    <x-field-validation errorname="bank_id" />
                    <input type="hidden" name="bank_employee_id"
                        value="{{ old('bank_employee_id') ?? $employee->id }}">
                    <x-field-validation errorname="bank_employee_id" />
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Bank Name</label>
                                <input type="text" class="form-control" name="bank_name"
                                    value="{{ old('bank_name') ?? ($employee->bank->name ?? '') }}">
                                <x-field-validation errorname="bank_name" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Bank Account No</label>
                                <input class="form-control" type="text" name="bank_account_no"
                                    value="{{ old('bank_account_no') ?? ($employee->bank->account_no ?? '') }}">
                                <x-field-validation errorname="bank_account_no" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Branch Code</label>
                                <input class="form-control" type="text" name="bank_branch_code"
                                    value="{{ old('bank_branch_code') ?? ($employee->bank->branch_code ?? '') }}">
                                <x-field-validation errorname="bank_branch_code" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>IBAN Number</label>
                                <input class="form-control" type="text" name="bank_iban_number"
                                    value="{{ old('bank_iban_number') ?? ($employee->bank->iban_number ?? '') }}">
                                <x-field-validation errorname="bank_iban_number" />
                            </div>
                        </div>
                    </div>
                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
