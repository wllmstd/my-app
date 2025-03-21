<head>
    <!-- Bootstrap & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

</head>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="{{ url('/supportdashboard') }}">
                <img src="{{ asset('images/geco_navbar.png') }}" alt="GECO Logo" class="me-2"
                    style="height: 40px; border-radius: 10px;">
                <span>GECO Queueing System</span>
            </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('supportdashboard') ? 'active text-white' : '' }}"
                        href="{{ route('supportdashboard') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('supportmanage') ? 'active text-white' : '' }}"
                        href="{{ route('supportmanage') }}">Manage</a>
                </li>

                <!-- Profile Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown">
                        <img src="{{ asset('storage/' . auth()->user()->image) }}" class="rounded-circle me-2" alt="Profile" width="35" height="35">
                        <span>{{ auth()->user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="{{ route('support.supportprofile') }}"><i class="fa fa-user me-2"></i>View Profile</a></li>
                        <li>
                            <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                <i class="fa fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
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
                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger">Yes, Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>


<style>
    /* Modern Navbar Styling */
.navbar {
    background: #1e3362; /* Dark background */
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
    padding: 10px 20px;
    font-family: 'Poppins', sans-serif;

}

/* Navbar Brand */
.navbar-brand {
    font-size: 1.5rem;
    font-weight: bold;
    color: #ffffff !important;
    transition: color 0.3s ease-in-out;
    margin-left: 5rem;
}

.navbar-brand:hover {
    color: #a3cae9 !important;
}

/* Navbar Links */
.navbar-nav .nav-link {
    font-size: 1.1rem;
    color: rgba(255, 255, 255, 0.8);
    transition: color 0.3s ease-in-out;
    padding: 8px 15px;
    margin-left:15px;

}

.navbar-collapse {
    margin-right: 5.3rem;

}


.navbar-nav .nav-link:hover,
.navbar-nav .nav-link.active {
    color: #a3cae9 !important; /* Modern blue accent */
    position: relative; /* Needed for underline effect */

}

.navbar-nav .nav-link.active::after {
    content: ''; 
    position: absolute;
    left: 50%;
    bottom: -3px; /* Distance from text */
    width: 40px; /* Fixed width for all */
    height: 3px; /* Thickness */
    background-color: #a3cae9; /* Accent color */
    border-radius: 2px;
    transform: translateX(-50%); /* Center align */
}

/* Profile Dropdown */
.navbar-nav .dropdown-toggle {
    color: white !important;
    transition: color 0.3s ease-in-out;
}

.navbar-nav .dropdown-toggle:hover {
    color: #a3cae9 !important;
}

/* Profile Image */
.navbar-nav .dropdown-toggle img {
    border: 2px solid #a3cae9;
    transition: transform 0.3s ease-in-out;
}

.navbar-nav .dropdown-toggle:hover img {
    transform: scale(1.1);
}

/* Dropdown Menu */
.dropdown-menu {
    border-radius: 8px;
    border: none;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.15);
    animation: fadeIn 0.3s ease-in-out;
}

/* Dropdown Items */
.dropdown-item {
    transition: background 0.3s ease-in-out, color 0.3s ease-in-out;
}

.dropdown-item:hover {
    background: #a3cae9;
    color: white !important;
}

/* Logout Modal */
.modal-content {
    border-radius: 10px;
}

.modal-footer .btn {
    padding: 8px 20px;
}

/* Keyframe Animation */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

</style>
<script>document.addEventListener("DOMContentLoaded", function () {
    // Dropdown animation
    const dropdowns = document.querySelectorAll(".dropdown");

    dropdowns.forEach((dropdown) => {
        dropdown.addEventListener("mouseenter", function () {
            let dropdownMenu = this.querySelector(".dropdown-menu");
            dropdownMenu.classList.add("show");
        });

        dropdown.addEventListener("mouseleave", function () {
            let dropdownMenu = this.querySelector(".dropdown-menu");
            dropdownMenu.classList.remove("show");
        });
    });

    // Navbar hover effect
    const navLinks = document.querySelectorAll(".nav-link");

    navLinks.forEach((link) => {
        link.addEventListener("mouseover", function () {
            link.style.transform = "translateY(-2px)";
        });

        link.addEventListener("mouseout", function () {
            link.style.transform = "translateY(0)";
        });
    });
});
</script>
