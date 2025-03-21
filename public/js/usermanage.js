console.log("✅ usermanage.js has been loaded!");

// Initialize Bootstrap tooltips (Add Hover Effect & Tooltip for Buttons)
$(document).ready(function () {
    $("[title]").tooltip();

    let filters = document.getElementById("filter-buttons");

    // Function to expand filters
    function expandFilters() {
        filters.style.display = "flex";
        filters.style.opacity = "0";
        filters.style.maxWidth = "900px";

        let opacity = 0;
        let slide = -20;
        function animateExpand() {
            if (opacity >= 1) {
                filters.style.opacity = "1";
                filters.style.transform = "translateX(0)";
                localStorage.setItem("filtersOpen", "true");
                return;
            }
            opacity += 0.1;
            slide += 2;
            filters.style.opacity = opacity;
            filters.style.transform = `translateX(${slide}px)`;
            requestAnimationFrame(animateExpand);
        }
        animateExpand();
    }

    // Function to collapse filters
    function collapseFilters() {
        let opacity = 1;
        let slide = 0;
        function animateCollapse() {
            if (opacity <= 0) {
                filters.style.maxWidth = "0px";
                filters.style.display = "none";
                localStorage.setItem("filtersOpen", "false");
                return;
            }
            opacity -= 0.1;
            slide -= 2;
            filters.style.opacity = opacity;
            filters.style.transform = `translateX(${slide}px)`;
            requestAnimationFrame(animateCollapse);
        }
        animateCollapse();
    }

    // Toggle button click event
    document.getElementById("toggle-btn").addEventListener("click", function () {
        let isOpen = filters.style.maxWidth !== "0px" && filters.style.maxWidth !== "";
        if (!isOpen) {
            expandFilters();
        } else {
            collapseFilters();
        }
    });

    // Restore state on page load
    if (localStorage.getItem("filtersOpen") === "true") {
        expandFilters();
    } else {
        filters.style.display = "none";
        filters.style.maxWidth = "0px";
    }

    let table = $("#requestTable").DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        lengthMenu: [5, 10, 25, 50],
        order: [[7, "desc"]],
        columnDefs: [{ orderable: false, targets: [8] }]
    });

        // Initialize DataTable for completed requests (Table 2)
    let table2 = $("#completedRequestsTable").DataTable({
        paging: true,             // Enable pagination
        searching: true,          // Enable search
        ordering: true,           // Enable column ordering
        info: true,               // Show table info
        lengthMenu: [5, 10, 25, 50], // Set page length options
        order: [[7, "desc"]],     // Order by "Updated Time" column descending
        autoWidth: false // ✅ Prevent automatic resizing

    });

    setTimeout(function () {
        let searchContainer = $(".dataTables_filter"); // Get the search bar container

        // Create the Add Request Button
        let addButton = `
            <button class="btn btn-success btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#addRequestModal">
                <i class="bi bi-plus-circle"></i> Add Request
            </button>
        `;

        searchContainer.append(addButton); // Append the button next to search bar
    }, );


    // ✅ Load last viewed counts and dismissed states from localStorage
    let lastViewedCounts = JSON.parse(localStorage.getItem('lastViewedCounts')) || {
        all: 0,
        pending: 0,
        inProgress: 0,
        underReview: 0,
        needsRevision: 0,
        completed: 0
    };

    let dismissedBadges = JSON.parse(localStorage.getItem('dismissedBadges')) || {
        all: false,
        pending: false,
        inProgress: false,
        underReview: false,
        needsRevision: false,
        completed: false
    };

    // ✅ Preserve Filter State
    let savedFilter = localStorage.getItem("selectedFilter") || "all";
    applyFilter(savedFilter);

    $(".filter-btn").on("click", function () {
        let filter = $(this).data("filter");
        localStorage.setItem("selectedFilter", filter);
        handleBadgeDismissal(filter);
        applyFilter(filter);
    });

    function applyFilter(filter) {
        $(".filter-btn").removeClass("active");
        $(".filter-btn[data-filter='" + filter + "']").addClass("active");
    
        if (filter === "all") {
            table.search("").columns().search("").draw();
            $("#requestTable").show(); 
            $("#requestTable_wrapper").show(); // ✅ Show table 1 pagination
            $("#completedRequestsTable").hide(); 
            $("#completedRequestsTable_wrapper").hide(); // ✅ Hide table 2 pagination
        } else if (filter === "Completed") {
            $("#requestTable").hide(); 
            $("#requestTable_wrapper").hide(); // ✅ Hide table 1 pagination
            $("#completedRequestsTable").show(); 
            $("#completedRequestsTable_wrapper").show(); // ✅ Show table 2 pagination
        } else {
            table.column(1).search("^" + filter + "$", true, false).draw();
            $("#requestTable").show(); 
            $("#requestTable_wrapper").show(); // ✅ Show table 1 pagination
            $("#completedRequestsTable").hide(); 
            $("#completedRequestsTable_wrapper").hide(); // ✅ Hide table 2 pagination
        }
    }
    

    function updateStatusCounts() {
        let allCount = table.rows().count();

        // ✅ Extract clean text from status column
        let statuses = table.column(1).data().toArray().map(status => {
            return $("<div>").html(status).text().trim(); // Handle HTML or plain text
        });

        let pendingCount = statuses.filter(status => status === "Pending").length;
        let inProgressCount = statuses.filter(status => status === "In Progress").length;
        let underReviewCount = statuses.filter(status => status === "Under Review").length;
        let needsRevisionCount = statuses.filter(status => status === "Needs Revision").length;
        let completedCount = table2.rows().count(); // ✅ Use table2 instead of completedTable

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


    function handleBadgeDismissal(filter) {
        let key = filter.replace(/\s+/g, '-').toLowerCase();

        // ✅ Mark badge as dismissed
        dismissedBadges[key] = true;
        $(`#new-badge-${key}`).hide();

        // ✅ Update last viewed counts after dismissal
        switch (filter) {
            case "all":
                lastViewedCounts.all = table.rows().count();
                break;
            case "Pending":
                lastViewedCounts.pending = table.rows().data().toArray()
                    .filter(row => $(row[1]).text().trim() === "Pending").length;
                break;
            case "In Progress":
                lastViewedCounts.inProgress = table.rows().data().toArray()
                    .filter(row => $(row[1]).text().trim() === "In Progress").length;
                break;
            case "Under Review":
                lastViewedCounts.underReview = table.rows().data().toArray()
                    .filter(row => $(row[1]).text().trim() === "Under Review").length;
                break;
            case "Needs Revision":
                lastViewedCounts.needsRevision = table.rows().data().toArray()
                    .filter(row => $(row[1]).text().trim() === "Needs Revision").length;
                break;
            case "Completed":
                lastViewedCounts.completed = table2.rows().data().toArray()
                    .filter(row => $(row[1]).text().trim() === "Completed").length;
                break;
        }

        // ✅ Save to localStorage
        localStorage.setItem('lastViewedCounts', JSON.stringify(lastViewedCounts));
        dismissedBadges.pending = false;
        localStorage.setItem('dismissedBadges', JSON.stringify(dismissedBadges));
    }
    

    updateStatusCounts();

    // ✅ Update counts on table redraw
    table.on('draw', updateStatusCounts);
});


//Function for File Removal
document
    .getElementById("attachment")
    .addEventListener("change", function (event) {
        const fileList = document.getElementById("fileList");
        fileList.innerHTML = "";

        Array.from(event.target.files).forEach((file, index) => {
            const fileDiv = document.createElement("div");
            fileDiv.classList.add(
                "d-flex",
                "align-items-center",
                "border",
                "p-2",
                "mb-1",
                "rounded"
            );
            fileDiv.innerHTML = `
                <span class="me-auto">${file.name} (${(
                    file.size / 1024
                ).toFixed(2)} KB)</span>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeFile(${index})">
                    <i class="bi bi-x"></i>
                </button>
            `;
            fileDiv.dataset.index = index;
            fileList.appendChild(fileDiv);
        });
    });

function removeFile(index) {
    const fileList = document.getElementById("attachment");
    const newFiles = Array.from(fileList.files).filter((_, i) => i !== index);

    const dataTransfer = new DataTransfer();
    newFiles.forEach((file) => dataTransfer.items.add(file));
    fileList.files = dataTransfer.files;

    document.querySelector(`div[data-index="${index}"]`).remove();
}

// View and Edit Form
$(document).ready(function () {
    // Handle View Button Click
    $(document).on("click", ".viewRequestBtn", function () {
        let requestId = $(this).data("id");
        let firstName = $(this).attr("data-first-name");
        let lastName = $(this).attr("data-last-name");
        let nationality = $(this).attr("data-nationality");
        let location = $(this).attr("data-location");
        let format = $(this).attr("data-format");
        let attachments = $(this).attr("data-attachments");
        let dateCreated = $(this).data("date-created");
        let status = $(this).closest("tr").find("td:eq(1)").text().trim(); // Get status from table

        // Debugging: Ensure status is correctly fetched
        console.log("Opening View & Edit Modal. Status:", status);

        // Populate Form Fields
        $("#request_id").val(requestId);
        $("#edit_first_name").val(firstName);
        $("#edit_last_name").val(lastName);
        $("#edit_nationality").val(nationality);
        $("#edit_location").val(location);
        $("#edit_format").val(format);
        $(".modal-body span.date-created").text(dateCreated || "N/A");

        // Get requested by name from the button data attributes
        let requestedBy = $(this).attr("data-requested-by");
        $("#requestedBy").text(requestedBy ? requestedBy : "Unknown User");

        // Allow editing only if status is "Pending" or "Under Review"
        let isEditable = ["Pending", "Under Review"].includes(status);
        
        $("#viewRequestModal input, #viewRequestModal select").prop("disabled", !isEditable);

        // Populate Existing Attachments
        let attachmentsHtml = "<h6>Existing Attachments:</h6>";
        let attachmentsArray = attachments ? JSON.parse(attachments) : [];

        if (attachmentsArray.length > 0) {
            attachmentsArray.forEach((file) => {
                let fileName = file.split("/").pop();
                let fileUrl = `/storage/attachments/${fileName}`;
                attachmentsHtml += `
                <div class="d-flex align-items-center border p-2 mb-1 rounded">
                    <a href="${fileUrl}" target="_blank" class="me-auto">${fileName}</a>
                    <button type="button" class="btn btn-sm btn-danger delete-attachment-btn"
                        data-request-id="${requestId}" data-file-name="${file}"
                        ${!isEditable ? "disabled" : ""}>
                        <i class="bi bi-trash"></i>
                    </button>
                </div>`;
            });

            // Disable file upload if editing is not allowed
            $("#edit_attachments").prop("disabled", !isEditable);
        } else {
            attachmentsHtml += "<p>No attachments found.</p>";
            $("#edit_attachments").prop("disabled", !isEditable);
        }

        $("#existingAttachments").html(attachmentsHtml);

        // Prevent file selection if disabled
        $("#edit_attachments").off("change").on("change", function () {
            if ($(this).prop("disabled")) {
                alert("You can only have one attachment. Please remove the existing file before uploading a new one.");
                $(this).val(""); // Clear the selected file
            }
        });

        // Open modal
        $("#viewRequestModal").modal("show");
    });

    // Handle File Deletion
    $(document).on("click", ".delete-attachment-btn", function () {
        let requestId = $(this).data("request-id");
        let fileName = $(this).data("file-name");

        if (confirm("Are you sure you want to delete this attachment?")) {
            $.ajax({
                url: `/requests/${requestId}/delete-attachment`,
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    file_name: fileName,
                },
                success: function (response) {
                    if (response.success) {
                        alert("Attachment deleted successfully!");

                        // Remove the deleted attachment from UI
                        $(`button[data-file-name='${fileName}']`).closest("div").remove();

                        // Check if there are remaining attachments
                        if ($("#existingAttachments").children().length === 1) {
                            $("#existingAttachments").html("<p>No attachments found.</p>");
                        }

                        // Enable file upload input since the attachment is deleted
                        $("#edit_attachments").prop("disabled", false);
                    } else {
                        alert("Failed to delete attachment.");
                    }
                },
                error: function (xhr) {
                    console.error("Error deleting file:", xhr.responseText);
                    alert("An error occurred. Please try again.");
                },
            });
        }
    });

    // Handle Form Submission
    $("#updateRequestForm").on("submit", function (e) {
        e.preventDefault();

        let requestId = $("#request_id").val().trim();
        if (!requestId) {
            alert("Error: Missing request ID.");
            return;
        }

        let formData = new FormData(this);
        formData.append("_method", "PUT");

        $.ajax({
            url: "/requests/update/" + requestId,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $("#viewRequestModal").modal("hide");
                alert("Request updated successfully!");
                location.reload();
            },
            error: function (xhr) {
                console.error("Error:", xhr.responseText);
                alert("Failed to update the request. Please try again.");
            },
        });
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

    let uploadedFilesHtml = "<h6>Submitted Files:</h6>";
    if (uploadedFilesArray.length > 0) {
        uploadedFilesArray.forEach((file) => {
            let fileName = file.split("/").pop();
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
        uploadedFilesHtml += "<p>No submitted files available.</p>";
    }

    $("#reviewUploadedFormat").html(uploadedFilesHtml);
}

//  Set CSRF Token Globally / Wait for document to be fully loaded
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    $(document).ready(function () {
        // Handle Review Submission Modal Opening
        $(document).on("click", ".reviewSubmissionBtn", function (event) {
            event.preventDefault();
            let requestId = $(this).data("id");
            let row = $(this).closest("tr");
            let status = row.find("td[data-status]").data("status"); // Use data attribute

            $.ajax({
                url: `/requests/${requestId}/details`,
                type: "GET",
                success: function (data) {
                    $("#reviewSubmissionModal").data("request-id", requestId);
                    $("#reviewRequester").text(`${data.First_Name} ${data.Last_Name}`);
                    $("#reviewFormat").text(data.Format);

                    let profilerName =
                        data.profiler_first_name && data.profiler_last_name
                            ? `${data.profiler_first_name} ${data.profiler_last_name}`
                            : "Not Assigned";
                    $("#reviewProfiler").text(profilerName);

                    if (data.uploaded_format) {
                        displayUploadedFiles(data.uploaded_format);
                    } else {
                        $("#reviewUploadedFormat").html("<p>No submitted file available</p>");
                    }

                    // ✅ Enable/Disable buttons based on status
                    const isUnderReview = (status === "Under Review");
                    $("#markAsDoneBtn, #openFeedbackModalBtn")
                        .prop("disabled", !isUnderReview)
                        .toggleClass("disabled", !isUnderReview);

                    $("#reviewSubmissionModal").modal("show");
                },
                error: function () {
                    alert("Failed to load request details.");
                },
            });
        });
    });

    // Handle "Mark as Done" Click
    $("#markAsDoneBtn").on("click", function () {
        let requestId = $("#reviewSubmissionModal").data("request-id");

        if (!requestId) {
            alert("Error: Missing request ID.");
            return;
        }

        $.post(`/requests/${requestId}/complete`, {
            status: "Completed",
        })
            .done(function () {
                alert("Request marked as complete!");
                $("#reviewSubmissionModal").modal("hide");
                location.reload();
            })
            .fail(function () {
                alert("Error updating request status.");
            });
    });

    // Open Feedback Modal
    $("#openFeedbackModalBtn").on("click", function () {
        $("#reviewSubmissionModal").modal("hide"); // Hide the main modal
        $("#feedbackModal").modal("show");
    });

    // Handle "Send for Revision" Click
    $("#sendForRevisionBtn").on("click", function () {
        let requestId = $("#reviewSubmissionModal").data("request-id");
        let feedback = $("#feedbackMessage").val();

        if (!requestId) {
            alert("Error: Missing request ID.");
            return;
        }

        if (!feedback.trim()) {
            alert("Please provide feedback before sending.");
            return;
        }

        $.post(`/requests/${requestId}/revise`, {
            status: "Needs Revision",
            feedback: feedback,
        })
            .done(function () {
                alert("Request sent back for revision.");
                $("#feedbackModal").modal("hide");
                location.reload();
            })
            .fail(function () {
                alert("Error updating request status.");
            });
    });
});

// Load Profiler Name in the Review Submission Modal
$(document).on("click", ".reviewSubmissionBtn", function () {
    let requestId = $(this).data("id");

    $.ajax({
        url: `/requests/${requestId}/details`,
        type: "GET",
        success: function (data) {
            $("#reviewRequester").text(`${data.First_Name} ${data.Last_Name}`);
            $("#reviewFormat").text(data.Format);

            let profilerName =
                data.profiler_first_name && data.profiler_last_name
                    ? `${data.profiler_first_name} ${data.profiler_last_name}`
                    : "Not Assigned";

            $("#reviewProfiler").text(profilerName);

            if (data.uploaded_format) {
                displayUploadedFiles(data.uploaded_format);
            } else {
                $("#reviewUploadedFormat").html(
                    "<p>No submitted file available</p>"
                );
            }

            $("#reviewSubmissionModal").modal("show");
        },
        error: function () {
            $("#reviewProfiler").text("Not Assigned");
            alert("Failed to load request details.");
        },
    });

    $(document).ready(function () {
        $(".filter-btn").on("click", function () {
            let filter = $(this).data("filter");

            // Remove 'active' class from all buttons and add to clicked one
            $(".filter-btn").removeClass("active");
            $(this).addClass("active");

            // Show or hide rows based on filter
            if (filter === "all") {
                $(".request-row").show();
            } else {
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
    });
});

// Handle Review Submission Modal Buttons if the request is completed
$(document).ready(function () {
    function updateButtonsBasedOnStatus(status) {
        let disableButtons = ["Pending", "In Progress", "Needs Revision", "Completed"].includes(status);

        $("#markAsDoneBtn, #openFeedbackModalBtn").prop("disabled", disableButtons);
        console.log("Status:", status, "| Buttons Disabled:", disableButtons);
    }

    // When clicking the Review Submission button, get the status and update buttons
    $(".reviewSubmissionBtn").on("click", function () {
        let status = $(this).closest("tr").find("td:eq(1)").text().trim(); // Get status from the table
        console.log("Opening modal. Status:", status);
    
        // Set the status inside the modal to ensure consistency
        $("#reviewSubmissionModal").find(".status-text").text(status);
    
        // Open modal
        $("#reviewSubmissionModal").modal("show");
    
        // Update buttons based on status
        updateButtonsBasedOnStatus(status);
    });    

    // Ensure buttons stay correct when modal is reopened
    $("#reviewSubmissionModal").on("shown.bs.modal", function () {
        let status = $("#reviewSubmissionModal").find(".status-text").text().trim(); // Get status from modal content

        console.log("Modal opened. Status:", status);

        updateButtonsBasedOnStatus(status);
    });
});

