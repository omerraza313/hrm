<!-- Approve Leave Modal -->
<div class="modal custom-modal fade" id="approve_leave_status" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-header">
                    <h3>Leave Status</h3>
                    <p id="status_para">Are you sure want to approve for this leave?</p>
                </div>
                <div class="modal-btn delete-action">
                    <form method="post" id="statusForm">
                        @csrf
                        @method('POST')
                        <div class="row">

                            <div class="col-lg-12">
                                <div id="d_note"></div>
                            </div>
                            <div class="col-6">


                                <button type="submit" class="btn btn-primary continue-btn w-100"
                                    id="status_btn_text">Delete</button>

                            </div>
                            <div class="col-6">
                                <a href="javascript:void(0);" data-bs-dismiss="modal"
                                    class="btn btn-primary cancel-btn">Cancel</a>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Approve Leave Modal -->
