<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

    @include('support.support_navbar') <!-- Include the navbar here -->

    <div class="container mt-4">
        <h2 class="mb-4 text-center">Dashboard Overview</h2>
        <div class="row">
            <!-- Chart 1 -->
            <div class="col-md-6">
                <div class="chart-container">
                    <h5 class="text-center">Sales Data</h5>
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
            <!-- Chart 2 -->
            <div class="col-md-6">
                <div class="chart-container">
                    <h5 class="text-center">User Growth</h5>
                    <canvas id="usersChart"></canvas>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <!-- Chart 3 -->
            <div class="col-md-6">
                <div class="chart-container">
                    <h5 class="text-center">Revenue</h5>
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
            <!-- Chart 4 -->
            <div class="col-md-6">
                <div class="chart-container">
                    <h5 class="text-center">Performance</h5>
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Sales Chart
        new Chart(document.getElementById("salesChart"), {
            type: "bar",
            data: {
                labels: ["Jan", "Feb", "Mar", "Apr", "May"],
                datasets: [{
                    label: "Sales",
                    data: [30, 45, 60, 70, 90],
                    backgroundColor: "#007bff"
                }]
            }
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
    </script>
</body>
</html>