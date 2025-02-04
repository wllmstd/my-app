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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body>

    @include('admin.admin_navbar') <!-- Include the navbar here -->
    <div class="container mt-4">
        <h2 class="text-center mb-4">Manage Users</h2>

        <!-- Add User Button -->
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="bi bi-person-plus"></i> Add User
            </button>
        </div>

        <!-- User Management Table -->
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Department</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $loop->iteration }}</td> <!-- Display the iteration number -->
                    <td>{{ $user->first_name }}</td>
                    <td>{{ $user->last_name }}</td>
                    <td>{{ $user->department }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <!-- Edit button that opens the Edit Modal -->
                        <button class="btn btn-warning btn-sm" onclick="openEditModal({{ json_encode($user) }})" data-bs-toggle="modal" data-bs-target="#editUserModal">Edit</button>
                        <form action="{{ route('adminmanage.delete', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirmDelete();">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
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
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <!-- Password -->
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <!-- Confirm Password -->
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Image -->
                                <div class="mb-3 text-center">
                                    <label for="image" class="form-label">Profile Image</label>
                                    <div class="d-flex justify-content-center">
                                        <img id="profileImage" src="" class="rounded-circle img-fluid mt-3 mb-3" style="width: 300px; height: 300px; object-fit: cover; display: none;">
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
                                    <input type="text" class="form-control" id="editFirstName" name="first_name" required>
                                </div>
                                <!-- Last Name -->
                                <div class="mb-3">
                                    <label for="editLastName" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="editLastName" name="last_name" required>
                                </div>
                                <!-- Department -->
                                <div class="mb-3">
                                    <label for="editDepartment" class="form-label">Department</label>
                                    <input type="text" class="form-control" id="editDepartment" name="department" required>
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
                                    <img id="editProfileImage" src="" class="rounded-circle img-fluid mt-3" style="width: 300px; height: 300px; object-fit: cover;">
                                    <input type="file" class="form-control" id="editImage" name="image" accept="image/*">
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


    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript to Populate Edit Modal -->
    <script>
            function openEditModal(user) {
    // Open the edit modal
        $('#editUserModal').modal('show');

        // Set the form action URL for the save-edited route
        document.getElementById('editUserForm').action = '/adminmanage/save-edited/' + user.id;

        // Set the hidden ID field
        document.getElementById('editUserId').value = user.id;

        // Populate the form fields with the current user data
        document.getElementById('editFirstName').value = user.first_name;
        document.getElementById('editLastName').value = user.last_name;
        document.getElementById('editDepartment').value = user.department;
        document.getElementById('editEmail').value = user.email;

        // Set the profile image (if it exists)
        document.getElementById('editProfileImage').src = user.image ? '/storage/' + user.image : 'https://via.placeholder.com/150';
    }


    </script>


</body>
</html>
