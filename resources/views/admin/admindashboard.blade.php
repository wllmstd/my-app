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

        <!-- Welcome Section -->
        <div class="welcome-box text-center mb-4 p-3 rounded">
            <h2 class="welcome-title">Welcome, Admin {{ auth()->user()->first_name ?? 'Admin' }}! 🌞</h2>
            <p class="welcome-subtitle">Here’s an overview of your platform’s status and activity.</p>
        </div>


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

            <!-- Notifications & Updates -->
            <div class="col-md-6 d-flex equal-height">
                <div class="stat-card w-100">
                    <h5 class="text-center mb-3">Recent Activity</h5>
                    <ul class="list-group" id="recentActivity">
                        <li class="list-group-item text-muted">No recent activity</li>
                    </ul>
                </div>
            </div>

            <!-- Modern Calendar (Smaller) -->
            <div class="col-md-6 d-flex equal-height">
                <div class="calendar-card w-100">
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