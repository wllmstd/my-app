<!DOCTYPE html>
<html>
<head>
    <title>Revision Requested</title>
</head>
<body>
    <h2>Revision Requested</h2>

    <p>Dear {{ $profiler->first_name }} {{ $profiler->last_name }},</p>

    <p>The request submitted by <strong>{{ $requester->first_name }} {{ $requester->last_name }}</strong> has been marked for revision.</p>

    <h3>Request Details:</h3>
    <ul>
        <li><strong>Request ID:</strong> {{ $request->Request_ID }}</li>
        <li><strong>Name:</strong> {{ $request->First_Name }} {{ $request->Last_Name }}</li>
        <li><strong>Nationality:</strong> {{ $request->Nationality }}</li>
        <li><strong>Location:</strong> {{ $request->Location }}</li>
        <li><strong>Requested Format:</strong> {{ $request->Format }}</li>
        <li><strong>Created On:</strong> {{ $request->Date_Created }}</li>
    </ul>

    <h3>Feedback for Revision:</h3>
    <p>{{ $feedback }}</p> <!-- ✅ Show feedback -->

    <p>Please review the request and make the necessary changes.</p>

    <p>Thank you!</p>
</body>
</html>
