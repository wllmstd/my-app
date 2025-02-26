$(document).ready(function () {
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
                    let fileName = file.split("/").pop();
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
    $(document).on("click", ".acceptRequestBtn", function () {
        let requestId = $(this).attr("data-id"); // ✅ Correctly fetch ID

        if (!requestId) {
            alert("Request ID not found. Please try again.");
            return;
        }

        $.ajax({
            url: "/requests/accept/" + requestId, // ✅ Correct Route
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"), // ✅ CSRF Token
            },
            data: {
                status: "In Progress",
            }, // ✅ Send updated status
            success: function (response) {
                alert(
                    "Request accepted successfully! Status updated to In Progress."
                );
                location.reload(); // ✅ Refresh Page to Update Status

                // ✅ Update status in table dynamically
                let row = $(
                    "button.uploadBtn[data-id='" + requestId + "']"
                ).closest("tr");
                row.find("td:nth-child(2)").text("In Progress"); // Update status column

                // ✅ Enable Upload Button and Change Style
                let uploadBtn = row.find(".uploadBtn");
                uploadBtn
                    .removeAttr("disabled")
                    .removeClass("btn-secondary")
                    .addClass("btn-success");
            },
            error: function (xhr) {
                console.error("Error:", xhr.responseText);
                alert(
                    "Failed to accept request. Please check console for details."
                );
            },
        });
    });

    $(".filter-btn").on("click", function () {
        let filter = $(this).data("filter");

        // Remove 'active' class from all buttons and add to clicked one
        $(".filter-btn").removeClass("active");
        $(this).addClass("active");

        // Show or hide tables and their headings based on filter
        if (filter === "all") {
            $("#acceptedRequestsTable, #pendingRequestsTable").show();
            $("#acceptedRequestsHeading, #pendingRequestsHeading").show(); // Show headings
            $(".request-row, .pending-row").show();
        } else if (filter === "Pending") {
            $("#acceptedRequestsTable, #acceptedRequestsHeading").hide(); // Hide Table 1 & Heading
            $("#pendingRequestsTable, #pendingRequestsHeading").show(); // Show Table 2 & Heading
        } else {
            $("#acceptedRequestsTable, #acceptedRequestsHeading").show(); // Show Table 1 & Heading
            $("#pendingRequestsTable, #pendingRequestsHeading").hide(); // Hide Table 2 & Heading

            $(".request-row").each(function () {
                let rowStatus = $(this).data("status");

                if (rowStatus === filter) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    });

    // ✅ Open Upload Modal & Load Existing Files
    $(".openUploadModalBtn").on("click", function () {
        selectedRequestId = $(this).data("id");
        let existingFiles = $(this).data("files") || [];

        $("#upload_request_id").val(selectedRequestId); // Set ID

        let filesContainer = $("#existingUploadedFiles");
        filesContainer.html(""); // Clear previous content

        if (existingFiles.length > 0) {
            existingFiles.forEach((file) => {
                let fileUrl = `/storage/uploads/${file}`;
                let fileHtml = `
                <div class="d-flex align-items-center border p-2 mb-1 rounded">
                    <a href="${fileUrl}" target="_blank" class="me-auto">${file}</a>
                    <button class="btn btn-sm btn-danger deleteFileBtn" data-id="${selectedRequestId}" data-file="${file}">
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

    // ✅ Upload Files and Show Confirmation Modal
    // ✅ Upload Files and Show Forward Confirmation Modal
    $("#uploadForm").on("submit", function (e) {
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
            success: function (response) {
                // ✅ Hide Upload Modal
                $("#uploadModal").modal("hide");

                // ✅ Ensure selectedRequestId is set
                selectedRequestId = requestId;

                // ✅ Open Forward Confirmation Modal
                setTimeout(() => {
                    $("#forwardConfirmModal").modal("show");
                }, 500); // Small delay to ensure smooth transition
            },
            error: function (xhr) {
                console.error("Error:", xhr.responseText);
                alert("File upload failed. Please try again.");
            },
        });
    });

    // ✅ Forward Request After Confirming
    $("#confirmForwardBtn").on("click", function () {
        if (!selectedRequestId) {
            alert("Error: Request ID not found.");
            return;
        }

        $.ajax({
            url: "/requests/forward/" + selectedRequestId,
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                alert(response.success);
                $("#forwardConfirmModal").modal("hide");

                // ✅ Preserve the current filter before reload
                let currentFilter = localStorage.getItem("selectedFilter");

                setTimeout(() => {
                    localStorage.setItem("selectedFilter", currentFilter);
                    location.reload();
                }, 500);
            },
            error: function (xhr) {
                console.error("Error:", xhr.responseText);
                alert("Failed to forward request. Please try again.");
            },
        });
    });

    // ✅ Delete File Functionality
    // ✅ Delete File Functionality (Fixed)
    $(document).on("click", ".deleteFileBtn", function () {
        let fileName = $(this).data("file"); // Get file name
        let requestId = $(this).data("id"); // Get request ID

        // ✅ Show confirmation prompt before deleting
        if (!confirm("Are you sure you want to delete this file?")) return;

        $.ajax({
            url: "/requests/delete-file/" + requestId,
            type: "POST",
            data: {
                file_name: fileName,
                _token: $('meta[name="csrf-token"]').attr("content"), // CSRF token
            },
            success: function (response) {
                // ✅ Remove deleted file from UI (inside modal)
                $(`button.deleteFileBtn[data-file='${fileName}']`)
                    .closest("div")
                    .remove();

                // ✅ If no more files left, show "No uploaded files yet."
                let remainingFiles = $("#existingUploadedFiles").children()
                    .length;
                if (remainingFiles === 0) {
                    $("#existingUploadedFiles").html(
                        "<p>No uploaded files yet.</p>"
                    );
                }

                // ✅ Update table row UI dynamically
                let filesContainer = $("#uploadedFormat-" + requestId);
                filesContainer.html(
                    response.files
                        .map(
                            (file) =>
                                `<div class="d-flex align-items-center border p-2 mb-1 rounded">
                    <a href="/storage/uploads/${file}" target="_blank">${file}</a>
                    <button class="btn btn-sm btn-danger deleteFileBtn" data-id="${requestId}" data-file="${file}">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>`
                        )
                        .join("")
                );
            },
            error: function (xhr) {
                console.error("Error:", xhr.responseText);
                alert("Failed to delete file. Please try again.");
            },
        });
    });

    // ✅ Restore the last selected filter on page load
    let savedFilter = localStorage.getItem("selectedFilter");
    if (savedFilter) {
        $(".filter-btn[data-filter='" + savedFilter + "']").click();
    } else {
        $(".filter-btn[data-filter='all']").click(); // Default to "All Tables"
    }

    // ✅ Save filter selection before making changes
    $(".filter-btn").on("click", function () {
        let filter = $(this).data("filter");
        localStorage.setItem("selectedFilter", filter); // Store filter in localStorage
    });

    // ✅ Function to Reload Page While Keeping the Filter
    function reloadPageWithFilter() {
        let currentFilter = localStorage.getItem("selectedFilter") || "all";
        $(".filter-btn[data-filter='" + currentFilter + "']").click(); // ✅ Reapply Filter
    }
});

// Delete Attachment Function
function deleteAttachment(requestId, file) {
    if (confirm("Are you sure you want to delete this attachment?")) {
        let deletedFiles = $("#deletedFilesInput").val()
            ? JSON.parse($("#deletedFilesInput").val())
            : [];
        deletedFiles.push(file);
        $("#deletedFilesInput").val(JSON.stringify(deletedFiles));

        $("a[href$='" + file + "']")
            .closest("div")
            .remove(); // Remove from UI
    }
}

$(".viewAttachmentsBtn").click(function () {
    let attachments = $(this).data("attachments");
    console.log("Raw attachments data:", attachments); // Debugging output

    $("#attachmentsList").empty();

    if (!attachments || attachments === "null" || attachments.length === 0) {
        $("#attachmentsList").append(
            '<li class="list-group-item text-muted">No attachments available.</li>'
        );
        return;
    }

    let attachmentArray;
    try {
        attachmentArray =
            typeof attachments === "string"
                ? JSON.parse(attachments)
                : attachments;
    } catch (error) {
        console.error("Error parsing attachments:", error);
        $("#attachmentsList").append(
            '<li class="list-group-item text-danger">Error loading attachments.</li>'
        );
        return;
    }

    if (!Array.isArray(attachmentArray)) {
        console.error("Expected an array but got:", typeof attachmentArray);
        $("#attachmentsList").append(
            '<li class="list-group-item text-danger">Invalid attachment format.</li>'
        );
        return;
    }

    attachmentArray.forEach((file, index) => {
        let filePath = `/storage/attachments/${file}`;
        let fileName = file.split("/").pop(); // Extract file name

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
    $(".downloadAttachmentBtn").click(function () {
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

$(document).ready(function () {});
