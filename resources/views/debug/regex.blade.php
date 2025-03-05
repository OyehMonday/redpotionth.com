<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Regex Debug</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        input, textarea { width: 100%; padding: 10px; margin-top: 10px; }
        button { padding: 10px 15px; margin-top: 10px; cursor: pointer; }
        .container { max-width: 600px; margin: auto; }
        .result { margin-top: 20px; padding: 15px; background: #f9f9f9; border: 1px solid #ccc; }
    </style>
</head>
<body>
    <div class="container">
        <h2>QR Code Regex Debug</h2>
        <form action="{{ route('debug.regex.process') }}" method="POST">
            @csrf
            <label for="qr_raw_data">Enter Raw QR Data:</label>
            <textarea name="qr_raw_data" rows="5" required>{{ old('qr_raw_data') }}</textarea>
            <button type="submit">Extract Reference Number</button>
        </form>

        @if(isset($qrRawData))
        <div class="result">
            <h3>Debug Results</h3>
            <p><strong>Raw QR Data:</strong> {{ $qrRawData }}</p>
            <p><strong>Extracted Reference Number:</strong> {{ $extractedRefNumber }}</p>
        </div>
        @endif
    </div>
</body>
</html>
