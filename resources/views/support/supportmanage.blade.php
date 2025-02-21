<!-- supportmanage.blade.php -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Manage Profiles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Bundle (JS & Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (if needed) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

</head>

<body>

    <!-- Include the navbar here -->
    @include('support.support_navbar')

    <div class="container mt-4">
        <h2>Support Profiles</h2>

        <!-- Table to display profiles (which are actually requests) -->
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>#</th> <!-- For headcount of profiles -->
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
                @foreach ($profiles as $index => $profile)
                <!-- Index to get # -->
                <tr>
                    <td>{{ $index + 1 }}</td> <!-- This gives the number of the request -->
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
                        @if (count($attachments) === 1)
                        1 file
                        @else
                        {{ count($attachments) }} files
                        @endif
                        @else
                        No attachment
                        @endif
                    </td>
                    <td>{{ $profile->Date_Created }}</td>
                    <td>{{ $profile->Updated_Time }}</td>
                    <td>
                        <button class="btn btn-primary btn-sm viewRequestBtn" data-id="{{ $profile->Request_ID }}"
                            data-first-name="{{ $profile->First_Name }}" data-last-name="{{ $profile->Last_Name }}"
                            data-nationality="{{ $profile->Nationality }}" data-location="{{ $profile->Location }}"
                            data-format="{{ $profile->Format }}" data-attachments="{{ $profile->Attachment }}"
                            data-bs-toggle="modal" data-bs-target="#viewRequestModal">
                            View & Edit
                        </button>


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
                    Are you sure you want to delete this profile? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Request Modal -->
    <div class="modal fade" id="viewRequestModal" tabindex="-1" aria-labelledby="viewRequestModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewRequestModalLabel">View Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="request_id" name="id"> <!-- Hidden field for ID -->

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

                    <!-- Attachments Section -->
                    <div id="existingAttachments">
                        <h6>Attachments:</h6>
                        <p id="noAttachments">No attachments found.</p>
                        <div id="attachmentsContainer"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary acceptRequestBtn">
                        Accept Request
                    </button>

                </div>

            </div>
        </div>
    </div>




    </div>


</body>


<script>
$(document).ready(function() {
    $(document).on("click", ".viewRequestBtn", function () {
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
</script>

</html>