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

        <!-- Welcome Section -->
        <div class="welcome-box text-center mb-4 p-3 rounded">
            <h2 class="welcome-title">Welcome, Profiler {{ auth()->user()->first_name ?? 'Admin' }}! 🌞</h2>
            <p class="welcome-subtitle">Here’s an overview of your status and activity.</p>
        </div>

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
                <div class="stat-card equal-height d-flex flex-column">
                    <!-- Title above the chart -->
                    <div class="chart-title">
                        <h5 class="text-center">Accepted Requests (By Day)</h5>
                    </div>

                    <!-- Chart container with specific class for Accepted Requests -->
                    <div class=" chart-container-accepted-requests">
                        <canvas id="acceptedRequestsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Chart Container -->
            <div class="col-md-6 d-flex equal-height">
                <div class="calendar-card w-100 d-flex flex-column">
                    <div class="calendar-header">
                        <button class="calendar-btn prev-btn">&lt;</button>
                        <span class="calendar-month" id="calendarMonth">March 2025</span>
                        <button class="calendar-btn next-btn">&gt;</button>
                    </div>
                    <div class="calendar-grid" id="calendarGrid"></div>
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





    document.addEventListener("DOMContentLoaded", function() {
        fetch("{{ route('support.request.accepted.by.day') }}")
            .then(response => response.json())
            .then(data => {
                console.log("Chart data:", data); // ✅ Confirm data structure

                let ctx = document.getElementById("acceptedRequestsChart").getContext("2d");

                new Chart(ctx, {
                    type: "bar",
                    data: {
                        labels: ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
                        datasets: [{
                            label: "Accepted Requests",
                            data: data, // ✅ Use the array directly
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



    document.addEventListener('DOMContentLoaded', () => {
        const calendarMonth = document.getElementById('calendarMonth');
        const calendarGrid = document.getElementById('calendarGrid');
        const prevBtn = document.querySelector('.prev-btn');
        const nextBtn = document.querySelector('.next-btn');

        let currentDate = new Date();

        function renderCalendar() {
            calendarGrid.innerHTML = '';
            const month = currentDate.getMonth();
            const year = currentDate.getFullYear();

            calendarMonth.textContent = new Intl.DateTimeFormat('en-US', {
                month: 'long',
                year: 'numeric'
            }).format(currentDate);

            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            // Add empty days for the starting day of the week
            for (let i = 0; i < firstDay; i++) {
                calendarGrid.innerHTML += `<div class="calendar-day empty"></div>`;
            }

            // Add actual days
            for (let day = 1; day <= daysInMonth; day++) {
                const dayElement = document.createElement('div');
                dayElement.classList.add('calendar-day');
                dayElement.textContent = day;

                if (
                    day === new Date().getDate() &&
                    year === new Date().getFullYear() &&
                    month === new Date().getMonth()
                ) {
                    dayElement.classList.add('today');
                }

                calendarGrid.appendChild(dayElement);
            }
        }

        prevBtn.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        });

        nextBtn.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        });

        renderCalendar();
    });
    </script>


</body>

</html>