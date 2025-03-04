console.log("✅ usermanage.js has been loaded!");


// Initialize Bootstrap tooltips (Add Hover Effect & Tooltip for Buttons)
$(document).ready(function() {
    $('[title]').tooltip();


      // Initialize DataTables
      let table = $('#requestTable').DataTable({
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "lengthMenu": [5, 10, 25, 50],
        "columnDefs": [{
            "orderable": false,
            "targets": [9] // Disable sorting for action column
        }]
    });

    // ✅ Preserve Filter State
    let savedFilter = localStorage.getItem("selectedFilter") || "all";
    applyFilter(savedFilter);

    // ✅ Apply Filter on Click
    $(".filter-btn").on("click", function () {
        let filter = $(this).data("filter");
        localStorage.setItem("selectedFilter", filter);
        applyFilter(filter);
    });

    function applyFilter(filter) {
        $(".filter-btn").removeClass("active");
        $(".filter-btn[data-filter='" + filter + "']").addClass("active");

        if (filter === "all") {
            table.search("").columns().search("").draw(); // Show everything
        } else {
            table.column(1).search("^" + filter + "$", true, false).draw(); // Exact match
        }
    }
});

//Function for File Removal 
document.getElementById('attachment').addEventListener('change', function(event) {
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
$(document).ready(function() {
    // Handle View Button Click
    $(document).on("click", ".viewRequestBtn", function() {
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
    $(document).ready(function() {
        $("#updateRequestForm").on("submit", function(e) {
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
                success: function(response) {
                    $("#viewRequestModal").modal("hide"); // Hide the view modal

                    // Show success message inside the View/Edit modal instead of closing it
                    $("#successMessage").text("Request updated successfully!");
                    $("#successModal").modal("show");

                    // Update fields dynamically without closing the modal
                    setTimeout(() => {
                            $("#successModal").modal("hide");
                        },
                        10
                    ); // Auto-hide success modal after 1 second (adjust if needed)


                    // Reload after closing success modal
                    $("#successModal").on("hidden.bs.modal", function() {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    console.error("Error:", xhr.responseText);

                    // Hide View/Edit Modal first
                    $("#viewRequestModal").modal("hide");

                    // Show Failure Modal after a slight delay

                    $("#failedMessage").text(
                        "Failed to update the request. Please try again."
                    );
                    $("#failedModal").modal("show");;
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
                success: function(response) {
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

        $(document).ready(function() {
        // Handle File Deletion
        $(".delete-file-btn").on("click", function() {
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
    $(document).on("click", ".deleteRequestBtn", function() {
        deleteId = $(this).data("id");
    });

    // Confirm deletion and send AJAX request
    $("#confirmDelete").on("click", function() {
        if (deleteId) {
            $.ajax({
                url: "/requests/delete/" + deleteId, // Ensure this matches your Laravel route
                type: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") // ✅ Fix: Ensure CSRF token is included
                },
                success: function(response) {
                    // Hide the delete modal after deletion
                    $("#deleteModal").modal("hide");

                    // Reload the page after a short delay to reflect changes
                    setTimeout(() => {
                        location.reload();
                    }, 10); // Adjust delay if needed
                },
                error: function(xhr, status, error) {
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
$("#deleteAttachmentModal").on("hidden.bs.modal", function() {
    if (!deleteConfirmed) {
        $("#viewRequestModal").modal("show"); // Reopen view modal if not deleted
    }
});

// Handle Confirm Delete
let deleteConfirmed = false;
$("#confirmDeleteBtn").on("click", function() {
    deleteConfirmed = true; // Mark as confirmed

    // ✅ Save the current filter before making the request
    let currentFilter = localStorage.getItem("selectedFilter") || "all";

    $.ajax({
        url: `/requests/${deleteRequestId}/delete-attachment`,
        type: "POST",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") // ✅ Fix: Ensure CSRF token is included
        },
        data: {
            file_name: deleteFileName
        },
        success: function(response) {
            $("#deleteAttachmentModal").modal("hide"); // Close delete modal

            // ✅ Restore the selected filter after reloading
            setTimeout(() => {
                localStorage.setItem("selectedFilter", currentFilter);
                location.reload();
            }, 500);
        },
        error: function(xhr) {
            alert("Error deleting attachment: " + xhr.responseJSON.error);
        }
    });
});


// Function to parse and display uploaded files from the request table only
function displayUploadedFiles(uploadedFormat) {
    let uploadedFilesArray = [];

    try {
        if (typeof uploadedFormat === "string") {
            uploadedFilesArray = JSON.parse(uploadedFormat);
        } else if (Array.isArray(uploadedFormat)) {
            uploadedFilesArray = uploadedFormat;
        }
    } catch (error) {
        console.error("Error parsing uploaded files:", error);
        uploadedFilesArray = [];
    }

    let uploadedFilesHtml = '<h6>Submitted Files:</h6>';
    if (uploadedFilesArray.length > 0) {
        uploadedFilesArray.forEach((file) => {
            let fileName = file.split('/').pop();
            let fileUrl = `/storage/uploads/${fileName}`;
            let downloadUrl = `/download/${fileName}`;

            uploadedFilesHtml += `
            <div class="d-flex justify-content-between align-items-center border p-2 mb-1 rounded">
                <a href="${fileUrl}" target="_blank" class="text-primary text-decoration-none">${fileName}</a>
                <a href="${downloadUrl}" class="btn btn-sm btn-success">
                    <i class="bi bi-download"></i> Download
                </a>
            </div>`;
        });
    } else {
        uploadedFilesHtml += '<p>No submitted files available.</p>';
    }

    $("#reviewUploadedFormat").html(uploadedFilesHtml);
}

//  Set CSRF Token Globally / Wait for document to be fully loaded
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });




    // Handle Review Submission Modal
    $(document).on("click", ".reviewSubmissionBtn", function(event) {
        event.preventDefault();
        let requestId = $(this).data("id");

        $.ajax({
            url: `/requests/${requestId}/details`,
            type: "GET",
            success: function(data) {
                $("#reviewSubmissionModal").data("request-id", requestId);
                $("#reviewRequester").text(`${data.First_Name} ${data.Last_Name}`);
                $("#reviewDetails").text(data.Format);

                let profilerName = (data.profiler_first_name && data.profiler_last_name) ?
                    `${data.profiler_first_name} ${data.profiler_last_name}` :
                    "Not Assigned";
                $("#reviewProfiler").text(profilerName);

                if (data.uploaded_format) {
                    displayUploadedFiles(data.uploaded_format);
                } else {
                    $("#reviewUploadedFormat").html("<p>No submitted file available</p>");
                }
                $("#reviewSubmissionModal").modal("show");
            },
            error: function() {
                alert("Failed to load request details.");
            }
        });
    });

    // Handle "Mark as Done" Click
    $("#markAsDoneBtn").on("click", function() {
        let requestId = $("#reviewSubmissionModal").data("request-id");
        let feedback = $("#reviewFeedback").val();

        if (!requestId) {
            alert("Error: Missing request ID.");
            return;
        }

        $.post(`/requests/${requestId}/complete`, {
                status: "Completed",
                feedback: feedback
            })
            .done(function() {
                alert("Request marked as complete!");
                $(`button[data-id='${requestId}']`).closest("tr").find("td:nth-child(2)").text(
                    "Completed");
                $("#reviewSubmissionModal").modal("hide");

                $("#reviewSubmissionModal").on("hidden.bs.modal", function() {
                location.reload();
            });

            })
            .fail(function(xhr) {
                console.error("AJAX Error:", xhr.responseText);
                alert("Error updating request status.");
            });
    });

    // Handle "Request Revision" Click
    $("#reviseBtn").on("click", function() {
        let requestId = $("#reviewSubmissionModal").data("request-id");
        let feedback = $("#reviewFeedback").val();

        if (!requestId) {
            alert("Error: Missing request ID.");
            return;
        }

        $.post(`/requests/${requestId}/revise`, {
                status: "Needs Revision",
                feedback: feedback
            })
            .done(function() {
                alert("Request sent back for revision.");
                $("#reviewSubmissionModal").modal("hide");
                location.reload();
            })
            .fail(function() {
                alert("Error updating request status.");
            });
    });
});

// Load Profiler Name in the Review Submission Modal
$(document).on("click", ".reviewSubmissionBtn", function() {
    let requestId = $(this).data("id");

    $.ajax({
        url: `/requests/${requestId}/details`,
        type: "GET",
        success: function(data) {
            $("#reviewRequester").text(`${data.First_Name} ${data.Last_Name}`);
            $("#reviewFormat").text(data.Format);

            let profilerName = (data.profiler_first_name && data.profiler_last_name) ?
                `${data.profiler_first_name} ${data.profiler_last_name}` :
                "Not Assigned";

            $("#reviewProfiler").text(profilerName);

            if (data.uploaded_format) {
                displayUploadedFiles(data.uploaded_format);
            } else {
                $("#reviewUploadedFormat").html("<p>No submitted file available</p>");
            }

            $("#reviewSubmissionModal").modal("show");
        },
        error: function() {
            $("#reviewProfiler").text("Not Assigned");
            alert("Failed to load request details.");
        }
    });

    $(document).ready(function() {
        $(".filter-btn").on("click", function() {
            let filter = $(this).data("filter");

            // Remove 'active' class from all buttons and add to clicked one
            $(".filter-btn").removeClass("active");
            $(this).addClass("active");

            // Show or hide rows based on filter
            if (filter === "all") {
                $(".request-row").show();
            } else {
                $(".request-row").each(function() {
                    let rowStatus = $(this).data("status");

                    if (rowStatus === filter) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }
        });
    });
});