<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/support/supportdashboard.css') }}">

    <!-- Custom CSS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS (Required for Modal) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>


<body>

    @include('support.support_navbar')
    <!-- Include the navbar here -->

    <div class="container mt-4">
        <h2 class="mb-4 text-center">Dashboard Overview</h2>
        <div class="row">
            <!-- Pending Requests Card -->
            <div class="col-md-6">
                <div class="stat-card equal-height">
                    <i class="fas fa-clock stat-icon"></i>
                    <div>
                        <div class="stat-number" id="pendingRequestsCount">0</div>
                        <div class="text-muted">Pending Requests</div>
                    </div>
                </div>
            </div>

            <!-- Chart 1 - Support Request Status -->
            <!-- Chart 1 - Support Request Status -->
            <div class="col-md-6">
                <div class="stat-card equal-height d-flex flex-column">
                    <!-- Title in a separate row -->
                    <h5 class="text-center mb-3">Support Request Status</h5>

                    <!-- Chart and Details in another row -->
                    <div class="d-flex w-100" style="flex-grow: 1; align-items: center;">
                        <!-- Chart Container -->
                        <div style="flex: 1; display: flex; align-items: center; justify-content: center;">
                            <canvas id="supportStatusChart" style="max-width: 200px; max-height: 200px;"></canvas>
                        </div>
                        <!-- Details Container -->
                        <div style="flex: 1; padding-left: 20px;">
                            <ul class="list-unstyled" id="supportStatusDetails">
                                <li>
                                    <span
                                        style="background-color: #17a2b8; width: 15px; height: 15px; display: inline-block; border-radius: 50%;"></span>
                                    In Progress: <strong id="supportInProgressCount">0</strong>
                                </li>
                                <li>
                                    <span
                                        style="background-color: #ffc107; width: 15px; height: 15px; display: inline-block; border-radius: 50%;"></span>
                                    Under Review: <strong id="supportUnderReviewCount">0</strong>
                                </li>
                                <li>
                                    <span
                                        style="background-color: #dc3545; width: 15px; height: 15px; display: inline-block; border-radius: 50%;"></span>
                                    Needs Revision: <strong id="supportNeedsRevisionCount">0</strong>
                                </li>
                                <li>
                                    <span
                                        style="background-color: #28a745; width: 15px; height: 15px; display: inline-block; border-radius: 50%;"></span>
                                    Completed: <strong id="supportCompletedCount">0</strong>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="row mt-4">
            <!-- Chart 3 -->
            <div class="col-md-6">
                <div class="chart-container">
                    <h5 class="text-center">Accepted Requests (By Day)</h5>
                    <canvas id="acceptedRequestsChart"></canvas>
                </div>
            </div>

            <!-- Chart Container -->
            <div class="col-md-6">
                <div class="chart-container">
                    <h5 class="text-center">User Status Overview</h5>
                    <canvas id="usersChart"></canvas>
                </div>
            </div>

        </div>
    </div>


    <script>
    async function fetchPendingRequestsCount() {
        try {
            const response = await fetch('/pending-requests-count'); // ✅ Fixed route
            const data = await response.json();

            // ✅ Update the DOM
            document.getElementById('pendingRequestsCount').innerText = data.pendingRequestsCount;
        } catch (error) {
            console.error('Error fetching pending requests:', error);
            document.getElementById('pendingRequestsCount').innerText = 'Error';
        }
    }

    // ✅ Fetch data when the page loads
    fetchPendingRequestsCount();

    // ✅ Optionally refresh every 5 seconds
    setInterval(fetchPendingRequestsCount, 5000);






    document.addEventListener("DOMContentLoaded", function() {
        fetch("{{ route('support.request.status.counts') }}")
            .then(response => response.json())
            .then(data => {
                console.log("Fetched Status Data:", data);

                // ✅ Update the correct elements
                document.getElementById("supportInProgressCount").textContent = data.in_progress || 0;
                document.getElementById("supportUnderReviewCount").textContent = data.under_review || 0;
                document.getElementById("supportNeedsRevisionCount").textContent = data.needs_revision || 0;
                document.getElementById("supportCompletedCount").textContent = data.completed || 0;

                // ✅ Draw the chart if the canvas exists
                let ctx = document.getElementById("supportStatusChart")?.getContext("2d");
                if (ctx) {
                    new Chart(ctx, {
                        type: "pie",
                        data: {
                            labels: ["In Progress", "Under Review", "Needs Revision", "Completed"],
                            datasets: [{
                                data: [
                                    data.in_progress || 0,
                                    data.under_review || 0,
                                    data.needs_revision || 0,
                                    data.completed || 0
                                ],
                                backgroundColor: ["#17a2b8", "#ffc107", "#dc3545",
                                    "#28a745"
                                ],
                                borderColor: "#fff",
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                }
            })
            .catch(error => console.error("Error fetching status counts:", error));
    });


    document.addEventListener("DOMContentLoaded", function () {
    fetch("{{ route('support.request.accepted.by.day') }}")
        .then(response => response.json())
        .then(data => {
            let ctx = document.getElementById("acceptedRequestsChart").getContext("2d");

            new Chart(ctx, {
                type: "bar",
                data: {
                    labels: ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
                    datasets: [{
                        label: "Accepted Requests",
                        data: [
                            data.Monday,
                            data.Tuesday,
                            data.Wednesday,
                            data.Thursday,
                            data.Friday
                        ],
                        backgroundColor: "#4CAF50",
                        borderColor: "#388E3C",
                        borderWidth: 1,
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        })
        .catch(error => console.error("Error fetching accepted requests by day:", error));
});

    </script>

</body>

</html>