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
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <!-- DataTables CSS -->
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"> -->
    <!-- DataTables JS -->
    <!-- <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script> -->
</head>

<body>

    <!-- Include the navbar here -->
    @include('user.user_navbar')

    <div class="container mt-4">
        <h2>User Requests</h2>

        <!-- Add Request Button -->
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addRequestModal">
                <i class="bi bi-plus-circle"></i> Add Request
            </button>
        </div>

        <!-- Table to display requests -->
        <table class="table table-bordered table-striped" id="requestTable">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
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
                @foreach ($requests as $index => $request)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $request->Status }}</td>
                                    <td>{{ $request->First_Name }}</td>
                                    <td>{{ $request->Last_Name }}</td>
                                    <td>{{ $request->Nationality }}</td>
                                    <td>{{ $request->Location }}</td>
                                    <td>{{ $request->Format }}</td>
                                    <!-- FIXED ATTACHMENT COUNT LOGIC -->
                                    <td>
                                        @php
                                            $attachments = json_decode($request->Attachment, true);
                                        @endphp

                                        @if (!empty($attachments) && is_array($attachments))
                                            @if (count($attachments) === 1)
                                                1 file
                                            @else
                                                {{ count($attachments) }} files
                                            @endif
                                        @else
                                            No attachment
                                        @endif
                                    </td>


                                    <td>{{ $request->Date_Created }}</td>
                                    <td>{{ $request->Updated_Time }}</td>
                                    <td>
                                        <!-- View Request Button -->
                                        <button class="btn btn-primary btn-sm viewRequestBtn" data-id="{{ $request->Request_ID }}"
                                            data-first-name="{{ $request->First_Name }}" data-last-name="{{ $request->Last_Name }}"
                                            data-nationality="{{ $request->Nationality }}" data-location="{{ $request->Location }}"
                                            data-format="{{ $request->Format }}" data-attachments="{{ $request->Attachment }}"
                                            data-bs-toggle="modal" data-bs-target="#viewRequestModal">
                                            <i class="bi bi-eye"></i> View
                                        </button>

                                        <!-- Delete Request Button -->
                                        <button class="btn btn-danger btn-sm deleteRequestBtn" data-id="{{ $request->Request_ID }}"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>

                                        <!-- Review Submission Button -->
                                        <button class="btn btn-warning btn-sm reviewSubmissionBtn" data-id="{{ $request->Request_ID }}"
                                            data-first-name="{{ $request->First_Name }}" data-last-name="{{ $request->Last_Name }}"
                                            data-nationality="{{ $request->Nationality }}" data-location="{{ $request->Location }}"
                                            data-format="{{ $request->Format }}" data-uploaded-format="{{ $request->uploaded_format }}"
                                            data-profiler="{{ $request->Profiler_Name }}">
                                            <i class="bi bi-file-earmark-check"></i> Review Submission
                                        </button>
                                    </td>
                                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Add Request Modal -->
    <div class="modal fade" id="addRequestModal" tabindex="-1" aria-labelledby="addRequestModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRequestModalLabel">Add Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('requests.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="nationality" class="form-label">Nationality</label>
                            <input type="text" class="form-control" id="nationality" name="nationality" required>
                        </div>
                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="location" name="location" required>
                        </div>
                        <div class="mb-3">
                            <label for="format" class="form-label">Format</label>
                            <select class="form-select" id="format" name="format" required>
                                <option value="Geco Standard">Geco Standard</option>
                                <option value="Geco New Date">Geco New Date</option>
                                <option value="Geco New Rate">Geco New Rate</option>
                                <option value="Blind">Blind</option>
                                <option value="HTD">HTD</option>
                                <option value="SAP">SAP</option>
                                <option value="PCX">PCX</option>
                                <option value="Accenture">Accenture</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="attachment" class="form-label">Attachments (PDF, DOCX)</label>
                            <input type="file" class="form-control" id="attachment" name="attachments[]"
                                accept=".pdf,.doc,.docx" multiple>
                        </div>

                        <!-- File Preview Section -->
                        <div id="fileList" class="mt-2"></div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Request Modal -->
    <div class="modal fade" id="viewRequestModal" tabindex="-1" aria-labelledby="viewRequestModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewRequestModalLabel">View & Edit Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateRequestForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <!-- Laravel requires this for update requests -->
                    <input type="hidden" id="request_id" name="id"> <!-- Hidden field for ID -->

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="edit_first_name" name="first_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="edit_last_name" name="last_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_nationality" class="form-label">Nationality</label>
                            <input type="text" class="form-control" id="edit_nationality" name="nationality" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="edit_location" name="location" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_format" class="form-label">Format</label>
                            <select class="form-select" id="edit_format" name="format" required>
                                <option value="Geco Standard">Geco Standard</option>
                                <option value="Geco New Date">Geco New Date</option>
                                <option value="Geco New Rate">Geco New Rate</option>
                                <option value="Blind">Blind</option>
                                <option value="HTD">HTD</option>
                                <option value="SAP">SAP</option>
                                <option value="PCX">PCX</option>
                                <option value="Accenture">Accenture</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_attachments" class="form-label">Attachments</label>
                            <input type="file" class="form-control" id="edit_attachments" name="attachments[]" multiple>
                        </div>

                        <!-- Existing Attachments -->
                        <div id="existingAttachments">
                            <h6>Existing Attachments:</h6>
                            @php
                                $attachments = json_decode($request->Attachment, true);
                            @endphp
                            @if (!empty($attachments))
                                @foreach ($attachments as $file)
                                    <div class="d-flex align-items-center border p-2 mb-1 rounded">
                                        <a href="{{ asset('storage/attachments/' . $file) }}" target="_blank"
                                            class="me-auto">{{ $file }}</a>
                                        <<button type="button" class="btn btn-sm btn-danger delete-attachment-btn"
                                            data-request-id="{{ $request->Request_ID }}" data-file-name="{{ $file }}">
                                            <i class="bi bi-trash"></i>
                                            </button>
                                    </div>
                                @endforeach
                            @else
                                <p>No attachments found.</p>
                            @endif
                        </div>

                        <!-- Hidden Input for Deleted Files -->
                        <input type="hidden" name="deleted_files[]" id="deletedFilesInput">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <!-- Added modal-dialog-centered class here -->
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
                    <button type="button" class="btn btn-danger" id="confirmDelete">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Attachment Confirmation Modal -->
    <div class="modal fade" id="deleteAttachmentModal" tabindex="-1" aria-labelledby="deleteAttachmentModalLabel"
        aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteAttachmentModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this attachment?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                </div>
                <div class="modal-body text-center">
                    <p id="successMessage">Request updated successfully!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Okay</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Failed Modal -->
    <div class="modal fade" id="failedModal" tabindex="-1" aria-labelledby="failedModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="failedModalLabel">Update Failed</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p id="failedMessage">Failed to update the request. Please try again.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Okay</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Review Submission Modal -->
    <div class="modal fade" id="reviewSubmissionModal" tabindex="-1" aria-labelledby="reviewSubmissionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reviewSubmissionModalLabel">Review Submission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Profiled by:</strong> <span id="reviewProfiler"></span></p>
                    <p><strong>Requester:</strong> <span id="reviewRequester"></span></p>
                    <p><strong>Request Details:</strong> <span id="reviewDetails"></span></p>


                    <p><strong>Submitted File:</strong></p>
                    <div id="reviewUploadedFormat"></div>

                    <label for="reviewFeedback">Feedback:</label>
                    <textarea id="reviewFeedback" class="form-control" rows="3"
                        placeholder="Provide feedback..."></textarea>

                    <div class="mt-3">
                        <button id="markAsDoneBtn" class="btn btn-success">Mark as Done</button>
                        <button id="reviseBtn" class="btn btn-danger">Request Revision</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


</body>

</html>

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<script>

    //Initialize DataTable
    $(document).ready(function () {
        $('#requestTable').DataTable({
            "paging": true, // Enable pagination
            "searching": true, // Enable search
            "ordering": true, // Enable sorting
            "info": true, // Show information (entries count)
            "lengthMenu": [5, 10, 25, 50], // Dropdown for entries per page
            "columnDefs": [
                { "orderable": false, "targets": [10] } // Disable sorting for the action column
            ]
        });
    });

    //Function for File Removal 
    document.getElementById('attachment').addEventListener('change', function (event) {
        const fileList = document.getElementById('fileList');
        fileList.innerHTML = '';

        Array.from(event.target.files).forEach((file, index) => {
            const fileDiv = document.createElement('div');
            fileDiv.classList.add('d-flex', 'align-items-center', 'border', 'p-2', 'mb-1', 'rounded');
            fileDiv.innerHTML = `
                <span class="me-auto">${file.name} (${(file.size / 1024).toFixed(2)} KB)</span>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeFile(${index})">
                    <i class="bi bi-x"></i>
                </button>
            `;
            fileDiv.dataset.index = index;
            fileList.appendChild(fileDiv);
        });
    });

    function removeFile(index) {
        const fileList = document.getElementById('attachment');
        const newFiles = Array.from(fileList.files).filter((_, i) => i !== index);

        const dataTransfer = new DataTransfer();
        newFiles.forEach(file => dataTransfer.items.add(file));
        fileList.files = dataTransfer.files;

        document.querySelector(`div[data-index="${index}"]`).remove();
    }

    //View and Edit Form
    $(document).ready(function () {
        // Handle View Button Click
        $(document).on("click", ".viewRequestBtn", function () {
            // Debugging
            console.log("Button Data Attributes:", $(this).data());

            // Get values using .attr() for better reliability
            let requestId = $(this).data("id");
            let firstName = $(this).attr("data-first-name");
            let lastName = $(this).attr("data-last-name");
            let nationality = $(this).attr("data-nationality");
            let location = $(this).attr("data-location");
            let format = $(this).attr("data-format");
            let attachments = $(this).attr("data-attachments");

            console.log("Request ID:", requestId);
            console.log("First Name:", firstName);
            console.log("Last Name:", lastName);
            console.log("Nationality:", nationality);
            console.log("Location:", location);
            console.log("Format:", format);

            // Populate Form Fields
            $("#request_id").val(requestId);
            $("#edit_first_name").val(firstName);
            $("#edit_last_name").val(lastName);
            $("#edit_nationality").val(nationality);
            $("#edit_location").val(location);
            $("#edit_format").val(format);

            // Populate Existing Attachments
            let attachmentsHtml = '<h6>Existing Attachments:</h6>';
            try {
                let attachmentsArray = attachments ? JSON.parse(attachments) : [];
                if (attachmentsArray.length > 0) {
                    attachmentsArray.forEach((file) => {
                        let fileName = file.split('/').pop();
                        let fileUrl = `/storage/attachments/${fileName}`;
                        attachmentsHtml += `
                    <div class="d-flex align-items-center border p-2 mb-1 rounded">
                        <a href="${fileUrl}" target="_blank" class="me-auto">${fileName}</a>
                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteAttachment(${requestId}, '${file}')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                `;
                    });
                } else {
                    attachmentsHtml += '<p>No attachments found.</p>';
                }
            } catch (error) {
                console.error("Error parsing attachments:", error);
                attachmentsHtml += '<p>Error loading attachments.</p>';
            }
            $("#existingAttachments").html(attachmentsHtml);


            $("#request_id").val(requestId);

            // Set Form Action
            $("#updateRequestForm").attr("action", `/requests/update/${requestId}`);
        });


        // Handle Form Submission to save changes to the request
        $(document).ready(function () {
            $("#updateRequestForm").on("submit", function (e) {
                e.preventDefault(); // Prevent full-page reload

                let requestId = $("#request_id").val()
                    .trim(); // Get request ID and ensure it's not empty
                if (!requestId) {
                    alert("Error: Missing request ID.");
                    return;
                }

                let formData = new FormData(this);
                formData.append("_method", "PUT"); // Tell Laravel this is a PUT request

                $.ajax({
                    url: "/requests/update/" + requestId,
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        // Show success message inside the View/Edit modal instead of closing it
                        $("#successMessage").text("Request updated successfully!");
                        $("#successModal").modal("show");

                        // Update fields dynamically without closing the modal
                        setTimeout(() => {
                            $("#successModal").modal("hide");
                        }, 1000); // Auto-hide success modal after 1 second (adjust if needed)


                        // Reload after closing success modal
                        $("#successModal").on("hidden.bs.modal", function () {
                            location.reload();
                        });
                    },
                    error: function (xhr) {
                        console.error("Error:", xhr.responseText);

                        // Hide View/Edit Modal first
                        $("#viewRequestModal").modal("hide");

                        // Show Failure Modal after a slight delay

                        $("#failedMessage").text(
                            "Failed to update the request. Please try again."
                        );
                        $("#failedModal").modal("show");
                        ;
                    }
                });
            });
        });




        // Function to Delete an Attachment
        function deleteAttachment(requestId, fileName) {
            if (confirm("Are you sure you want to delete this attachment?")) {
                $.ajax({
                    url: `/requests/${requestId}/delete-attachment`,
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        file_name: fileName
                    },
                    success: function (response) {
                        if (response.success) {
                            alert("Attachment deleted successfully!");
                            location.reload(); // Reload the page to reflect changes
                        } else {
                            alert("Failed to delete attachment.");
                        }
                    }
                });
            }
        }


        $(document).ready(function () {
            // Handle File Deletion
            $(".delete-file-btn").on("click", function () {
                let fileToDelete = $(this).data("file");
                let deletedFilesInput = $("#deletedFilesInput");

                // Add file to deleted files array
                let deletedFiles = deletedFilesInput.val() ? JSON.parse(deletedFilesInput.val()) :
                    [];
                deletedFiles.push(fileToDelete);
                deletedFilesInput.val(JSON.stringify(deletedFiles));

                // Remove the file from the UI
                $(this).closest("div").remove();
            });
        });

        //Delete Request 
        let deleteId = null;

        // Capture request ID when delete button is clicked
        $(document).on("click", ".deleteRequestBtn", function () {
            deleteId = $(this).data("id");
        });

        // Confirm deletion and send AJAX request
        $("#confirmDelete").on("click", function () {
            if (deleteId) {
                $.ajax({
                    url: "/requests/delete/" + deleteId, // Ensure this matches your Laravel route
                    type: "DELETE",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        // Hide the delete modal after deletion
                        $("#deleteModal").modal("hide");

                        // Reload the page after a short delay to reflect changes
                        setTimeout(() => {
                            location.reload();
                        }, 500); // Adjust delay if needed
                    },
                    error: function (xhr, status, error) {
                        alert("Error deleting request: " + error);
                    }
                });
            }
        });
    });

    // Store the request ID and file name globally
    let deleteRequestId = null;
    let deleteFileName = null;

    // Function to switch from the View Modal to the Delete Confirmation Modal
    function deleteAttachment(requestId, fileName) {
        deleteRequestId = requestId;
        deleteFileName = fileName;

        $("#viewRequestModal").modal("hide"); // Hide the view modal
        setTimeout(() => {
            $("#deleteAttachmentModal").modal("show"); // Show delete modal after transition
        }, 300); // Small delay for smooth transition
    }

    // Handle Cancel - Switch Back to the View Modal
    $("#deleteAttachmentModal").on("hidden.bs.modal", function () {
        if (!deleteConfirmed) {
            $("#viewRequestModal").modal("show"); // Reopen view modal if not deleted
        }
    });

    // Handle Confirm Delete
    let deleteConfirmed = false;
    $("#confirmDeleteBtn").on("click", function () {
        deleteConfirmed = true; // Mark as confirmed

        $.ajax({
            url: `/requests/${deleteRequestId}/delete-attachment`,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                file_name: deleteFileName
            },
            success: function (response) {
                $("#deleteAttachmentModal").modal("hide"); // Close delete modal
                location.reload(); // Reload to reflect changes

            },
            error: function (xhr) {
                alert("Error deleting attachment: " + xhr.responseJSON.error);
            }
        });
    });

    //Handles the Review Submission Modal
    $(document).ready(function () {
        $(".reviewSubmissionBtn").on("click", function () {
            let requestId = $(this).data("id");
            let firstName = $(this).data("first-name");
            let lastName = $(this).data("last-name");
            let format = $(this).data("format");
            let uploadedFormat = $(this).data("uploaded-format");

            // Fill modal fields
            $("#reviewRequester").text(firstName + " " + lastName);
            $("#reviewDetails").text(format);

            // Display uploaded file
            if (uploadedFormat) {
                $("#reviewUploadedFormat").html(
                    `<a href="/storage/submitted_files/${uploadedFormat}" target="_blank">${uploadedFormat}</a>`
                );
            } else {
                $("#reviewUploadedFormat").html("<p>No submitted file available</p>");
            }

            // Show the modal
            $("#reviewSubmissionModal").modal("show");

            // Handle "Mark as Done" Button Click
            $("#markAsDoneBtn").off("click").on("click", function () {
                $.ajax({
                    url: `/requests/${requestId}/complete`,
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        status: "Completed",
                        feedback: $("#reviewFeedback").val()
                    },
                    success: function () {
                        alert("Request marked as complete!");
                        $("#reviewSubmissionModal").modal("hide");
                        location.reload(); // Refresh table
                    },
                    error: function () {
                        alert("Error updating request status.");
                    }
                });
            });

            // Handle "Request Revision" Button Click
            $("#reviseBtn").off("click").on("click", function () {
                $.ajax({
                    url: `/requests/${requestId}/revise`,
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        status: "Needs Revision",
                        feedback: $("#reviewFeedback").val()
                    },
                    success: function () {
                        alert("Request sent back for revision.");
                        $("#reviewSubmissionModal").modal("hide");
                        location.reload(); // Refresh table
                    },
                    error: function () {
                        alert("Error updating request status.");
                    }
                });
            });
        });
    });

    //Load Profiler Name in the Review Submission Modal
    $(document).ready(function () {
        $(".reviewSubmissionBtn").on("click", function () {
            let requestId = $(this).data("id");

            $.ajax({
                url: `/requests/${requestId}/details`,
                type: "GET",
                success: function (data) {
                    $("#reviewRequester").text(data.First_Name + " " + data.Last_Name);
                    $("#reviewDetails").text(data.Format);

                    //profiler name is display
                    let profilerName = (data.profiler_first_name && data.profiler_last_name)
                        ? `${data.profiler_first_name} ${data.profiler_last_name}`
                        : "Not Assigned";

                    $("#reviewProfiler").text(profilerName);

                    //Display uploaded file
                    if (data.uploaded_format) {
                        $("#reviewUploadedFormat").html(
                            `<a href="/storage/submitted_files/${data.uploaded_format}" target="_blank">${data.uploaded_format}</a>`
                        );
                    } else {
                        $("#reviewUploadedFormat").html("<p>No submitted file available</p>");
                    }

                    // Show the modal
                    $("#reviewSubmissionModal").modal("show");
                },
                error: function () {
                    $("#reviewProfiler").text("Not Assigned");
                    alert("Failed to load request details.");
                }
            });
        });
    });


</script>