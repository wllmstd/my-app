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

    <!-- Include the navbar here -->
    @include('user.user_navbar') 

    <div class="container mt-4">
        <h2>User Requests</h2>

        <!-- Add Request Button (green button positioned to the right) -->
        <div class="d-flex justify-content-end mb-3">
            <a href="" class="btn btn-success btn-sm">Add Request</a> 
        </div>

        <!-- Table to display requests -->
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>#</th> <!-- Changed to # for the headcount -->
                    <th>Status</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Nationality</th>
                    <th>Location</th>
                    <th>Format</th>
                    <th>Attachment</th>
                    <th>Date Created</th>
                    <th>Updated Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($requests as $index => $request) <!-- Adding index to count the requests -->
                <tr>
                    <td>{{ $index + 1 }}</td> <!-- Show the row number as # -->
                    <td>{{ $request->Status }}</td>
                    <td>{{ $request->First_Name }}</td>
                    <td>{{ $request->Last_Name }}</td>
                    <td>{{ $request->Nationality }}</td>
                    <td>{{ $request->Location }}</td>
                    <td>{{ $request->Format }}</td>
                    <td>{{ $request->Attachment }}</td>
                    <td>{{ $request->Date_Created }}</td>
                    <td>{{ $request->Updated_Time }}</td>
                    <td>
                        <!-- Add buttons for actions like delete, edit, etc. -->
                        <a href="#" class="btn btn-warning btn-sm">Edit</a>
                        <a href="#" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">Delete</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Delete Confirmation Modal (for Delete action) -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this request? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
