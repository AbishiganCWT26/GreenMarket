<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unauthorized</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-image: url('/assets/images/Access-Denied.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: inherit;
            filter: blur(5px);
            transform: scale(1.02);
            z-index: -1;
        }

        .glass-card {
            -webkit-backdrop-filter: blur(12px);
            border-radius: 48px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2), 0 8px 20px rgba(0, 0, 0, 0.15), inset 0 2px 4px rgba(255,255,255,0.3);
            padding: 3rem 4rem;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.2, 0.9, 0.3, 1.1);
            border: 1px solid rgba(255, 255, 255, 0.5);
            max-width: 90vw;
            width: fit-content;
            margin: 1.5rem;
        }

        .glass-card:hover {
            transform: scale(1.02) translateY(-6px);
            box-shadow: 0 35px 60px rgba(0, 0, 0, 0.3), 0 12px 30px rgba(0, 0, 0, 0.25), inset 0 3px 6px rgba(255,255,255,0.5);
            border-color: rgba(255, 255, 255, 0.8);
            background: rgba(255, 255, 255, 0.3);
        }

        .lock-icon {
            font-size: 3rem;
            line-height: 1;
            display: inline-block;
            filter: drop-shadow(0 10px 12px rgba(0,0,0,0.2));
            transition: all 0.3s ease;
            animation: gentleFloat 3s infinite ease-in-out;
        }

        .glass-card:hover .lock-icon {
            transform: rotate(4deg) scale(1.1);
            filter: drop-shadow(0 18px 18px rgba(0,0,0,0.25));
        }

        h1 {
            font-size: 3.2rem;
            font-weight: 600;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #ffffff, #ffffffff);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-shadow: 2px 4px 12px rgba(0,0,0,0.1);
            margin-bottom: 0.5rem;
            transition: all 0.25s;
        }

        .glass-card:hover h1 {
            background: linear-gradient(135deg, #ffffffff, #ffffffff);
            -webkit-background-clip: text;
            background-clip: text;
            letter-spacing: -0.3px;
            text-shadow: 4px 6px 16px rgba(0,0,0,0.15);
        }

        .message-badge {
            background: rgba(220, 38, 38, 0.2);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            border-radius: 100px;
            padding: 0.9rem 2.2rem;
            margin: 1.8rem 0 2.2rem;
            font-size: 1.25rem;
            font-weight: 500;
            color: #f5afafff;
            border: 1px solid rgba(255, 255, 255, 0.45);
            box-shadow: inset 0 1px 4px rgba(255,255,255,0.6), 0 6px 12px rgba(0,0,0,0.1);
            transition: all 0.3s;
            word-break: break-word;
        }

        .glass-card:hover .message-badge {
            background: rgba(220, 38, 38, 0.25);
            border-color: rgba(255, 255, 255, 0.7);
            box-shadow: inset 0 2px 6px rgba(255,255,255,0.7), 0 10px 18px rgba(0,0,0,0.15);
            transform: scale(1.01);
        }

        .button-group {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            border: none;
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            padding: 0.9rem 2.4rem;
            border-radius: 50px;
            font-size: 1.2rem;
            font-weight: 550;
            color: #1e293b;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1), 0 2px 4px rgba(255, 255, 255, 0.4) inset;
            border: 1px solid rgba(255, 255, 255, 0.7);
            flex: 0 1 auto;
            min-width: 160px;
        }

        .btn:hover {
            background: rgba(255, 255, 255, 0.7);
            transform: translateY(-5px) scale(1.03);
            box-shadow: 0 18px 30px rgba(0, 0, 0, 0.2), 0 4px 8px rgba(255, 255, 255, 0.6) inset;
            border-color: white;
            color: #0b1622;
        }

        .btn:active {
            transform: translateY(-2px) scale(1.01);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .btn-login {
            background: rgba(230, 245, 255, 0.65);
            border-color: rgba(255, 255, 255, 0.85);
        }

        .btn-login:hover {
            background: rgba(240, 250, 255, 0.9);
            box-shadow: 0 18px 30px rgba(0, 60, 130, 0.2), 0 4px 8px rgba(255, 255, 255, 0.7) inset;
        }

        .btn-back {
            background: rgba(250, 235, 215, 0.55);
        }

        .btn-back:hover {
            background: rgba(255, 245, 225, 0.85);
        }

        @keyframes gentleFloat {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
            100% { transform: translateY(0px); }
        }

        @media screen and (min-width: 2560px) and (max-width: 5000px) {
            .glass-card { padding: 1.5rem 2.5rem; border-radius: 80px; }
            .lock-icon { font-size: 9rem; }
            h1 { font-size: 5rem; }
            .message-badge { font-size: 2.2rem; padding: 1.4rem 4rem; }
            .btn { font-size: 2rem; padding: 1.5rem 4rem; min-width: 260px; }
        }

        @media screen and (min-width: 1501px) and (max-width: 2559px) {
            .glass-card { padding: 1.5rem 2.5rem; border-radius: 64px; }
            .lock-icon { font-size: 7rem; }
            h1 { font-size: 4rem; }
            .message-badge { font-size: 1.8rem; padding: 1.2rem 3rem; }
            .btn { font-size: 1.6rem; padding: 1.2rem 3rem; }
        }

        @media screen and (min-width: 1400px) and (max-width: 1500px) {
            .glass-card { padding: 1.5rem 2.5rem; }
            .lock-icon { font-size: 6.2rem; }
            h1 { font-size: 3.5rem; }
        }

        @media screen and (min-width: 1200px) and (max-width: 1399px) {
            .glass-card { padding: 1.5rem 2.5rem; }
            .lock-icon { font-size: 5.8rem; }
            h1 { font-size: 3.2rem; }
        }

        @media screen and (min-width: 1001px) and (max-width: 1199px) {
            .glass-card { padding: 1rem 2rem; }
        }

        @media screen and (max-width: 1000px) {
            .glass-card { padding: 2.5rem 3.5rem; }
            .button-group { gap: 1rem; }
            .btn { min-width: 140px; padding: 0.8rem 1.8rem; }
        }

        @media screen and (min-width: 992px) and (max-width: 999px) {
            .glass-card { padding: 2.5rem 3.2rem; }
            h1 { font-size: 2.8rem; }
        }

        @media screen and (min-width: 768px) and (max-width: 991px) {
            .glass-card { padding: 2.2rem 2.5rem; }
            .lock-icon { font-size: 4.8rem; }
            h1 { font-size: 2.5rem; }
            .message-badge { font-size: 1.1rem; padding: 0.8rem 1.5rem; }
            .btn { font-size: 1.1rem; padding: 0.7rem 1.2rem; min-width: 120px; }
        }

        @media screen and (min-width: 576px) and (max-width: 767px) {
            .glass-card { padding: 2rem 1.8rem; }
            .lock-icon { font-size: 4.2rem; }
            h1 { font-size: 2.2rem; }
            .message-badge { font-size: 1rem; padding: 0.8rem 1.2rem; margin: 1.2rem 0 1.8rem; }
            .btn { font-size: 1rem; padding: 0.7rem 1rem; min-width: 110px; }
        }

        @media screen and (min-width: 481px) and (max-width: 575px) {
            .glass-card { padding: 1.8rem 1.2rem; border-radius: 36px; }
            .lock-icon { font-size: 3.8rem; }
            h1 { font-size: 2rem; }
            .message-badge { font-size: 0.95rem; padding: 0.7rem 1rem; }
            .button-group { flex-direction: column; gap: 0.8rem; }
            .btn { width: 100%; font-size: 1rem; padding: 0.8rem; }
        }

        @media screen and (min-width: 380px) and (max-width: 480px) {
            .glass-card { padding: 1.5rem 1rem; border-radius: 32px; margin: 1rem; }
            .lock-icon { font-size: 3.3rem; margin-bottom: 0.5rem; }
            h1 { font-size: 1.8rem; }
            .message-badge { font-size: 0.9rem; padding: 0.6rem 0.8rem; margin: 1rem 0 1.5rem; }
            .btn { font-size: 0.95rem; padding: 0.7rem; }
        }

        @media screen and (max-width: 379px) {
            .glass-card { padding: 1.2rem 0.8rem; border-radius: 28px; margin: 0.7rem; }
            .lock-icon { font-size: 2.8rem; }
            h1 { font-size: 1.5rem; }
            .message-badge { font-size: 0.8rem; padding: 0.5rem 0.6rem; }
            .btn { font-size: 0.85rem; padding: 0.6rem 0.5rem; min-width: 80px; }
            .button-group { gap: 0.5rem; }
        }
    </style>
</head>
<body>
    <div class="glass-card">
        <div class="lock-icon">🔒</div>
        <h1>Access Denied</h1>
        <div class="message-badge">
            {{ session('error', 'Unauthorized access.') }}
        </div>
        <div class="button-group">
            <button class="btn btn-login" id="loginBtn">Go to Login</button>
            <button class="btn btn-back" id="backBtn">Go Back</button>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const loginBtn = document.getElementById('loginBtn');
            const backBtn = document.getElementById('backBtn');

            if (loginBtn) {
                loginBtn.addEventListener('click', function () {
                    window.location.href = "{{ route('login') }}";
                });
            }

            if (backBtn) {
                backBtn.addEventListener('click', function () {
                    window.history.back();
                });
            }
        });
    </script>
</body>
</html>