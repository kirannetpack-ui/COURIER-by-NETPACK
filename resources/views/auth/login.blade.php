<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NETPACK · Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(145deg, #f0f4f8 0%, #dce4ec 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .container {
            max-width: 480px;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 40px;
            padding: 2.5rem 2.5rem;
            box-shadow: 0 25px 50px -10px rgba(0, 20, 30, 0.25),
                        inset 0 1px 2px rgba(255, 255, 255, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .app-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .app-header .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 0.3rem;
        }

        .app-header .logo i {
            font-size: 2.5rem;
            color: #1e5b77;
        }

        .app-header .logo h1 {
            font-size: 2.2rem;
            font-weight: 700;
            color: #0b2b3b;
            letter-spacing: -0.5px;
        }

        .app-header .logo h1 span {
            color: #2a7faa;
        }

        .app-header p {
            color: #3a5b6b;
            font-size: 0.95rem;
            opacity: 0.8;
        }

        .form-group {
            margin-bottom: 1.2rem;
        }

        .form-group label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            font-weight: 500;
            color: #1f4353;
            margin-bottom: 0.3rem;
        }

        .form-group label .required {
            color: #d32f2f;
            font-size: 0.7rem;
        }

        .form-group label i {
            color: #2a7faa;
            width: 1.1rem;
            font-size: 0.9rem;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem 1rem;
            background: rgba(255, 255, 255, 0.6);
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 20px;
            font-size: 0.95rem;
            color: #0b2b3b;
            transition: all 0.2s ease;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
        }

        .form-group input:focus {
            outline: none;
            border-color: #2a7faa;
            background: white;
            box-shadow: 0 4px 12px rgba(42, 127, 170, 0.12), inset 0 2px 4px rgba(0,0,0,0.02);
        }

        .form-group input::placeholder {
            color: #8ba2b0;
            font-weight: 300;
            font-size: 0.9rem;
        }

        .btn-primary {
            width: 100%;
            padding: 0.85rem;
            background: #1e5b77;
            border: none;
            border-radius: 60px;
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 8px 18px -6px rgba(26, 80, 102, 0.3);
            margin-top: 0.5rem;
        }

        .btn-primary i {
            font-size: 1.1rem;
        }

        .btn-primary:hover:not(:disabled) {
            background: #134a62;
            transform: scale(1.01);
            box-shadow: 0 12px 24px -8px rgba(19, 74, 98, 0.4);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .spinner {
            display: inline-block;
            width: 1.1rem;
            height: 1.1rem;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 0.8s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .error-message {
            color: #d32f2f;
            font-size: 0.85rem;
            margin-top: 0.5rem;
            display: none;
            align-items: flex-start;
            gap: 0.5rem;
            background: rgba(211, 47, 47, 0.05);
            padding: 0.75rem 1rem;
            border-radius: 12px;
            border-left: 3px solid #d32f2f;
        }

        .error-message.show {
            display: flex;
        }

        .error-message ul {
            margin: 0;
            padding-left: 1.2rem;
        }

        .error-message ul li {
            margin-bottom: 0.2rem;
        }

        .form-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.9rem;
            color: #2a5a6b;
        }

        .form-footer a {
            color: #1e5b77;
            font-weight: 600;
            text-decoration: none;
            border-bottom: 1px dotted rgba(30, 91, 119, 0.3);
            cursor: pointer;
        }

        .form-footer a:hover {
            border-bottom: 1px solid #1e5b77;
        }

        .remember-me {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            font-size: 0.85rem;
        }

        .remember-me label {
            display: flex;
            align-items: center;
            gap: 0.3rem;
            color: #3a5b6b;
            cursor: pointer;
        }

        .remember-me a {
            color: #2a7faa;
            text-decoration: none;
        }

        .remember-me a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .container {
                padding: 1.8rem 1.2rem;
            }
            .app-header .logo h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Header -->
    <div class="app-header">
        <div class="logo">
            <i class="fas fa-box"></i>
            <h1>NET<span>PACK</span></h1>
        </div>
        <p>Nepal's Trusted Delivery & E-Commerce Platform</p>
    </div>

    <!-- LOGIN FORM -->
    <form method="POST" action="{{ route('login.submit') }}">
        @csrf

        <div class="form-group">
            <label for="email"><i class="fas fa-envelope"></i> Email Address <span class="required">*</span></label>
            <input type="email" id="email" name="email" placeholder="your@email.com" value="{{ old('email') }}" required autofocus />
        </div>

        <div class="form-group">
            <label for="password"><i class="fas fa-lock"></i> Password <span class="required">*</span></label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required />
        </div>

        <div class="remember-me">
            <label>
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }} /> Remember me
            </label>
            <!-- Removed the forgot password link to avoid route error -->
        </div>

        @if ($errors->any())
            <div class="error-message show">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <strong>Please fix the following errors:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <button type="submit" class="btn-primary" id="loginBtn">
            <i class="fas fa-sign-in-alt"></i> Sign In
        </button>

        <div class="form-footer">
            Don't have an account? <a href="{{ route('register') }}">Create one</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('form');
    const loginBtn = document.getElementById('loginBtn');

    loginForm.addEventListener('submit', function() {
        loginBtn.disabled = true;
        loginBtn.innerHTML = '<span class="spinner"></span> Signing in...';
    });
});
</script>

</body>
</html>