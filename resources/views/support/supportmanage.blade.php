<!-- supportmanage.blade.php -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Manage Profiles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <!-- jQuery & DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>


</head>

<body>

    <!-- Include the navbar here -->
    @include('support.support_navbar')

    <div class="container mt-4">
        <!-- Table 1: My Accepted Requests -->
        <h2>My Accepted Requests</h2>
        <table id="acceptedRequestsTable" class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Status</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Nationality</th>
                    <th>Location</th>
                    <th>Format</th>
                    <th>Date Accepted</th>
                    <th>Attachments</th>
                    <th>Uploads</th>
                    <th>Actions</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($myAcceptedRequests as $index => $request)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        @if($request->Status === 'Pending')
                        <span class="badge bg-warning text-dark">{{ $request->Status }}</span> <!-- Yellow -->
                        @elseif($request->Status === 'In Progress')
                        <span class="badge bg-primary">{{ $request->Status }}</span> <!-- Blue -->
                        @elseif($request->Status === 'Under Review')
                        <span class="badge"
                            style="background-color: orange; color: black;">{{ $request->Status }}</span>
                        <!-- Orange -->
                        @elseif($request->Status === 'Under Revision')
                        <span class="badge bg-danger">{{ $request->Status }}</span> <!-- Red -->
                        @elseif($request->Status === 'Completed')
                        <span class="badge bg-success">{{ $request->Status }}</span> <!-- Green -->
                        @else
                        <span class="badge bg-secondary">{{ $request->Status }}</span> <!-- Default (Gray) -->
                        @endif
                    </td>


                    <td>{{ $request->First_Name }}</td>
                    <td>{{ $request->Last_Name }}</td>
                    <td>{{ $request->Nationality }}</td>
                    <td>{{ $request->Location }}</td>
                    <td>{{ $request->Format }}</td>
                    <td>{{ $request->Updated_Time }}</td>
                    <td>
                        <button class="btn btn-info btn-sm viewAttachmentsBtn"
                            data-attachments='@json(json_decode($request->Attachment, true))' data-bs-toggle="modal"
                            data-bs-target="#attachmentsModal">
                            View Attachments
                        </button>


                    </td>

                    <!-- ✅ Display Uploaded Format -->

                    <td id="uploadedFormat-{{ $request->Request_ID }}">
                        <!-- Upload Button - Opens Upload Modal -->
                        <button class="btn btn-warning btn-sm openUploadModalBtn" data-id="{{ $request->Request_ID }}"
                            data-files='@json(json_decode($request->uploaded_format, true) ?? [])'
                            {{ $request->Status === 'In Progress' ? '' : 'disabled' }} data-bs-toggle="modal"
                            data-bs-target="#uploadModal">
                            Edit & Upload
                        </button>
                    </td>
                    <td>
                        <button class="btn btn-success btn-sm forwardRequestBtn" data-id="{{ $request->Request_ID }}"
                            {{ $request->Status === 'In Progress' ? '' : 'disabled' }}>
                            Forward
                        </button>
                    </td>




                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Table 2: Pending Requests -->
        <h2>All Pending Requests</h2>
        <table class="table table-bordered">
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
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($profiles as $index => $profile)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $profile->Status }}</td>
                    <td>{{ $profile->First_Name }}</td>
                    <td>{{ $profile->Last_Name }}</td>
                    <td>{{ $profile->Nationality }}</td>
                    <td>{{ $profile->Location }}</td>
                    <td>{{ $profile->Format }}</td>
                    <td>
                        @php
                        $attachments = json_decode($profile->Attachment, true);
                        @endphp

                        @if (!empty($attachments) && is_array($attachments))
                        {{ count($attachments) }} file(s)
                        @else
                        No attachment
                        @endif
                    </td>
                    <td>{{ $profile->Date_Created }}</td>
                    <td>
                        <button class="btn btn-primary btn-sm viewRequestBtn" data-id="{{ $profile->Request_ID }}"
                            data-first-name="{{ $profile->First_Name }}" data-last-name="{{ $profile->Last_Name }}"
                            data-nationality="{{ $profile->Nationality }}" data-location="{{ $profile->Location }}"
                            data-format="{{ $profile->Format }}" data-attachments="{{ $profile->Attachment }}"
                            data-bs-toggle="modal" data-bs-target="#viewRequestModal">
                            View & Accept
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>

    <!-- View & Accept Request Modal -->
    <div class="modal fade" id="viewRequestModal" tabindex="-1" aria-labelledby="viewRequestModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewRequestModalLabel">View Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="request_id" name="id">

                    <div class="mb-3">
                        <label for="view_first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="view_first_name" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="view_last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="view_last_name" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="view_nationality" class="form-label">Nationality</label>
                        <input type="text" class="form-control" id="view_nationality" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="view_location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="view_location" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="view_format" class="form-label">Format</label>
                        <input type="text" class="form-control" id="view_format" readonly>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary acceptRequestBtn">Accept Request</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload Attachment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="upload_request_id" name="request_id"> <!-- Store Request ID -->

                    <div class="modal-body">
                        <h6>Existing Files:</h6>
                        <div id="existingUploadedFiles" class="mb-3"></div> <!-- ✅ Files + Trash Buttons Here -->

                        <label for="fileUpload" class="form-label">Select File(s)</label>
                        <input type="file" class="form-control" id="fileUpload" name="uploaded_format[]" multiple>

                        <div id="uploadFeedback" class="text-danger d-none">Please select at least one file.</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Forward Confirmation Modal -->
    <div class="modal fade" id="forwardConfirmModal" tabindex="-1" aria-labelledby="forwardConfirmLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="forwardConfirmLabel">Confirm Forwarding</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to forward this request? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmForwardBtn">Yes, Forward</button>
                </div>
            </div>
        </div>
    </div>




    <!-- Attachments Modal -->
    <div class="modal fade" id="attachmentsModal" tabindex="-1" aria-labelledby="attachmentsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="attachmentsModalLabel">Attachments</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul id="attachmentsList" class="list-group">
                        <!-- Attachments will be inserted dynamically here -->
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>



    <!-- JavaScript for Accept Request -->
    <script>
        $(document).ready(function() {
            $(document).on("click", ".viewRequestBtn", function() {
                let requestId = $(this).data("id"); // ✅ Fetch ID from View button
                let firstName = $(this).attr("data-first-name");
                let lastName = $(this).attr("data-last-name");
                let nationality = $(this).attr("data-nationality");
                let location = $(this).attr("data-location");
                let format = $(this).attr("data-format");
                let attachments = $(this).attr("data-attachments");

                // ✅ Populate Modal Fields
                $("#request_id").val(requestId);
                $("#view_first_name").val(firstName);
                $("#view_last_name").val(lastName);
                $("#view_nationality").val(nationality);
                $("#view_location").val(location);
                $("#view_format").val(format);

                // ✅ Assign Request ID to Accept Button (Fix)
                $(".acceptRequestBtn").attr("data-id", requestId);

                // ✅ Handle Attachments
                let attachmentsContainer = $("#attachmentsContainer");
                let noAttachmentsText = $("#noAttachments");
                attachmentsContainer.html(""); // Clear previous attachments

                try {
                    let attachmentsArray = attachments ? JSON.parse(attachments) : [];
                    if (attachmentsArray.length > 0) {
                        noAttachmentsText.hide();
                        attachmentsArray.forEach((file) => {
                            let fileName = file.split('/').pop();
                            let fileUrl = `/storage/attachments/${fileName}`;
                            attachmentsContainer.append(`
                        <div class="d-flex align-items-center border p-2 mb-1 rounded">
                            <a href="${fileUrl}" target="_blank" class="me-auto">${fileName}</a>
                        </div>
                    `);
                        });
                    } else {
                        noAttachmentsText.show();
                    }
                } catch (error) {
                    console.error("Error parsing attachments:", error);
                    noAttachmentsText.text("Error loading attachments.").show();
                }

                // ✅ Show Modal
                $("#viewRequestModal").modal("show");
            });

            // ✅ Accept Request Button - Ensure Correct ID is Sent
            $(document).on("click", ".acceptRequestBtn", function() {
                let requestId = $(this).attr("data-id"); // ✅ Correctly fetch ID

                if (!requestId) {
                    alert("Request ID not found. Please try again.");
                    return;
                }

                $.ajax({
                    url: "/requests/accept/" + requestId, // ✅ Correct Route
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") // ✅ CSRF Token
                    },
                    data: {
                        status: "In Progress"
                    }, // ✅ Send updated status
                    success: function(response) {
                        alert("Request accepted successfully! Status updated to In Progress.");
                        location.reload(); // ✅ Refresh Page to Update Status

                        // ✅ Update status in table dynamically
                        let row = $("button.uploadBtn[data-id='" + requestId + "']").closest(
                            "tr");
                        row.find("td:nth-child(2)").text("In Progress"); // Update status column

                        // ✅ Enable Upload Button and Change Style
                        let uploadBtn = row.find(".uploadBtn");
                        uploadBtn.removeAttr("disabled").removeClass("btn-secondary").addClass(
                            "btn-success");
                    },
                    error: function(xhr) {
                        console.error("Error:", xhr.responseText);
                        alert("Failed to accept request. Please check console for details.");
                    }
                });
            });
        });




        // Delete Attachment Function
        function deleteAttachment(requestId, file) {
            if (confirm("Are you sure you want to delete this attachment?")) {
                let deletedFiles = $("#deletedFilesInput").val() ? JSON.parse($("#deletedFilesInput").val()) : [];
                deletedFiles.push(file);
                $("#deletedFilesInput").val(JSON.stringify(deletedFiles));

                $("a[href$='" + file + "']").closest("div").remove(); // Remove from UI
            }
        }


        $(document).ready(function() {
            // ✅ Open Upload Modal & Load Existing Files
            $(".openUploadModalBtn").on("click", function() {
                let requestId = $(this).data("id");
                let existingFiles = $(this).data("files") || [];

                $("#upload_request_id").val(requestId); // Set ID

                let filesContainer = $("#existingUploadedFiles");
                filesContainer.html(""); // Clear previous content

                if (existingFiles.length > 0) {
                    existingFiles.forEach(file => {
                        let fileUrl = `/storage/uploads/${file}`;
                        let fileHtml = `
                    <div class="d-flex align-items-center border p-2 mb-1 rounded">
                        <a href="${fileUrl}" target="_blank" class="me-auto">${file}</a>
                        <button class="btn btn-sm btn-danger deleteFileBtn" data-id="${requestId}" data-file="${file}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                `;
                        filesContainer.append(fileHtml);
                    });
                } else {
                    filesContainer.html("<p>No uploaded files yet.</p>");
                }
            });

            // ✅ Delete File Functionality
            $(document).on("click", ".deleteFileBtn", function() {
                let fileName = $(this).data("file"); // Get file name
                let requestId = $(this).data("id"); // Get request ID

                if (!confirm("Are you sure you want to delete this file?")) return;

                $.ajax({
                    url: "/requests/delete-file/" + requestId,
                    type: "POST",
                    data: {
                        file_name: fileName,
                        _token: $('meta[name="csrf-token"]').attr("content") // CSRF token
                    },
                    success: function(response) {
                        alert(response.success); // ✅ Show success message

                        // ✅ Hide the modal first
                        $("#viewUploadedFilesModal").modal("hide");

                        // ✅ Reload the page after a short delay
                        setTimeout(() => {
                            location.reload();
                        }, 500); // Delay of 500ms before reloading
                    },
                    error: function(xhr) {
                        console.error("Error:", xhr.responseText);
                        alert("Failed to delete file. Please try again.");
                    }
                });
            });




            $("#uploadForm").on("submit", function(e) {
                e.preventDefault();

                let formData = new FormData();
                let requestId = $("#upload_request_id").val();
                let files = $("#fileUpload")[0].files;

                if (files.length === 0) {
                    $("#uploadFeedback").removeClass("d-none");
                    return;
                }

                // ✅ Append all files to FormData
                for (let i = 0; i < files.length; i++) {
                    formData.append("uploaded_format[]", files[i]);
                }

                formData.append("request_id", requestId);
                formData.append("_token", $('meta[name="csrf-token"]').attr("content"));

                $.ajax({
                    url: "/requests/upload-format/" + requestId,
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        alert(response.success);

                        // ✅ Update files inside the modal dynamically
                        let filesContainer = $("#existingUploadedFiles");
                        filesContainer.html(response.files.map(file =>
                            `<div class="d-flex align-items-center border p-2 mb-1 rounded">
                        <a href="/storage/uploads/${file}" target="_blank">${file}</a>
                        <button class="btn btn-sm btn-danger deleteFileBtn" data-id="${requestId}" data-file="${file}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>`
                        ).join(''));

                        // ✅ Hide the modal first
                        $("#uploadModal").modal("hide");

                        // ✅ Reload the page after a short delay
                        setTimeout(() => {
                            location.reload();
                        }, 500); // Adjust delay if needed
                    },
                    error: function(xhr) {
                        console.error("Error:", xhr.responseText);
                        alert("File upload failed. Please try again.");
                    }
                });
            });
        });





        $(".viewAttachmentsBtn").click(function() {
            let attachments = $(this).data("attachments");
            console.log("Raw attachments data:", attachments); // Debugging output

            $("#attachmentsList").empty();

            if (!attachments || attachments === "null" || attachments.length === 0) {
                $("#attachmentsList").append(
                    '<li class="list-group-item text-muted">No attachments available.</li>');
                return;
            }

            let attachmentArray;
            try {
                attachmentArray = typeof attachments === "string" ? JSON.parse(attachments) : attachments;
            } catch (error) {
                console.error("Error parsing attachments:", error);
                $("#attachmentsList").append(
                    '<li class="list-group-item text-danger">Error loading attachments.</li>');
                return;
            }

            if (!Array.isArray(attachmentArray)) {
                console.error("Expected an array but got:", typeof attachmentArray);
                $("#attachmentsList").append(
                    '<li class="list-group-item text-danger">Invalid attachment format.</li>');
                return;
            }

            attachmentArray.forEach((file, index) => {
                let filePath = `/storage/attachments/${file}`;
                let fileName = file.split('/').pop(); // Extract file name

                console.log("File Path:", filePath); // Debugging
                $("#attachmentsList").append(`
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <a href="${filePath}" target="_blank">${fileName}</a>
                <button class="btn btn-success btn-sm downloadAttachmentBtn" data-file="${filePath}" data-filename="${fileName}">
                    Download
                </button>
            </li>
        `);
            });

            // Download functionality
            $(".downloadAttachmentBtn").click(function() {
                let fileUrl = $(this).data("file");
                let fileName = $(this).data("filename");

                let a = document.createElement("a");
                a.href = fileUrl;
                a.download = fileName; // This forces the download
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            });
        });



        $(document).ready(function() {
            let selectedRequestId = null;

            // When "Forward" is clicked, store the request ID and show the modal
            $(".forwardRequestBtn").on("click", function() {
                selectedRequestId = $(this).data("id");
                $("#forwardConfirmModal").modal("show");
            });

            // When "Yes, Forward" is clicked, send AJAX request
            $("#confirmForwardBtn").on("click", function() {
                if (!selectedRequestId) {
                    alert("Error: Request ID not found.");
                    return;
                }

                $.ajax({
                    url: "/requests/forward/" + selectedRequestId, // Laravel Route
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                    },
                    success: function(response) {
                        alert(response.success); // Show success message
                        $("#forwardConfirmModal").modal("hide"); // Close modal

                        // ✅ Reload the page after a short delay to reflect changes
                        setTimeout(() => {
                            location.reload();
                        }, 500);
                    },
                    error: function(xhr) {
                        console.error("Error:", xhr.responseText);
                        alert("Failed to forward request. Please try again.");
                    }
                });
            });
        });

        //Initialize DataTables
        $(document).ready(function() {
            // Enable DataTables for My Accepted Requests
            $('#acceptedRequestsTable').DataTable({
                "paging": true, // Enable pagination
                "searching": true, // Enable search bar
                "ordering": true, // Enable sorting
                "info": true // Show info (e.g., "Showing 1-10 of 50 entries")
            });

            // Enable DataTables for All Pending Requests
            $('#pendingRequestsTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true
            });
        });
    </script>

</body>

</html>