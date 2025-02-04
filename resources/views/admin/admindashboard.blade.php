<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

    @include('admin.admin_navbar') <!-- Include the navbar here -->

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
    </div>

    <script>
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
    </script>

</body>
</html>
