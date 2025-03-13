<!DOCTYPE html>
<html>

<head>
    <title>Request Accepted</title>
</head>

<body>
    <!-- <h2>Request Accepted</h2> -->

    <p>Dear {{ $user->first_name ?? 'User' }} {{ $user->last_name ?? '' }},</p>

    <!-- <p>Your request has been accepted by
        <strong>{{ $profiler->first_name ?? 'Unknown' }} {{ $profiler->last_name ?? '' }}</strong>.
    </p> -->
    <p>Your request has been <strong>Accepted</strong> and is now <strong>In progress</strong>.</p>

    <h3>Request Details:</h3>
    <ul>
        <li><strong>Request ID:</strong> {{ $request->Request_ID ?? 'N/A' }}</li>
        <li><strong>Name of Applicant:</strong> {{ $request->First_Name ?? 'N/A' }} {{ $request->Last_Name ?? '' }}</li>
        <li><strong>Nationality:</strong> {{ $request->Nationality ?? 'N/A' }}</li>
        <li><strong>Location:</strong> {{ $request->Location ?? 'N/A' }}</li>
        <li><strong>Requested Format:</strong> {{ $request->Format ?? 'N/A' }}</li>
        <li><strong>Created On:</strong> {{ $formattedDate }}</li>
        <!-- <li><strong>Accepted By:</strong>
            {{ $profiler->first_name ?? 'Unknown' }} {{ $profiler->last_name ?? '' }}
        </li> -->
    </ul>

    <!-- <p>Thank you for using our service!</p> -->

    <p>Best regards,
        <br><strong>{{ $profiler->first_name ?? 'Unknown' }} {{ $profiler->last_name ?? '' }}</strong>
        <br>Support Team
    </p>
</body>

</html>