function add_education_clone() {
    let subList = JSON.parse($("#subject_list").val());
    const randomString = generateRandomString(10);
    console.clear();
    console.log(subList);
    let subString = ``;
    Object.keys(subList).forEach(key => {
        const value = subList[key];
        subString += `
            <option value="${key}">
                ${value}
            </option>
        `;
    });

    let html_field = `<div class="card">
    <div class="card-body">
    <input type="hidden" name="edu_id[]">
        <h3 class="card-title">Education Information <a href="javascript:void(0);" onclick="deleteClone(event);"
        class="delete-icon"><i class="fa fa-trash-o"></i></a></h3>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                <label>Institution</label>
                    <input type="text" class="form-control" name="edu_name[]"
                        >
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                <label>Subject</label>
                <select class="form-control" name="edu_subject[]">
                <option value="">--Select--
                </option>
                    ${subString}
            </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                <label>Degree</label>
                    <input type="text" class="form-control" name="edu_degree[]"
                        >
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                <label>Grade</label>
                    <input type="text" class="form-control" name="edu_grade[]"
                        >
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                <label>Starting Date</label>
                    <div class="cal-icon">
                        <input type="text" class="form-control datetimepicker"
                            name="edu_start_date[]" >
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                <label>Complete Date</label>
                    <div class="cal-icon">
                        <input type="text" class="form-control datetimepicker"
                            name="edu_complete_date[]" >
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>`;

    let clone = document.getElementById("clone_edu_field");
    clone.innerHTML += html_field;

    if ($(".datetimepicker").length > 0) {
        $(".datetimepicker").datetimepicker({
            format: "DD/MM/YYYY",
            icons: {
                up: "fa fa-angle-up",
                down: "fa fa-angle-down",
                next: "fa fa-angle-right",
                previous: "fa fa-angle-left",
            },
        });
    }
}

function generateRandomString(length) {
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let result = '';

    for (let i = 0; i < length; i++) {
        const randomIndex = Math.floor(Math.random() * characters.length);
        result += characters.charAt(randomIndex);
    }

    return result;
}


function deleteClone(ev) {
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
