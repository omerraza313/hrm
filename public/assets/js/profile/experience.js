function add_experience_clone() {
    let html_field = `<div class="card">
    <div class="card-body">
    <input type="hidden" name="edu_id[]">
        <h3 class="card-title">Experience Information <a href="javascript:void(0);" onclick="deleteExpClone(event);"
        class="delete-icon"><i class="fa fa-trash-o"></i></a></h3>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                <label>Company Name</label>
                    <input type="text" class="form-control" name="exp_company_name[]"
                        >
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                <label>Location</label>
                    <input type="text" class="form-control" name="exp_location[]"
                        >
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                <label>Job Position</label>
                    <input type="text" class="form-control" name="exp_job_position[]"
                        >
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                <label>Period From</label>
                    <div class="cal-icon">
                        <input type="text" class="form-control datetimepicker"
                            name="exp_period_from[]" >
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                <label>Period To</label>
                    <div class="cal-icon">
                        <input type="text" class="form-control datetimepicker"
                            name="exp_period_to[]" >
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>`;

    let clone = document.getElementById("clone_exp_field");
    clone.innerHTML += html_field;

    if($('.datetimepicker').length > 0) {
		$('.datetimepicker').datetimepicker({
			format: 'DD/MM/YYYY',
			icons: {
				up: "fa fa-angle-up",
				down: "fa fa-angle-down",
				next: 'fa fa-angle-right',
				previous: 'fa fa-angle-left'
			}
		});
	}
}

function deleteExpClone(ev) {
    let mainBody = ev.target.parentNode.parentNode.parentNode.parentNode;
    mainBody.remove();
}

// function focusdatepick(ev) {
//     console.log(ev.target);
//     let ele = ev.target;
//     ele.datetimepicker({
//         // Add configuration options if needed
//     });
//     $(ele).datetimepicker("show");
// }

// function blurdatepick(ev) {
//     $(ev.target).datetimepicker("hide");
// }
