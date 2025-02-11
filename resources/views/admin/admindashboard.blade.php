<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <link rel="stylesheet" href="{{ asset('css/admin/admindashboard.css') }}"> <!-- Link to Laravel's CSS -->

    <!-- Bootstrap & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- jQuery & Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</head>

<body>

    @include('admin.admin_navbar')

    <div class="container mt-4">
        <h2 class="mb-4 text-center">Dashboard Overview</h2>
        <div class="row">
            <!-- Total Users Card (Same Height as Charts) -->
            <div class="col-md-6 d-flex equal-height">
                <div class="stat-card w-100">
                    <i class="fa-solid fa-users stat-icon"></i>
                    <div>
                        <div class="stat-number" id="totalUsers">{{ $totalUsers }}</div>
                        <p class="text-muted">Total Users</p>
                    </div>
                </div>
            </div>
            <!-- Users by Department Card -->
            <div class="col-md-6 d-flex equal-height">
                <div class="stat-card w-100 d-flex flex-column">
                    <!-- Header -->
                    <h5 class="text-center mb-3">Users by Department</h5>

                    <div class="d-flex w-100" style="flex-grow: 1; align-items: center;">
                        <!-- Chart on the Left -->
                        <div style="flex: 1; display: flex; align-items: center; justify-content: center;">
                            <canvas id="departmentChart" style="max-width: 200px; max-height: 200px;"></canvas>
                        </div>

                        <!-- Department Colors & Names on the Right -->
                        <div style="flex: 1; padding-left: 20px;">
                            <ul class="list-unstyled" id="departmentDetails"></ul>
                        </div>
                    </div>
                </div>
            </div>




            <div class="row mt-4">
                <!-- Revenue Chart -->
                <div class="col-md-6 d-flex equal-height">
                    <div class="chart-container w-100">
                        <h5 class="text-center">Revenue</h5>
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
                <!-- Performance Chart -->
                <div class="col-md-6 d-flex equal-height">
                    <div class="chart-container w-100">
                        <h5 class="text-center">Performance</h5>
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <script>
        function fetchUserCount() {
            $.ajax({
                url: "/admin/users/count",
                method: "GET",
                success: function(data) {
                    $("#totalUsers").text(data.totalUsers);
                }
            });
        }

        $(document).ready(function() {
            setInterval(fetchUserCount, 5000);
        });

        // User Growth Chart
        new Chart(document.getElementById("usersChart"), {
            type: "line",
            data: {
                labels: ["Jan", "Feb", "Mar", "Apr", "May"],
                datasets: [{
                    label: "Users",
                    data: [100, 150, 200, 300, 500],
                    borderColor: "#28a745",
                    fill: false
                }]
            }
        });

        // Revenue Chart
        new Chart(document.getElementById("revenueChart"), {
            type: "doughnut",
            data: {
                labels: ["Product A", "Product B", "Product C"],
                datasets: [{
                    data: [40, 30, 30],
                    backgroundColor: ["#ffc107", "#dc3545", "#17a2b8"]
                }]
            }
        });

        // Performance Chart
        new Chart(document.getElementById("performanceChart"), {
            type: "pie",
            data: {
                labels: ["Completed", "In Progress", "Pending"],
                datasets: [{
                    data: [60, 25, 15],
                    backgroundColor: ["#28a745", "#007bff", "#dc3545"]
                }]
            }
        });

        $(document).ready(function() {
            $.ajax({
                url: "/admin/users/department-count",
                method: "GET",
                success: function(data) {
                    var ctx = document.getElementById("departmentChart").getContext("2d");

                    var colors = ["#ff6384", "#36a2eb", "#ffce56"];
                    var labels = Object.keys(data);
                    var values = Object.values(data);

                    // Generate the Pie Chart WITHOUT showing labels above the chart
                    new Chart(ctx, {
                        type: "pie",
                        data: {
                            labels: labels, // Labels are still needed for tooltips but won't be displayed
                            datasets: [{
                                data: values,
                                backgroundColor: colors
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: false // 🚀 THIS DISABLES THE LABELS ABOVE THE CHART!
                                }
                            }
                        }
                    });

                    // 🛠 Append department names & colors in #departmentDetails (Only on the Right)
                    var detailsHtml = " ";
                    labels.forEach((department, index) => {
                        detailsHtml += `
        <li class="department-label">
            <span style="background-color: ${colors[index]};"></span>
            ${department}: <strong style="margin-left: 8px;">${values[index]}</strong>
        </li>
    `;
                    });

                    // Append to the department details section
                    $("#departmentDetails").html(detailsHtml);
                }
            });
        });
        </script>
</body>

</html>