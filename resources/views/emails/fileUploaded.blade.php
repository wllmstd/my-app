<!DOCTYPE html>
<html>

<head>
    <title>File Uploaded Notification</title>
</head>

<body>
    <p>Dear {{ $user->first_name ?? 'User' }} {{ $user->last_name ?? '' }},</p>

    <p>The requested file format has been <strong>SUCCESSFULLY UPLOADED</strong> and is now available for <strong>REVIEW</strong></p>

    <h3>Uploaded Files:</h3>
    <ul>
        @forelse($uploadedFiles as $file)
        <li><a href="{{ asset('storage/uploads/' . $file) }}" target="_blank">{{ $file }}</a></li>
        @empty
        <li>No files uploaded.</li>
        @endforelse
    </ul>

    <h3>Request Details:</h3>
    <ul>
        <li><strong>Request ID:</strong> {{ $request->Request_ID ?? 'N/A' }}</li>
        <li><strong>Name of Applicant:</strong> {{ $request->First_Name ?? 'N/A' }} {{ $request->Last_Name ?? '' }}</li>
        <li><strong>Nationality:</strong> {{ $request->Nationality ?? 'N/A' }}</li>
        <li><strong>Location:</strong> {{ $request->Location ?? 'N/A' }}</li>
        <li><strong>Requested Format:</strong> {{ $request->Format ?? 'N/A' }}</li>
        <li><strong>Date Created:</strong> {{ $formattedDate ?? 'N/A' }}</li>
    </ul>

    <p>Best regards,
        <br><strong>{{ $profiler->first_name ?? 'Unknown' }} {{ $profiler->last_name ?? '' }}</strong>
        <br>Support Team
    </p>
</body>

</html>