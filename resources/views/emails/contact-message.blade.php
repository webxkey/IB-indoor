<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Contact Message</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; background-color: #f8f9fa; margin: 0; padding: 20px; }
        .container { background: #fff; border-radius: 6px; padding: 20px; max-width: 600px; margin: auto; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        h2 { color: #007bff; }
        p { line-height: 1.5; }
        hr { border: none; border-top: 1px solid #ddd; margin: 20px 0; }
        small { color: #888; }
    </style>
</head>
<body>
    <div class="container">
        <h2>New Contact Message Received</h2>
        <p><strong>Name:</strong> {{ $contactData['name'] }}</p>
        <p><strong>Email:</strong> {{ $contactData['email'] }}</p>
        <p><strong>Phone:</strong> {{ $contactData['phone'] ?? 'N/A' }}</p>
        <p><strong>Subject:</strong> {{ $contactData['subject'] }}</p>
        <p><strong>Message:</strong></p>
        <p>{{ $contactData['message'] }}</p>

        <hr>
        <small>This message was sent from your website contact form.</small>
    </div>
</body>
</html>
