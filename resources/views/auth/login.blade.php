<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — LPG Point</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="login-page">
    <div class="login-card">
        <div class="text-center mb-4">
            <div class="login-logo-icon">🔥</div>
            <h1 class="fs-4 fw-bold mb-0">LPG Point</h1>
            <p class="small text-secondary mt-1 mb-0">Gas Cylinder Shop POS</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger py-2 px-3 small" role="alert">
                ❌ {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input
                    type="text"
                    class="form-control"
                    id="username"
                    name="username"
                    value="{{ old('username') }}"
                    placeholder="Apna username daalen"
                    autocomplete="username"
                    autofocus
                >
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="position-relative">
                    <input
                        type="password"
                        class="form-control pe-5"
                        id="password"
                        name="password"
                        placeholder="Password likhein"
                        autocomplete="current-password"
                    >
                    <button type="button" class="password-toggle-btn" data-toggle-password="#password" tabindex="-1">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 mt-1">
                <i class="bi bi-box-arrow-in-right"></i> Login Karein
            </button>
        </form>
    </div>
</body>
</html>
