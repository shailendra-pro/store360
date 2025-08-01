<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Access Expired' }} - Store360</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .expired-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 3rem;
            text-align: center;
            max-width: 500px;
            width: 90%;
        }
        .expired-icon {
            font-size: 4rem;
            color: #dc3545;
            margin-bottom: 1.5rem;
        }
        .expired-title {
            color: #dc3545;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .expired-message {
            color: #6c757d;
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        .company-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
            border-left: 4px solid #0d6efd;
        }
        .contact-btn {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .contact-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(13, 110, 253, 0.3);
        }
        .back-btn {
            background: transparent;
            border: 2px solid #6c757d;
            color: #6c757d;
            border-radius: 50px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }
        .back-btn:hover {
            background: #6c757d;
            color: white;
        }
    </style>
</head>
<body>
    <div class="expired-card">
        <div class="expired-icon">
            <i class="bi bi-exclamation-triangle"></i>
        </div>

        <h1 class="expired-title">{{ $title ?? 'Access Expired' }}</h1>

        <p class="expired-message">
            {{ $message ?? 'Your access link has expired or is invalid.' }}
        </p>

        @if(isset($company))
        <div class="company-info">
            <strong>Company:</strong> {{ $company }}
        </div>
        @endif

        @if(isset($expired_at))
        <div class="company-info">
            <strong>Expired:</strong> {{ \Carbon\Carbon::parse($expired_at)->format('F j, Y \a\t g:i A') }}
        </div>
        @endif

        <div class="d-grid gap-2">
            <button class="btn contact-btn" onclick="contactSupport()">
                <i class="bi bi-envelope me-2"></i>
                Contact Support
            </button>
{{--
            <button class="btn back-btn" onclick="goBack()">
                <i class="bi bi-arrow-left me-2"></i>
                Go Back
            </button> --}}
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function contactSupport() {
            // You can customize this to open email client or redirect to support page
            window.location.href = 'mailto:support@store360.com?subject=WebGL Access Issue&body=Hello, I am having trouble accessing the WebGL application. Please help.';
        }

        // function goBack() {
        //     window.history.back();
        // }

        // Auto-redirect after 30 seconds
        setTimeout(function() {
            window.location.href = '/';
        }, 30000);
    </script>
</body>
</html>
