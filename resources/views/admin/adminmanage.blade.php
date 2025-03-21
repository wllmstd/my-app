<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Bundle (JS & Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery (if needed) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script><!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <!-- Bootstrap Icons CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <!-- JS FOR PROFILE -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="icon" type="image/png" src="{{ asset('images/gecologo.png') }}" sizes="512x512">

</head>

<body>

    @include('admin.admin_navbar')
    <!-- Include the navbar here -->
    <div class="scroll-container">

        <div class="container mt-4">
            <!-- Add User Button -->
            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-person-plus"></i> Add User
                </button>

            </div>

            <!-- User Management Table -->
            <table class="table" id="userTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User Name</th>
                        <th>Department</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $loop->iteration }}</td> <!-- Display the iteration number -->
                        <td>
                            <div class="d-flex align-items-center">
                                @if(auth()->user()->department == 'Admin')
                                <img src="{{ url('storage/' . $user->image) }}"
                                    onerror="this.onerror=null; this.src='{{ asset('images/default.png') }}';"
                                    alt="Profile Image" class="rounded-circle me-2" width="40" height="40">


                                @endif
                                <span>{{ $user->first_name }} {{ $user->last_name }}</span>
                            </div>


                        </td>
                        <td>{{ $user->department }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <!-- Edit button that opens the Edit Modal -->
                            <button class="btn btn-warning btn-sm" onclick="openEditModal({{ json_encode($user) }})"
                                data-bs-toggle="modal" data-bs-target="#editUserModal">
                                <i class="bi bi-pencil-square"></i>
                            </button>

                            <form action="{{ route('adminmanage.delete', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger btn-sm"
                                    onclick="openDeleteConfirmationModal({{ $user->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>

                            </form>


                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('adminmanage.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <!-- First Name -->
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                                </div>
                                <!-- Last Name -->
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                                </div>
                                <!-- Department -->
                                <div class="mb-3">
                                    <label for="department" class="form-label">Department</label>
                                    <select class="form-select" id="department" name="department" required>
                                        <option value="" disabled selected>Select Department</option>
                                        <option value="Profiler">Profiler</option>
                                        <option value="Talent Acquisition">Talent Acquisition</option>
                                        <option value="Admin">Admin</option>
                                    </select>
                                </div>
                                <!-- Email -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required
                                        autocomplete="new-password">
                                </div>
                                <!-- Password -->
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required
                                        autocomplete="new-password">
                                </div>
                                <!-- Confirm Password -->
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Image -->
                                <div class="mb-3 text-center">
                                    <label for="image" class="form-label">Profile Image</label>
                                    <div class="d-flex justify-content-center">
                                        <img id="profileImage" src="" class="rounded-circle img-fluid mt-3 mb-3"
                                            style="width: 300px; height: 300px; object-fit: cover; display: none;">
                                    </div>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form for Editing User -->
                    <form id="editUserForm" method="POST" action="" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="editUserId" name="id">
                        <div class="row">
                            <div class="col-md-6">
                                <!-- First Name -->
                                <div class="mb-3">
                                    <label for="editFirstName" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="editFirstName" name="first_name"
                                        required>
                                </div>
                                <!-- Last Name -->
                                <div class="mb-3">
                                    <label for="editLastName" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="editLastName" name="last_name" required>
                                </div>
                                <!-- Department -->
                                <div class="mb-3">
                                    <label for="editDepartment" class="form-label">Department</label>
                                    <select class="form-select" id="editDepartment" name="department" required>
                                        <option value="Admin"
                                            {{ old('department', $user->department) == 'Admin' ? 'selected' : '' }}>
                                            Admin</option>
                                        <option value="Profiler"
                                            {{ old('department', $user->department) == 'Profiler' ? 'selected' : '' }}>
                                            Profiler</option>
                                        <option value="Talent Acquisition"
                                            {{ old('department', $user->department) == 'Talent Acquisition' ? 'selected' : '' }}>
                                            Talent
                                            Acquisition</option>
                                    </select>
                                </div>

                                <!-- Email -->
                                <div class="mb-3">
                                    <label for="editEmail" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="editEmail" name="email" required>
                                </div>
                                <!-- Password -->
                                <div class="mb-3">
                                    <label for="editPassword" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="editPassword" name="password">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Image -->
                                <div class="mb-3 text-center">
                                    <label for="editImage" class="form-label">Profile Image</label>
                                    <img id="editProfileImage" src="" class="rounded-circle img-fluid mt-3"
                                        style="width: 300px; height: 300px; object-fit: cover;">
                                    <input type="file" class="form-control" id="editImage" name="image"
                                        accept="image/*">
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal (Centered with Bootstrap classes) -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <!-- Added modal-dialog-centered class -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    Are you sure you want to delete this user?<br>This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>



    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


    <!-- JavaScript to Reset Form Fields When Modal Opens -->
    <script>
    // Disable Autofill for Email and Password Fields
    $(document).ready(function() {
        $('#addEmail, #addPassword').on('focus', function() {
            $(this).val(''); // Clear the fields on focus
        });
    });
    </script>

    <script>
    // Function to preview image before upload (used in both Add and Edit modals)
    function previewImage(input, imgElementId) {
        const file = input.files[0]; // Get the selected file
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(imgElementId).src = e.target.result;
                document.getElementById(imgElementId).style.display = "block"; // Ensure the image is visible
            };
            reader.readAsDataURL(file); // Read the file as a Data URL
        } else {
            document.getElementById(imgElementId).src = ''; // Clear the image if no file selected
            document.getElementById(imgElementId).style.display = "none";
        }
    }

    // Attach event listeners for both Add and Edit modals
    document.getElementById("image").addEventListener("change", function() {
        previewImage(this, "profileImage");
    });

    document.getElementById("editImage").addEventListener("change", function() {
        previewImage(this, "editProfileImage");
    });

    // Function to open the Edit Modal and populate fields
    function openEditModal(user) {
        $('#editUserModal').modal('show');

        // Set form action
        document.getElementById('editUserForm').action = '/adminmanage/save-edited/' + user.id;

        // Populate fields
        document.getElementById('editUserId').value = user.id;
        document.getElementById('editFirstName').value = user.first_name;
        document.getElementById('editLastName').value = user.last_name;
        document.getElementById('editDepartment').value = user.department;
        document.getElementById('editEmail').value = user.email;

        // Set profile image if available
        let imageUrl = user.image ? '/storage/' + user.image : 'https://via.placeholder.com/150';
        document.getElementById('editProfileImage').src = imageUrl;
        document.getElementById('editProfileImage').style.display = "block"; // Ensure image is visible
    }
    </script>


    <!-- JavaScript to Handle Modal Behavior -->
    <script>
    // Function to open the delete confirmation modal and pass the user ID
    function openDeleteConfirmationModal(userId) {
        // Set the form action dynamically to target the correct user
        const form = document.querySelector('form[action*="delete"]'); // Find the correct form
        form.action = '/adminmanage/delete/' + userId; // Update form action with the user ID

        // Show the delete confirmation modal
        $('#deleteConfirmationModal').modal('show');
    }

    // Confirm the deletion when the "Delete" button in the modal is clicked
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        // Submit the form to delete the user
        const form = document.querySelector('form[action*="delete"]');
        form.submit();
    });
    </script>
</body>

<script>
$(document).ready(function() {
    $('#userTable').DataTable({
        "paging": true, // Enable pagination
        "searching": true, // Enable search
        "ordering": true, // Enable sorting
        "info": true, // Show information (entries count)
        "lengthMenu": [5, 10, 25, 50], // Dropdown for entries per page
    });
});
</script>


<style>
/* Modern Clean Design */
.container {
    max-width: 90%;
    margin: auto;
    font-family: 'Inter', sans-serif;
}

/* Header Styling */
h2 {
    font-size: 24px;
    font-weight: 600;
    color: #333;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-bottom: 3px solid #4CAF50;
    display: inline-block;
    padding-bottom: 5px;
}

/* Add User Button */
.btn-success {
    background: #4CAF50;
    border: none;
    padding: 10px 18px;
    border-radius: 8px;
}

.btn-success:hover {
    background: rgb(4, 81, 8);
    border: none;
    padding: 10px 18px;
    border-radius: 8px;
}


/* Profile Image */
.rounded-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #1f335f;
    margin-right: 10px;
}

/* Action Buttons */
.btn-warning,
.btn-danger {
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 14px;
}

.btn-warning {
    background: #FFB300;
    color: #fff;
    border: none;
}

.btn-warning:hover {
    background: #FFA000;
    transform: scale(1.1);
}

.btn-danger {
    background: #E53935;
    color: white;
    border: none;
}

.btn-danger:hover {
    background: #D32F2F;
    transform: scale(1.1);
}

.table {
    background: #D32F2F;

}

/* Global Font & Styling */
body,
table,
.dataTables_filter input,
.dataTables_length select {
    font-family: 'Poppins', sans-serif;
}

/* Search Bar */
.dataTables_filter {
    text-align: center;
    margin-bottom: 15px;
}

.dataTables_filter input {
    width: 250px;
    padding: 10px;
    font-size: 14px;
    border-radius: 8px;
    outline: none;
    transition: border-color 0.3s ease-in-out, box-shadow 0.3s;
    border-radius: 15px !important;

}

/* Styling for "Show X entries" */
.dataTables_length label {
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    color: #1f335f;
    font-weight: 500;
}

/* Styling for the dropdown itself */
.dataTables_length select {
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    color: #1f335f;
    padding: 5px;
    border-radius: 6px;
    border: 1px solid #ccc;
    outline: none;
}

/* Styling for "Search:" label */
.dataTables_filter label {
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    color: #1f335f;
    font-weight: 500;
}


#userTable {
    width: 100%;
    border-collapse: collapse;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    background: #1f335f;
    font-size: 14px;
}

#userTable th {
    background: #1f335f;
    color: #fff;
    text-align: center;
    vertical-align: middle;
    /* Centers vertically as well */
}

#userTable td {
    padding: 14px 16px;
    text-align: left;
    background: #fff;
    text-align: center;
    vertical-align: middle;
    /* Centers vertically as well */
}


/* Row Styling */
#userTable tbody tr {
    transition: background 0.3s, transform 0.2s ease-in-out;
}

/* Subtle Hover Effect */
#userTable tbody tr:hover {
    transform: scale(1.01);
}

/* Pagination */
.dataTables_paginate {
    text-align: center;
    margin-top: 20px;
}

.dataTables_paginate .paginate_button {
    font-family: 'Poppins', sans-serif;

    border-color: #1f335f !important;
    color: white !important;
    padding: 8px 12px;
    margin: 0 5px;
    border-radius: 15px !important;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s ease-in-out;
}

.dataTables_paginate .paginate_button:hover {
    background: #1f335f !important;
    transform: scale(1.1);
    color: white !important;
}

.dataTables_paginate .paginate_button.current {
    background: #A3CAE9 !important;
    font-weight: bold;
}

.dataTables_paginate .paginate_button.disabled {
    background: #ccc;
    cursor: not-allowed;
}

/* Entries Info Styling */
.dataTables_info {
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    color: #1f335f;
    font-weight: 500;
    margin-top: 10px;
    text-align: left;
    border-radius: 6px;
    display: inline-block;
}







/* CSS FOR MODALS*/

/* Modal Background Overlay */
.modal-backdrop {
    background-color: rgba(31, 51, 95, 0.8);
}

/* Modal Content Styling */
.modal-content {
    background-color: #f4f8ff;
    /* Light blue background */
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    border: 1px solid #1f335f;
}

/* Modal Header */
.modal-header {
    background-color: #1f335f;
    color: white;
    border-bottom: 2px solid #A3CAE9;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
}

.modal-title {
    font-family: 'Poppins', sans-serif;
    font-size: 20px;
    font-weight: 600;
}

/* Close Button */
.btn-close {
    background-color: #A3CAE9;
    border-radius: 50%;
    opacity: 1;
}

.btn-close:hover {
    background-color: rgb(255, 255, 255);
    opacity: 0.8;
}

/* Modal Body */
.modal-body {
    padding: 20px;
}

/* Form Labels */
.form-label {
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    color: #1f335f;
    font-weight: 500;
}

/* Form Inputs & Select */
.form-control,
.form-select {
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    border: 1px solid #A3CAE9;
    border-radius: 8px;
    padding: 10px;
    transition: border-color 0.3s ease-in-out;
}

.form-control:focus,
.form-select:focus {
    border-color: #1f335f;
    box-shadow: 0 0 10px rgba(31, 51, 95, 0.2);
}

/* Profile Image */
#profileImage {
    border: 2px solid #A3CAE9;
}

/* File Input Styling */
input[type="file"] {
    font-size: 14px;
    color: #1f335f;
}

/* Modal Footer */
.modal-footer {
    background-color: #f4f8ff;
    border-top: 1px solid #A3CAE9;
    border-bottom-left-radius: 12px;
    border-bottom-right-radius: 12px;
}

/* Primary Button (Add User) */
.btn-primary {
    background-color: #1f335f;
    border: none;
    padding: 10px 18px;
    border-radius: 8px;
    color: #fff;
    font-weight: 500;
    transition: background-color 0.3s ease;
}

.btn-primary:hover {
    background-color: #162847;
}

/* Secondary Button (Cancel) */
.btn-secondary {
    background-color: #A3CAE9;
    color: #1f335f;
    padding: 10px 18px;
    border-radius: 8px;
    border: none;
    font-weight: 500;
    transition: background-color 0.3s ease;
}

.btn-secondary:hover {
    background-color: #89b5d9;
}


/* Base Styling */
html,
body {
    height: 100%;
    overflow: hidden;
     Hide built-in scrollbar */
    font-family: 'Poppins', sans-serif;


}

/* Create a scrollable container */
.scroll-container {
    height: 100vh;
    overflow-y: auto;
    padding: 10px;
    box-sizing: border-box;
    padding-bottom: 100px;
    /* Extra space at the bottom */

}

/* Custom Scrollbar - Webkit (Chrome, Edge, Safari) */
.scroll-container::-webkit-scrollbar {
    width: 8px;
}

.scroll-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    /* Light background */
    border-radius: 10px;
}

.scroll-container::-webkit-scrollbar-thumb {
    background: rgba(31, 51, 95, 0.6);
    /* Blue scrollbar */
    border-radius: 10px;
    transition: background 0.3s ease;
}

.scroll-container::-webkit-scrollbar-thumb:hover {
    background: rgba(31, 51, 95, 0.8);
    /* Darker blue on hover */
}

/* Firefox scrollbar */
.scroll-container {
    scrollbar-width: thin;
    scrollbar-color: rgba(31, 51, 95, 0.6) #f1f1f1;
}

/* Hover effect for Firefox */
.scroll-container:hover {
    scrollbar-color: rgba(31, 51, 95, 0.8) #f1f1f1;
}
</style>

</html>