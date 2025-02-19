<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <!-- Bootstrap & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/user/userdashboard.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS (Required for Modal) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</head>

<body>
    @include('user.user_navbar')
    <!-- Include the navbar here -->

    <div class="container mt-4">
        <h2 class="mb-4 text-center">Dashboard Overview</h2>
        <div class="row">
            <!-- Total Requests Card -->
            <div class="col-md-6 d-flex equal-height">
                <div class="stat-card w-100">
                    <i class="fa-solid fa-file-lines stat-icon"></i> <!-- Paper Icon -->
                    <div>
                        <div class="stat-number" id="requestCount">0</div>
                        <p class="text-muted">Total Requests</p>
                    </div>
                </div>
            </div>

            <!-- Pending Status Card -->
            <div class="col-md-6 d-flex equal-height">
                <div class="stat-card w-100 d-flex flex-column">
                    <!-- Header -->
                    <h5 class="text-center mb-3">Request Status</h5>

                    <div class="d-flex w-100" style="flex-grow: 1; align-items: center;">
                        <!-- Chart on the Left -->
                        <div style="flex: 1; display: flex; align-items: center; justify-content: center;">
                            <canvas id="statusChart" style="max-width: 200px; max-height: 200px;"></canvas>
                        </div>

                        <!-- Status Legend with Numbers on the Right -->
                        <div style="flex: 1; padding-left: 20px;">
                            <ul class="list-unstyled" id="statusDetails">
                                <li><span
                                        style="background-color: #ffc107; width: 15px; height: 15px; display: inline-block; border-radius: 50%;"></span>
                                    Pending: <strong id="pendingCount">0</strong></li>
                                <li><span
                                        style="background-color: #17a2b8; width: 15px; height: 15px; display: inline-block; border-radius: 50%;"></span>
                                    In Progress: <strong id="inProgressCount">0</strong></li>
                                <li><span
                                        style="background-color: #28a745; width: 15px; height: 15px; display: inline-block; border-radius: 50%;"></span>
                                    Completed: <strong id="completedCount">0</strong></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>




        </div>
    </div>


    <script>
    document.addEventListener("DOMContentLoaded", function() {
        fetch("{{ route('user.request.counts') }}")
            .then(response => response.json())
            .then(data => {
                console.log("Total Requests:", data.totalRequests); // Debugging
                document.getElementById("requestCount").textContent = data.totalRequests; // Update number
            })
            .catch(error => console.error("Error fetching total requests:", error));
    });



    document.addEventListener("DOMContentLoaded", function() {
        fetch("{{ route('user.request.status.counts') }}")
            .then(response => response.json())
            .then(data => {
                console.log("Status Counts:", data); // Debugging

                // Update the numbers in the legend
                document.getElementById("pendingCount").textContent = data.pending;
                document.getElementById("inProgressCount").textContent = data.in_progress;
                document.getElementById("completedCount").textContent = data.completed;

                let ctx = document.getElementById("statusChart").getContext("2d");

                new Chart(ctx, {
                    type: "pie",
                    data: {
                        labels: ["Pending", "In Progress", "Completed"],
                        datasets: [{
                            label: "Request Status",
                            data: [data.pending, data.in_progress, data.completed],
                            backgroundColor: ["#ffc107", "#17a2b8", "#28a745"],
                            borderColor: "#fff",
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false // Hide default legend since we are using a custom one
                            }
                        }
                    }
                });
            })
            .catch(error => console.error("Error fetching request status counts:", error));
    });
    </script>




</body>

</html>