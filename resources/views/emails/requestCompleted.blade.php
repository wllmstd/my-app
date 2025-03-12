<!DOCTYPE html>
<html>
<head>
    <title>Request Completed Notification</title>
</head>
<body>
    <h2>Request Marked as Completed</h2>

    <p>Dear {{ $profiler->first_name ?? 'User' }} {{ $profiler->last_name ?? '' }},</p>

    <p>The user, {{ $user->first_name ?? 'Unknown' }} {{ $user->last_name ?? '' }}, has marked their request as completed.</p>

    <h3>Request Details:</h3>
    <ul>
        <li><strong>Request ID:</strong> {{ $request->Request_ID ?? 'N/A' }}</li>
        <li><strong>Name of Applicant:</strong> {{ $request->First_Name ?? 'N/A' }} {{ $request->Last_Name ?? '' }}</li>
        <li><strong>Requested Format:</strong> {{ $request->Format ?? 'N/A' }}</li>
        <li><strong>Location:</strong> {{ $request->Location ?? 'N/A' }}</li>
        <li><strong>Feedback:</strong> {{ $request->feedback ?? 'No feedback provided' }}</li>
        <li><strong>Marked as Completed By:</strong> {{ $user->first_name ?? 'Unknown' }} {{ $user->last_name ?? '' }}</li>
    </ul>

    <p>The request has now been updated to "Completed".</p>

    <p>Thank you!</p>
</body>
</html>
