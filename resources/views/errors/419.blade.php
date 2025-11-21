<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Telah Kadaluarsa</title>
    <meta http-equiv="refresh" content="3;url={{ $loginUrl ?? '/' }}">
    <style>
        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: #f7fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 500px;
            text-align: center;
        }
        h1 {
            color: #e53e3e;
            margin-bottom: 10px;
            font-size: 24px;
        }
        p {
            color: #4a5568;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3182ce;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        a {
            color: #3182ce;
            text-decoration: none;
            font-weight: 500;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>⏱️ Halaman Telah Kadaluarsa</h1>
        <p>
            Sesi Anda telah berakhir. Anda akan diarahkan ke halaman login dalam beberapa detik...
        </p>
        <div class="spinner"></div>
        <p>
            Atau klik <a href="{{ $loginUrl ?? '/' }}">di sini</a> untuk langsung ke halaman login.
        </p>
    </div>
</body>
</html>
