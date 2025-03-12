<!DOCTYPE html>
<html>
<head>
    <title>File Uploaded Notification</title>
</head>
<body>
    <h2>File Uploaded Notification</h2>

    <p>Dear {{ $user->first_name ?? 'User' }} {{ $user->last_name ?? '' }},</p>

    <p>The support team has uploaded the format you requested. It is now ready for review.</p>

    <h3>Request Details:</h3>
    <ul>
        <li><strong>Request ID:</strong> {{ $request->Request_ID ?? 'N/A' }}</li>
        <li><strong>Requested Format:</strong> {{ $request->Format ?? 'N/A' }}</li>
        <li><strong>Location:</strong> {{ $request->Location ?? 'N/A' }}</li>
        <li><strong>Uploaded By:</strong> {{ $profiler->first_name ?? 'Unknown' }} {{ $profiler->last_name ?? '' }}</li>
    </ul>

    <h3>Uploaded Files:</h3>
    <ul>
        @foreach($uploadedFiles as $file)
            <li><a href="{{ asset('storage/uploads/' . $file) }}" target="_blank">{{ $file }}</a></li>
        @endforeach
    </ul>

    <p>Thank you for using our service!</p>
</body>
</html>
