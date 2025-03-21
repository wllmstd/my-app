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

    <!-- Chart.js & jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JS (Required for Modal) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="icon" type="image/png" src="{{ asset('images/gecologo.png') }}" sizes="512x512">

</head>

<body>
    @include('user.user_navbar')

    <div class="scroll-container">
        <div class="container mt-4">
            <!-- Welcome Section -->
            <div class="welcome-box text-center mb-4 p-3 rounded">
                <h2 class="welcome-title">Welcome, Profiler {{ auth()->user()->first_name ?? 'Admin' }}! 🌞</h2>
                <p class="welcome-subtitle">Here’s an overview of your status and activity.</p>
            </div>

            <div class="row">
                <!-- Total Requests Card -->
                <div class="col-md-6 d-flex equal-height">
                    <div class="stat-card w-100">
                        <i class="fa-solid fa-thumbtack stat-icon"></i>
                        <div>
                            <div class="stat-number" id="requestCount">0</div>
                            <p class="text-muted">Total Requests</p>
                        </div>
                    </div>
                </div>

                <!-- Pending Status Card -->
                <!-- Pending Status Card -->
                <div class="col-md-6 d-flex equal-height">
                    <div class="stat-card w-100 d-flex flex-column">
                        <h5 class="text-center mb-3">Request Status</h5>
                        <div class="d-flex w-100" style="flex-grow: 1; align-items: center;">
                            <div style="flex: 1; display: flex; align-items: center; justify-content: center;">
                                <canvas id="statusChart" style="max-width: 200px; max-height: 200px;"></canvas>
                            </div>
                            <div style="flex: 1; padding-left: 20px;">
                                <ul class="list-unstyled" id="statusDetails">
                                    <li><span
                                            style="background-color: #ffc107; width: 15px; height: 15px; display: inline-block; border-radius: 50%;"></span>
                                        Pending: <strong id="pendingCount">0</strong></li>
                                    <li><span
                                            style="background-color: #136efd; width: 15px; height: 15px; display: inline-block; border-radius: 50%;"></span>

                                        In Progress: <strong id="inProgressCount">0</strong></li>

                                    <li><span
                                            style="background-color:rgb(255, 170, 0); width: 15px; height: 15px; display: inline-block; border-radius: 50%;"></span>
                                        Under Review: <strong id="underReviewCount">0</strong></li>
                                    <li><span
                                            style="background-color: #28a745; width: 15px; height: 15px; display: inline-block; border-radius: 50%;"></span>
                                        Completed: <strong id="completedCount">0</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Format Distribution Card -->
                <div class="col-md-6 d-flex equal-height">
                    <div
                        class="stat-card format-distribution w-100 d-flex flex-column align-items-center justify-content-between">
                        <h5 class="mb-3">Format Distribution</h5>
                        <canvas id="formatChart" class="chart-canvas"></canvas>
                    </div>
                </div>



                <!-- Total Attachments Card -->
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
    </div>

</body>

</html>




<script>
// Total Requests Count
document.addEventListener("DOMContentLoaded", function() {
    fetch("{{ route('user.request.counts') }}")
        .then(response => response.json())
        .then(data => {
            document.getElementById("requestCount").textContent = data.totalRequests;
        })
        .catch(error => console.error("Error fetching total requests:", error));
});

// Request Status Pie Chart
document.addEventListener("DOMContentLoaded", function() {
    fetch("{{ route('user.request.status.counts') }}")
        .then(response => response.json())
        .then(data => {
            document.getElementById("pendingCount").textContent = data.pending;
            document.getElementById("inProgressCount").textContent = data.in_progress;
            document.getElementById("underReviewCount").textContent = data.under_review;
            document.getElementById("completedCount").textContent = data.completed;

            let ctx = document.getElementById("statusChart").getContext("2d");
            new Chart(ctx, {
                type: "pie",
                data: {
                    labels: ["Pending", "In Progress", "Under Review","Completed"],
                    datasets: [{
                        data: [data.pending, data.in_progress, data.under_review, data.completed],
                        backgroundColor: ["#ffc107", "#136efd", "rgb(255, 170, 0)", "#28a745"],
                        borderColor: "#fff",
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false // Hide default legend since a custom one is used
                        }
                    }
                }
            });
        })
        .catch(error => console.error("Error fetching request status counts:", error));
});

document.addEventListener("DOMContentLoaded", function() {
    fetch("{{ route('user.request.format.counts') }}")
        .then(response => response.json())
        .then(data => {
            let allFormats = ["Geco Standard", "Geco New Date", "Geco New Rate", "Blind", "HTD", "SAP",
                "PCX", "Accenture"
            ];
            let formatData = allFormats.map(format => data.formats.labels.includes(format) ? data
                .formats.counts[data.formats.labels.indexOf(format)] : 0);

            let ctx = document.getElementById("formatChart").getContext("2d");
            new Chart(ctx, {
                type: "bar",
                data: {
                    labels: allFormats,
                    datasets: [{
                        label: "Format Count",
                        data: formatData,
                        backgroundColor: "#007bff"
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1, // Ensures only whole numbers are shown
                                precision: 0 // Removes decimal places
                            }
                        }
                    }
                }
            });
        })
        .catch(error => console.error("Error fetching format counts:", error));
});

document.addEventListener("DOMContentLoaded", function() {
    fetch("{{ route('user.request.attachments.count') }}")
        .then(response => response.json())
        .then(data => {
            document.getElementById("attachmentCount").textContent = data.totalAttachments;
        })
        .catch(error => console.error("Error fetching total attachments:", error));
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