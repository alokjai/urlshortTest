<!DOCTYPE html>
<html>
<head>
    <title>URL Shortener</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 8px;
            text-align: center;
            max-width: 400px;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
        }
        p {
            color: #666;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 10px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .icon {
            font-size: 40px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🔗</div>
        <h1>URL Shortener</h1>
        <p>A simple and efficient URL shortening service with multi-tenant company support and role-based access control.</p>
        <a href="{{ route('login') }}" class="btn">Get Started</a>
    </div>
</body>
</html>
