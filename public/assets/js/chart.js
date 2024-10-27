$(document).ready(function () {
    // Bar Chart
    let attendances = document.getElementById("total_attendances").value;
    console.clear();
    console.log(attendances);
    let allAttendances = [];

    if (attendances != "[]") {
        allAttendances = JSON.parse(attendances);
    }
    else {
        // console.log("Else Condition");
                // Get the current date
        let currentDate = new Date();

        // Get the components of the date (year, month, day)
        let year = currentDate.getFullYear();
        let month = String(currentDate.getMonth() + 1).padStart(2, '0'); // Months are zero-based
        let day = String(currentDate.getDate()).padStart(2, '0');

        // Format the date as "YYYY-MM-DD"
        let formattedDate = year + '-' + month + '-' + day;
        allAttendances.push({
            "arrival_date": formattedDate,
            "user_count": 0
        })
    }
    console.log(allAttendances);
    let chartData = [];
    allAttendances.forEach((element) => {
        chartData.push({
            y: element.arrival_date,
            a: element.user_count,
        });
    });

    Morris.Bar({
        element: "bar-charts",
        data: chartData,
        xkey: "y",
        ykeys: ["a"],
        labels: ["Total Employees"],
        lineColors: ["#00c5fb"],
        lineWidth: "3px",
        barColors: ["#00c5fb"],
        resize: true,
        redraw: true,
    });

    // Line Chart

    // Morris.Line({
    //     element: "line-charts",
    //     data: [
    //         { y: "2006", a: 50, b: 90 },
    //         { y: "2007", a: 75, b: 65 },
    //         { y: "2008", a: 50, b: 40 },
    //         { y: "2009", a: 75, b: 65 },
    //         { y: "2010", a: 50, b: 40 },
    //         { y: "2011", a: 75, b: 65 },
    //         { y: "2012", a: 100, b: 50 },
    //     ],
    //     xkey: "y",
    //     ykeys: ["a", "b"],
    //     labels: ["Total Sales", "Total Revenue"],
    //     lineColors: ["#00c5fb", "#0253cc"],
    //     lineWidth: "3px",
    //     resize: true,
    //     redraw: true,
    // });
});
