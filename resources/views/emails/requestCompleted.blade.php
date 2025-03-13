<!DOCTYPE html>
<html>
<head>
    <title>Request Completed Notification</title>
</head>
<body>
    <p>Dear {{ $profiler->first_name ?? 'User' }} {{ $profiler->last_name ?? '' }},</p>

    <p>The request for applicant {{ $request->First_Name ?? 'N/A' }} {{ $request->Last_Name ?? '' }}, has been successfully marked as <strong>Completed</strong>.</p>
    
    <p>Thank you for your attention to this request.</p>

    <h3>Request Details:</h3>
    <ul>
        <li><strong>Request ID:</strong> {{ $request->Request_ID ?? 'N/A' }}</li>
        <li><strong>Name of Applicant:</strong> {{ $request->First_Name ?? 'N/A' }} {{ $request->Last_Name ?? '' }}</li>
        <li><strong>Requested Format:</strong> {{ $request->Format ?? 'N/A' }}</li>
        <li><strong>Location:</strong> {{ $request->Location ?? 'N/A' }}</li>
        <li><strong>Feedback:</strong> {{ $request->feedback ?? 'No feedback provided' }}</li>
        <li><strong>Date Completed:</strong> {{ $formattedDate ?? 'N/A' }}</li>
    </ul>

    <p>Best regards,  
        <br><strong>{{ $user->first_name ?? 'Unknown' }} {{ $user->last_name ?? '' }}</strong>  
    </p>
</body>
</html>
