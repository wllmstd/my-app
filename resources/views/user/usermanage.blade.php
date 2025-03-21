<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Bundle (JS & Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (if needed) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js" defer></script>
    <link rel="icon" type="image/png" href="{{ asset('images/gecologo.png') }}" sizes="512x512">


</head>

<body>

    <!-- Include the navbar here -->
    @include('user.user_navbar')
    <div class="scroll-container">

        <div class="container mt-4">

        <div class="filter-container">
                <!-- Toggle Button (Always Visible) -->
                <button id="toggle-btn" class="btn btn-outline-secondary">☰ Filters</button>

                <!-- Filter Buttons (Initially Hidden) -->
                <div id="filter-buttons" class="filter-buttons">
                    <button class="btn btn-outline-dark filter-btn active" data-filter="all">
                        <div class="status-label-wrapper">
                            All Tables <span id="count-all" class="status-badge">0</span>
                        </div>
                    </button>
                    <button class="btn btn-outline-warning filter-btn" data-filter="Pending">
                        <span class="new-badge" id="new-badge-pending">NEW</span>
                        <div class="status-label-wrapper">
                            Pending Requests <span id="count-pending" class="status-badge">0</span>
                        </div>
                    </button>
                    <button class="btn btn-outline-primary filter-btn" data-filter="In Progress">
                        <span class="new-badge" id="new-badge-in-progress">NEW</span>
                        <div class="status-label-wrapper">
                            In Progress <span id="count-in-progress" class="status-badge">0</span>
                        </div>
                    </button>
                    <button class="btn btn-outline-orange filter-btn" data-filter="Under Review">
                        <span class="new-badge" id="new-badge-under-review">NEW</span>
                        <div class="status-label-wrapper">
                            Under Review <span id="count-under-review" class="status-badge">0</span>
                        </div>
                    </button>
                    <button class="btn btn-outline-danger filter-btn" data-filter="Needs Revision">
                        <span class="new-badge" id="new-badge-needs-revision">NEW</span>
                        <div class="status-label-wrapper">
                            Needs Revision <span id="count-needs-revision" class="status-badge">0</span>
                        </div>
                    </button>
                    <button class="btn btn-outline-success filter-btn" data-filter="Completed">
                        <span class="new-badge" id="new-badge-completed">NEW</span>
                        <div class="status-label-wrapper">
                            Completed <span id="count-completed" class="status-badge">0</span>
                        </div>
                    </button>
                </div>
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
                        <th>Updated Time</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($requests as $index => $request)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td data-status="{{ $request->Status }}">
                            @if($request->Status === 'Pending')
                            <span class="badge bg-warning text-dark">{{ $request->Status }}</span>
                            @elseif($request->Status === 'In Progress')
                            <span class="badge bg-primary">{{ $request->Status }}</span>
                            @elseif($request->Status === 'Under Review')
                            <span class="badge"
                                style="background-color: orange; color: black;">{{ $request->Status }}</span>
                            @elseif($request->Status === 'Needs Revision')
                            <span class="badge bg-danger">{{ $request->Status }}</span>
                            @elseif($request->Status === 'Completed')
                            <span class="badge bg-success">{{ $request->Status }}</span>
                            @else
                            <span class="badge bg-secondary">{{ $request->Status }}</span>
                            @endif
                        </td>
                        <td>{{ $request->First_Name }}</td>
                        <td>{{ $request->Last_Name }}</td>
                        <td>{{ $request->Nationality }}</td>
                        <td>{{ $request->Location }}</td>
                        <td>{{ $request->Format }}</td>
                        <td data-order="{{ \Carbon\Carbon::parse($request->Updated_Time)->timestamp }}">
                            {{ \Carbon\Carbon::parse($request->Updated_Time)->format('M j, Y, h:i A') }}
                            <br>
                            <small class="text-muted">
                                ({{ \Carbon\Carbon::parse($request->Updated_Time)->diffForHumans() }})
                            </small>
                        </td>

                        <td>
                            <button class="btn btn-primary btn-sm viewRequestBtn" data-id="{{ $request->Request_ID }}"
                                data-first-name="{{ $request->First_Name }}" data-last-name="{{ $request->Last_Name }}"
                                data-nationality="{{ $request->Nationality }}" data-location="{{ $request->Location }}"
                                data-format="{{ $request->Format }}" data-attachments="{{ $request->Attachment }}"
                                data-date-created="{{ \Carbon\Carbon::parse($request->Date_Created)->format('M j, Y, h:i A') }}"
                                data-bs-toggle="modal" data-bs-target="#viewRequestModal" title="View request details">
                                <i class="bi bi-eye"></i>
                            </button>

                            <button class="btn btn-warning btn-sm reviewSubmissionBtn"
                                data-id="{{ $request->Request_ID }}" data-first-name="{{ $request->First_Name }}"
                                data-last-name="{{ $request->Last_Name }}"
                                data-nationality="{{ $request->Nationality }}" data-location="{{ $request->Location }}"
                                data-format="{{ $request->Format }}"
                                data-uploaded-format="{{ $request->uploaded_format }}"
                                data-profiler="{{ $request->Profiler_Name }}"
                                title="Check if the submitted file meets the requested format">
                                <i class="bi bi-file-earmark-check"></i>
                            </button>


                            <button class="btn btn-danger btn-sm deleteRequestBtn" data-id="{{ $request->Request_ID }}"
                                data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete this request"
                                @if($request->Status === 'Completed') disabled @endif>
                                <i class="bi bi-trash"></i>
                            </button>

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>


            <!-- Table 2: Completed Requests -->
             
            <h2 id="completedRequestsHeading" style="display: none;">Completed Requests</h2>
            <table id="completedRequestsTable" class="table table-bordered table-striped" style="display: none;">
                <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Status</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Nationality</th>
                                <th>Location</th>
                                <th>Format</th>
                                <th>Updated Time</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($completedRequests as $index => $request)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td data-status="{{ $request->Status }}">
                                    @if($request->Status === 'Pending')
                                    <span class="badge bg-warning text-dark">{{ $request->Status }}</span>
                                    @elseif($request->Status === 'In Progress')
                                    <span class="badge bg-primary">{{ $request->Status }}</span>
                                    @elseif($request->Status === 'Under Review')
                                    <span class="badge"
                                        style="background-color: orange; color: black;">{{ $request->Status }}</span>
                                    @elseif($request->Status === 'Needs Revision')
                                    <span class="badge bg-danger">{{ $request->Status }}</span>
                                    @elseif($request->Status === 'Completed')
                                    <span class="badge bg-success">{{ $request->Status }}</span>
                                    @else
                                    <span class="badge bg-secondary">{{ $request->Status }}</span>
                                    @endif
                                </td>
                                <td>{{ $request->First_Name }}</td>
                                <td>{{ $request->Last_Name }}</td>
                                <td>{{ $request->Nationality }}</td>
                                <td>{{ $request->Location }}</td>
                                <td>{{ $request->Format }}</td>
                                <td data-order="{{ \Carbon\Carbon::parse($request->Updated_Time)->timestamp }}">
                                    {{ \Carbon\Carbon::parse($request->Updated_Time)->format('M j, Y, h:i A') }}
                                    <br>
                                    <small class="text-muted">
                                        ({{ \Carbon\Carbon::parse($request->Updated_Time)->diffForHumans() }})
                                    </small>
                                </td>
                                <td>
                                    <!-- View Button -->
                                    <button class="btn btn-primary btn-sm viewRequestBtn"
                                        data-id="{{ $request->Request_ID }}"
                                        data-first-name="{{ $request->First_Name }}"
                                        data-last-name="{{ $request->Last_Name }}"
                                        data-nationality="{{ $request->Nationality }}"
                                        data-location="{{ $request->Location }}" data-format="{{ $request->Format }}"
                                        data-attachments="{{ $request->Attachment }}"
                                        data-date-created="{{ \Carbon\Carbon::parse($request->Date_Created)->format('M j, Y, h:i A') }}"
                                        data-bs-toggle="modal" data-bs-target="#viewRequestModal"
                                        title="View request details">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    <!-- Review Button -->
                                    <button class="btn btn-warning btn-sm reviewSubmissionBtn"
                                        data-id="{{ $request->Request_ID }}"
                                        data-first-name="{{ $request->First_Name }}"
                                        data-last-name="{{ $request->Last_Name }}"
                                        data-nationality="{{ $request->Nationality }}"
                                        data-location="{{ $request->Location }}" data-format="{{ $request->Format }}"
                                        data-uploaded-format="{{ $request->uploaded_format }}"
                                        data-profiler="{{ $request->Profiler_Name }}"
                                        title="Check if the submitted file meets the requested format">
                                        <i class="bi bi-file-earmark-check"></i>
                                    </button>

                                    <!-- Delete Button -->
                                    <button class="btn btn-danger btn-sm deleteRequestBtn"
                                        data-id="{{ $request->Request_ID }}" data-bs-toggle="modal"
                                        data-bs-target="#deleteModal" title="Delete this request" @if($request->Status
                                        === 'Completed') disabled @endif>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>


        </div>
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
                                accept=".pdf,.doc,.docx">
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
                        <div class="mb-3 d-flex align-items-center">
                            <label class="form-label mb-0" style="white-space: nowrap;"><strong>Date
                                    Created:</strong></label>
                            <span class="ms-2 date-created">N/A</span>

                        </div>

                        <div class="mb-3 d-flex align-items-center">
                            <label class="form-label mb-0" style="white-space: nowrap;"><strong>Created
                                    By:</strong></label>
                            <span class="ms-2">
                                {{ ($request->user)->first_name }}
                                {{ ($request->user)->last_name }}
                            </span>
                        </div>

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
                    <p><strong>Name of Applicant:</strong> <span id="reviewRequester"></span></p>
                    <p><strong>Requested Format:</strong> <span id="reviewFormat"></span></p>

                    <div id="reviewUploadedFormat" class="mt-2"></div>

                    <div class="mt-3">
                        <button id="markAsDoneBtn" class="btn btn-success" @if($request->Status === 'Completed')
                            disabled
                            @endif>
                            Mark as Done
                        </button>
                        <button id="openFeedbackModalBtn" class="btn btn-danger" @if($request->Status === 'Completed')
                            disabled @endif>
                            Request Revision
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Feedback Modal -->
    <div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="feedbackModalLabel">Feedback</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <textarea id="feedbackMessage" class="form-control" rows="4"
                        placeholder="Provide detailed feedback..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button id="sendForRevisionBtn" class="btn btn-danger">Send for Revision</button>
                </div>
            </div>
        </div>
    </div>



</body>

<style>
.viewRequestBtn:hover {
    background-color: #0b5ed7;
    /* Darker blue */
    transition: background-color 0.2s ease-in-out;
}

.reviewSubmissionBtn:hover {
    background-color: #d39e00;
    /* Darker yellow */
    transition: background-color 0.2s ease-in-out;
}

.deleteRequestBtn:hover {
    background-color: #bb2d3b;
    /* Darker red */
    transition: background-color 0.2s ease-in-out;
}

.filter-btn {
    border-radius: 50px;
    /* Make buttons more circular */
    padding: 6px 14px;
}

.filter-btn {
    border-radius: 50px;
    /* Make buttons more circular */
    padding: 6px 14px;
}

.filter-btn.active {
    color: white !important;
}


/* Add specific colors when active */
.filter-btn.active[data-filter="all"] {
    background-color: #343a40 !important;
    /* Yellow */
    border-color: #343a40 !important;
}

.filter-btn.active[data-filter="Pending"] {
    background-color: #ffc107 !important;
    /* Yellow */
    border-color: #ffc107 !important;
}

.filter-btn.active[data-filter="In Progress"] {
    background-color: #0d6efd !important;
    /* Blue */
    border-color: #0d6efd !important;
}

.filter-btn.active[data-filter="Under Review"] {
    background-color: orange !important;
    /* Orange */
    border-color: orange !important;
}

.filter-btn.active[data-filter="Needs Revision"] {
    background-color: #dc3545 !important;
    /* Red */
    border-color: #dc3545 !important;
}

.filter-btn.active[data-filter="Completed"] {
    background-color: #198754 !important;
    /* Green */
    border-color: #198754 !important;
}

.btn-outline-orange {
    color: orange !important;
    border-color: orange !important;
}

.btn-outline-orange:hover {
    background-color: orange !important;
    color: white !important;
}

.filter-btn {
    position: relative;
}

.new-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background-color: red;
    color: white;
    padding: 2px 6px;
    font-size: 10px;
    font-weight: bold;
    border-radius: 8px;
    z-index: 10;
}



/* Base Styling */
html,
body {
    height: 100%;
    overflow: hidden;
    /* Hide built-in scrollbar */
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


#requestTable,
#completedRequestsTable {
    width: 100%;
    border-collapse: collapse;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    background: #1f335f;
    font-size: 12px;
}

#requestTable th,
#completedRequestsTable th {
    background: #1f335f;
    color: #fff;
    text-align: center;
    vertical-align: middle;
    /* Centers vertically as well */
}


#requestTable td,
#completedRequestsTable td {
    padding: 14px 16px;
    text-align: left;
    background: #fff;
    text-align: center;
    vertical-align: middle;
    /* Centers vertically as well */
}


/* Row Styling */
#requestTable tr,
#completedRequestsTable tbody tr {
    transition: background 0.3s, transform 0.2s ease-in-out;
}

#requestTable tr,
#completedRequestsTable tr {
    height: 70px;
    /* Adjust this for more spacing */
}

#requestTable td,
#completedRequestsTable td {
    padding: 20px 16px;
    /* Increase top & bottom padding */
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
    border: 1px solid #fff;
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


/* Filter Button Container */
.mb-3 {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    /* Reduce space between buttons */
    justify-content: left;
    align-items: center;
}

/* Filter Buttons - Smaller & More Compact */
.filter-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    /* Reduce space inside the button */
    min-width: 120px;
    /* Decrease width */
    padding: 6px 10px;
    /* Reduce padding for a smaller look */
    border-radius: 6px;
    /* Slightly smaller rounded corners */
    font-size: 12px;
    /* Reduce font size */
    font-weight: 500;
    transition: all 0.3s ease-in-out;
    border: 2px solid transparent;
    text-align: center;
    box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.1);
}


/* Keep button colors consistent */
.btn-outline-dark {
    border-color: #343a40;
    color: #343a40;
}

.btn-outline-warning {
    border-color: #ffc107;
    color: #856404;
}

.btn-outline-primary {
    border-color: #007bff;
    color: #0056b3;
}

.btn-outline-orange {
    border-color: #fd7e14;
    color: #b45309;
}

.btn-outline-danger {
    border-color: #dc3545;
    color: #721c24;
}

.btn-outline-success {
    border-color: #28a745;
    color: #155724;
}


.btn-outline-dark {
    color: #343a40 !important;
    border-color: #343a40 !important;
}

.btn-outline-dark:hover {
    background-color: #343a40 !important;
    color: white !important;
}

.btn-outline-warning {
    color: #343a40 !important;
    border-color: #ffc107 !important;
}

.btn-outline-warning:hover {
    background-color: #ffc107 !important;
    color: white !important;
}

.btn-outline-primary {
    color: #343a40 !important;
    border-color: #007bff !important;
}

.btn-outline-primary:hover {
    background-color: #007bff !important;
    color: white !important;
}

.btn-outline-orange {
    color: #343a40 !important;
    border-color: orange !important;
}

.btn-outline-orange:hover {
    background-color: orange !important;
    color: white !important;
}

.btn-outline-danger {
    color: #343a40 !important;
    border-color: #dc3545 !important;
}

.btn-outline-danger:hover {
    background-color: #dc3545 !important;
    color: white !important;
}

.btn-outline-success {
    color: #343a40 !important;
    border-color: #28a745 !important;
}

.btn-outline-success:hover {
    background-color: #28a745 !important;
    color: white !important;
}

/* Hover Effect */
.filter-btn:hover {
    transform: translateY(-2px);
    background-color: rgba(255, 255, 255, 0.1);
    box-shadow: 0px 5px 12px rgba(0, 0, 0, 0.15);
}

/* Active State */
.filter-btn.active {
    background-color: rgba(31, 51, 95, 0.15);
    border-width: 2px;
}

/* Status Badge - Smaller & Neater */
.status-badge {
    padding: 2px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

/* New Badge - Stylish Floating */
.new-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background-color: red;
    color: white;
    font-size: 10px;
    font-weight: 600;
    padding: 3px 6px;
    border-radius: 10px;
    box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.2);
}

/* Mobile Responsiveness */
@media (max-width: 768px) {
    .filter-btn {
        min-width: 120px;
        font-size: 12px;
        padding: 8px 12px;
    }

    .status-badge {
        font-size: 10px;
        padding: 3px 8px;
    }

    .new-badge {
        font-size: 9px;
        padding: 2px 5px;
    }
}


/* Table Styling */
.table {
    width: 100%;
    background: white;
    border-radius: 8px;
    box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

/* Flex container for toggle button and filters */
.filter-container {
    display: flex;
    align-items: center;
    gap: 10px;
}

/* Toggle Button */
#toggle-btn {
    min-width: 100px;
    padding: 8px 12px;
    margin-bottom: 30px;
}

/* Filter Buttons (Initially Hidden) */
.filter-buttons {
    display: none;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 30px;

}

/* Show filters when active */
.filter-buttons.show {
    display: flex;
}

/* Filter Button Styles */
.filter-btn {
    padding: 6px 12px;
    font-size: 13px;
    min-width: 120px;
    border-radius: 6px;
}
</style>

</html>

@if (session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<script src="{{ asset('js/usermanage.js') }}"></script>