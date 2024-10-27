<!-- Add Policy Modal -->
<div id="add_policy" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Policy</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('admin.policy.store') }}">
                    @method('POST')
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
            </div>
        </div>
    </div>
</div>
<!-- /Add Policy Modal -->

@push('modal-script')
    <script>
        // hourly_base_fields
        // $("#pay_gen_type").select2();

        // $("#pay_gen_type").on("change", function() {
        //     // Your custom function here
        //     alert('asd123');
        //     let selectedValue = $(this).val();
        //     if (selectedValue == "2") {
        //         $("#hourly_base_fields").css({
        //             'display': 'block'
        //         })
        //     }
        // });

        $(document).ready(function() {
            // Initialize DateTimePicker with time-only format
            $('.timepicker').datetimepicker({
                format: 'hh:mm A', // Display only hours and minutes
                // stepping: 1
            });
        });

        function payslipgenerationtypechange() {
            // alert(selectedValue);
            let selectedValue = $('#pay_gen_type').val();
            // alert(selectedValue);
            if (selectedValue == "3") {
                $("#hourly_base_fields").css({
                    'display': 'block'
                })
            } else {
                $("#hourly_base_fields").css({
                    'display': 'none'
                })
            }
        }


        function departmentChange(ev) {
            let {
                value
            } = ev.target;

            $("#add_policy_employee").empty();

            $.ajax({
                url: "/admin/get/employee?dept_id=" + value, // Replace with your API endpoint
                method: "GET",
                dataType: "json",
                success: function(response) {
                    // Handle the successful response
                    // $("#result").html("Title: " + response.title);
                    console.log(response);
                    var select2Instance = $('#add_policy_employee').select2({
                        minimumResultsForSearch: -1,
                        width: '100%'
                    });
                    let employees = response.data;
                    for (let index = 0; index < employees.length; index++) {
                        const obj = employees[index];
                        // Get the Select2 instance for the select element


                        // Create a new option element
                        var newOption = new Option(`${obj.first_name} ${obj.last_name}`, obj.id, false, false);

                        // Append the new option to the select2 dropdown
                        select2Instance.append(newOption);

                        // Trigger the 'change' event to notify Select2 about the change
                        select2Instance.trigger('change');
                    }
                },
                error: function(error) {
                    // Handle errors
                    console.log(error);
                }
            });
        }
    </script>
@endpush
