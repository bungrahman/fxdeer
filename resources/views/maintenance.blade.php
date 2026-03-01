<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance in Progress | FXDeer</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        :root {
            --primary: #FF2D20;
            --background: #050505;
            --glass: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.08);
            --text-dim: #999;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background-color: var(--background);
            background-image: 
                radial-gradient(circle at 20% 20%, rgba(255, 45, 32, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 80% 80%, rgba(255, 45, 32, 0.05) 0%, transparent 40%);
            color: white;
            font-family: 'Outfit', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .maintenance-card {
            background: var(--glass);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            padding: 4rem 2rem;
            border-radius: 30px;
            text-align: center;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .icon-box {
            width: 80px;
            height: 80px;
            background: rgba(255, 45, 32, 0.1);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            color: var(--primary);
        }

        h1 { font-size: 2.5rem; margin-bottom: 1rem; letter-spacing: -1px; }
        p { color: var(--text-dim); line-height: 1.6; margin-bottom: 2rem; }

        .btn-admin {
            display: inline-block;
            background: transparent;
            border: 1px solid var(--glass-border);
            color: white;
            padding: 0.8rem 1.5rem;
            border-radius: 12px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .btn-admin:hover {
            background: rgba(255,255,255,0.05);
            border-color: white;
        }

        /* Ambient Animation */
        .ambient {
            position: absolute;
            width: 300px;
            height: 300px;
            background: var(--primary);
            filter: blur(150px);
            opacity: 0.1;
            z-index: -1;
            animation: move 20s infinite alternate;
        }

        @keyframes move {
            from { transform: translate(-50%, -50%); }
            to { transform: translate(50%, 50%); }
        }
    </style>
</head>
<body>
    <div class="ambient"></div>
    <div class="maintenance-card">
        <div class="icon-box">
            <i data-feather="tool" style="width: 40px; height: 40px;"></i>
        </div>
        <h1>Under Maintenance</h1>
        <p>We're currently fine-tuning the system to ensure maximum performance. We'll be back shortly with even faster news delivery.</p>
        
        <a href="{{ route('admin.dashboard') }}" class="btn-admin">
            <i data-feather="lock" style="width: 14px; height: 14px; margin-right: 8px;"></i>
            Admin Access
        </a>
    </div>

    <script>
        feather.replace();
    </script>
</body>
</html>
