<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            background-color: #f8f9fa;
        }
        .chart-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }
        .navbar {
            background-color: #343a40 !important;
        }
        .navbar a {
            color: white !important;
        }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('admindashboard') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('adminmanage') }}">Manage</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Messages</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Notifications</a></li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Logout Confirmation Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p>Are you sure you want to log out?</p>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    <a href="{{ route('login') }}" class="btn btn-danger">Yes, Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Content -->
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
