$(document).ready(function () {
    $(document).on("click", ".viewRequestBtn", function () {
        let requestId = $(this).data("id"); // ✅ Fetch ID from View button
        let firstName = $(this).attr("data-first-name");
        let lastName = $(this).attr("data-last-name");
        let nationality = $(this).attr("data-nationality");
        let location = $(this).attr("data-location");
        let format = $(this).attr("data-format");
        let attachments = $(this).attr("data-attachments");

        // ✅ Fetch Requested By & Date Created
        let requestedBy = $(this).attr("data-requested-by");
        let dateCreated = $(this).attr("data-date-created");

        // ✅ Populate Modal Fields
        $("#request_id").val(requestId);
        $("#view_first_name").val(firstName);
        $("#view_last_name").val(lastName);
        $("#view_nationality").val(nationality);
        $("#view_location").val(location);
        $("#view_format").val(format);

        // ✅ Populate Requested By & Date Created
        $("#view_requested_by").text(requestedBy);
        $("#view_date_created").text(dateCreated);

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
            success: function () {
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

    // Open Upload Modal & Load Existing Files
    $(document).ready(function () {
        $(document).on("click", ".openUploadModalBtn", function () {
            let button = $(this); // The clicked button
            let requestId = button.data("id");
            let requestedBy = button.data("requested-by");
            let applicantName = button.data("applicant-name");
            let requestedFormat = button.data("requested-format");
            let existingFiles = button.data("files") || [];
            let feedback = button.data("feedback") || "No feedback provided"; // ✅ Get Feedback
            let status = button.data("status"); // ✅ Get status from the button

            //Debugging: Check if data attributes exist
            console.log("Request ID:", requestId);
            console.log("Requested By:", requestedBy);
            console.log("Applicant Name:", applicantName);
            console.log("Requested Format:", requestedFormat);
            console.log("Existing Files:", existingFiles);

            // If requestId is undefined, log an error and prevent modal from opening
            if (!requestId) {
                console.error(
                    "Error: requestId is not defined in the button attributes."
                );
                return;
            }

            // Populate Modal Fields
            $("#upload_request_id").val(requestId);
            $("#requestedBy").text(requestedBy);
            $("#applicantName").text(applicantName);
            $("#requestedFormat").text(requestedFormat);
            $("#uploadMessage").text(feedback); // ✅ Set feedback as plain text

            if (status === "Needs Revision") {
                $("#feedbackSection").removeClass("d-none"); // ✅ Show section
                $("#uploadMessage").text(feedback);
            } else {
                $("#feedbackSection").addClass("d-none"); // ✅ Hide section
            }

            //Populate Existing Files
            let filesContainer = $("#existingUploadedFiles");
            filesContainer.html(""); // Clear previous content

            if (Array.isArray(existingFiles) && existingFiles.length > 0) {
                existingFiles.forEach((file) => {
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

            //Open Modal
            $("#uploadModal").modal("show");
        });
    });

    //fetch request details
    $(document).on("click", ".openUploadModalBtn", function () {
        let requestId = $(this).data("id");

        $.ajax({
            url: `/requests/${requestId}/full-details`,
            type: "GET",
            success: function (data) {
                console.log("Fetched Data:", data);

                //Populate the requested by field
                if (
                    data.requested_by_first_name &&
                    data.requested_by_last_name
                ) {
                    $("#requestedBy").text(
                        `${data.requested_by_first_name} ${data.requested_by_last_name}`
                    );
                } else {
                    $("#requestedBy").text("Unknown User");
                }

                // Populate the other modal fields
                $("#applicantName").text(
                    `${data.First_Name} ${data.Last_Name}`
                );
                $("#requestedFormat").text(
                    data.Format || "No format specified"
                );

                $("#upload_request_id").val(requestId);

                //Ensure the modal updates before opening
                setTimeout(() => {
                    $("#uploadModal").modal("show");
                }, 300); // Small delay to ensure updates reflect
            },
            error: function () {
                alert("Failed to load request details.");
            },
        });
    });
});

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
        success: function () {
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
            let remainingFiles = $("#existingUploadedFiles").children().length;
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

$(document).ready(function () {
    // ✅ Initialize DataTables
    $("#userTable").DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        lengthMenu: [5, 10, 25, 50],
    });

    let acceptedTable = $("#acceptedRequestsTable").DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        lengthMenu: [5, 10, 25, 50],
        columnDefs: [{ orderable: false, targets: [9] }],
    });

    let pendingTable = $("#pendingRequestsTable").DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        lengthMenu: [5, 10, 25, 50],
        columnDefs: [{ orderable: false, targets: [8] }],
    });

        // ✅ Initialize Completed Requests Table
    let completedTable = $('#completedRequestsTable').DataTable({
            paging: true,
            searching: true,
            lengthChange: true,
            pageLength: 5,
            ordering: true,
            lengthMenu: [5, 10, 25, 50],
        });
    

    // ✅ Load last viewed counts and dismissed states from localStorage
    let lastViewedCounts = JSON.parse(
        localStorage.getItem("lastViewedCounts")
    ) || {
        all: 0,
        pending: 0,
        inProgress: 0,
        underReview: 0,
        needsRevision: 0,
        completed: 0,
    };

    let dismissedBadges = JSON.parse(
        localStorage.getItem("dismissedBadges")
    ) || {
        all: false,
        pending: false,
        inProgress: false,
        underReview: false,
        needsRevision: false,
        completed: false,
    };

    // ✅ Apply filter based on saved state
    let savedFilter = localStorage.getItem("selectedFilter") || "all";
    applyFilter(savedFilter);

    function applyFilter(filter) {
        $(".filter-btn").removeClass("active");
        $(".filter-btn[data-filter='" + filter + "']").addClass("active");

        console.log("Applying Filter:", filter);

        
    if (filter === "all") {
        $("#acceptedRequestsTable, #acceptedRequestsHeading").show(); 
        $("#pendingRequestsTable, #pendingRequestsHeading").hide(); 
        $("#completedRequestsTable, #completedRequestsHeading").hide(); 

        $("#acceptedRequestsTable_wrapper").show();
        $("#pendingRequestsTable_wrapper").hide();
        $("#completedRequestsTable_wrapper").hide();

        acceptedTable.search("").columns().search("").draw();
        pendingTable.search("").columns().search("").draw();
        completedTable.search("").columns().search("").draw();
    } else if (filter === "Pending") {
        $("#acceptedRequestsTable, #acceptedRequestsHeading").hide();
        $("#pendingRequestsTable, #pendingRequestsHeading").show(); 
        $("#completedRequestsTable, #completedRequestsHeading").hide(); 

        $("#acceptedRequestsTable_wrapper").hide();
        $("#pendingRequestsTable_wrapper").show();
        $("#completedRequestsTable_wrapper").hide();

        pendingTable.column(1).search(filter, true, false).draw();
    } else if (filter === "Completed") {
        $("#acceptedRequestsTable, #acceptedRequestsHeading").hide(); 
        $("#pendingRequestsTable, #pendingRequestsHeading").hide(); 
        $("#completedRequestsTable, #completedRequestsHeading").show(); 

        $("#acceptedRequestsTable_wrapper").hide();
        $("#pendingRequestsTable_wrapper").hide();
        $("#completedRequestsTable_wrapper").show();

        completedTable.draw(); // ✅ No need to search, just refresh the table
    } else {
        $("#acceptedRequestsTable, #acceptedRequestsHeading").show(); 
        $("#pendingRequestsTable, #pendingRequestsHeading").hide(); 
        $("#completedRequestsTable, #completedRequestsHeading").hide(); 

        $("#acceptedRequestsTable_wrapper").show();
        $("#pendingRequestsTable_wrapper").hide();
        $("#completedRequestsTable_wrapper").hide();

        acceptedTable.column(1).search(filter, true, false).draw();
    }

    setTimeout(updateStatusCounts, 300);
        pendingTable.on("draw", updateStatusCounts);
        acceptedTable.on("draw", updateStatusCounts);

        // ✅ Update Status Counts After Applying Filter
        updateStatusCounts();
    }

    function updateStatusCounts() {
        let allCount =
            acceptedTable.rows().count() + pendingTable.rows().count();
        let pendingCount = pendingTable
            .rows()
            .data()
            .toArray()
            .filter((row) => $(row[1]).text().trim() === "Pending").length;
        let inProgressCount = acceptedTable
            .rows()
            .data()
            .toArray()
            .filter((row) => $(row[1]).text().trim() === "In Progress").length;
        let underReviewCount = acceptedTable
            .rows()
            .data()
            .toArray()
            .filter((row) => $(row[1]).text().trim() === "Under Review").length;
        let needsRevisionCount = acceptedTable
            .rows()
            .data()
            .toArray()
            .filter(
                (row) => $(row[1]).text().trim() === "Needs Revision"
            ).length;
        let completedCount = completedTable
            .rows()
            .data()
            .toArray()
            .filter((row) => $(row[1]).text().trim() === "Completed").length;

        // ✅ Update button text with counts
        // ✅ Update button text with counts
        $("#count-all").text(`(${allCount})`);
        $("#count-pending").text(`(${pendingCount})`);
        $("#count-in-progress").text(`(${inProgressCount})`);
        $("#count-under-review").text(`(${underReviewCount})`);
        $("#count-needs-revision").text(`(${needsRevisionCount})`);
        $("#count-completed").text(`(${completedCount})`);
        
        // ✅ Badge Logic - Show how many are new
        let newAll = allCount - lastViewedCounts.all;
        let newPending = pendingCount - lastViewedCounts.pending;
        let newInProgress = inProgressCount - lastViewedCounts.inProgress;
        let newUnderReview = underReviewCount - lastViewedCounts.underReview;
        let newNeedsRevision = needsRevisionCount - lastViewedCounts.needsRevision;
        let newCompleted = completedCount - lastViewedCounts.completed;

        // 🏷️ If there's an increase, show the count in the badge
        $("#new-badge-all").text(newAll > 0 ? `${newAll} new` : "").toggle(newAll > 0);
        $("#new-badge-pending").text(newPending > 0 ? `${newPending} new` : "").toggle(newPending > 0);
        $("#new-badge-in-progress").text(newInProgress > 0 ? `${newInProgress} new` : "").toggle(newInProgress > 0);
        $("#new-badge-under-review").text(newUnderReview > 0 ? `${newUnderReview} new` : "").toggle(newUnderReview > 0);
        $("#new-badge-needs-revision").text(newNeedsRevision > 0 ? `${newNeedsRevision} new` : "").toggle(newNeedsRevision > 0);
        $("#new-badge-completed").text(newCompleted > 0 ? `${newCompleted} new` : "").toggle(newCompleted > 0);


        // ✅ Badge Logic - Based on visible count increase
        if (
            parseInt($("#count-all").text().replace(/\D/g, ""), 10) >
            lastViewedCounts.all
        ) {
            $("#new-badge-all").show();
        } else {
            $("#new-badge-all").hide();
        }

        if (
            parseInt($("#count-pending").text().replace(/\D/g, ""), 10) >
            lastViewedCounts.pending
        ) {
            $("#new-badge-pending").show();
        } else {
            $("#new-badge-pending").hide();
        }

        if (
            parseInt($("#count-in-progress").text().replace(/\D/g, ""), 10) >
            lastViewedCounts.inProgress
        ) {
            $("#new-badge-in-progress").show();
        } else {
            $("#new-badge-in-progress").hide();
        }

        if (
            parseInt($("#count-under-review").text().replace(/\D/g, ""), 10) >
            lastViewedCounts.underReview
        ) {
            $("#new-badge-under-review").show();
        } else {
            $("#new-badge-under-review").hide();
        }

        if (
            parseInt($("#count-needs-revision").text().replace(/\D/g, ""), 10) >
            lastViewedCounts.needsRevision
        ) {
            $("#new-badge-needs-revision").show();
        } else {
            $("#new-badge-needs-revision").hide();
        }

        if (
            parseInt($("#count-completed").text().replace(/\D/g, ""), 10) >
            lastViewedCounts.completed
        ) {
            $("#new-badge-completed").show();
        } else {
            $("#new-badge-completed").hide();
        }
    }

    $(".filter-btn").on("click", function () {
        let filter = $(this).data("filter");
        let key = filter.replace(/\s+/g, "-").toLowerCase();

        // ✅ Save the selected filter to localStorage
        localStorage.setItem("selectedFilter", filter);

        // ✅ Hide the badge when clicked
        $(`#new-badge-${key}`).hide();
        dismissedBadges[key] = true;

        // ✅ Update lastViewedCounts **AFTER** badge comparison
        switch (filter) {
            case "all":
                lastViewedCounts.all =
                    acceptedTable.rows().count() + pendingTable.rows().count();
                break;
            case "Pending":
                lastViewedCounts.pending = pendingTable
                    .rows()
                    .data()
                    .toArray()
                    .filter(
                        (row) => $(row[1]).text().trim() === "Pending"
                    ).length;
                break;
            case "In Progress":
                lastViewedCounts.inProgress = acceptedTable
                    .rows()
                    .data()
                    .toArray()
                    .filter(
                        (row) => $(row[1]).text().trim() === "In Progress"
                    ).length;
                break;
            case "Under Review":
                lastViewedCounts.underReview = acceptedTable
                    .rows()
                    .data()
                    .toArray()
                    .filter(
                        (row) => $(row[1]).text().trim() === "Under Review"
                    ).length;
                break;
            case "Needs Revision":
                lastViewedCounts.needsRevision = acceptedTable
                    .rows()
                    .data()
                    .toArray()
                    .filter(
                        (row) => $(row[1]).text().trim() === "Needs Revision"
                    ).length;
                break;
            case "Completed":
                lastViewedCounts.completed = completedTable
                    .rows()
                    .data()
                    .toArray()
                    .filter(
                        (row) => $(row[1]).text().trim() === "Completed"
                    ).length;
                break;
        }

        // ✅ Save state to localStorage
        localStorage.setItem(
            "lastViewedCounts",
            JSON.stringify(lastViewedCounts)
        );
        localStorage.setItem(
            "dismissedBadges",
            JSON.stringify(dismissedBadges)
        );

        applyFilter(filter);
    });

    // ✅ Force update after table redraw
    setTimeout(updateStatusCounts, 200);
    acceptedTable.on("draw", updateStatusCounts);
    pendingTable.on("draw", updateStatusCounts);
});

// Delete Attachment Function
function deleteAttachment(file) {
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

    attachmentArray.forEach((file) => {
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

    //Initialize Bootstrap tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});
