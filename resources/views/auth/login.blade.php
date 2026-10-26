<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Sweetie Dessert</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #ffccd5 0%, #ffebee 50%, #fce4ec 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }

        .login-card {
            background: #ffffff;
            border-radius: 28px;
            box-shadow: 0 18px 40px rgba(194, 24, 91, 0.12);
            max-width: 440px;
            width: 100%;
            padding: 36px 30px;
            border: 1px solid #ffccd5;
            text-align: center;
            position: relative;
        }

        .login-logo {
            width: 82px;
            height: 82px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #c2185b;
            padding: 3px;
            background: #fff;
            margin-bottom: 8px;
            box-shadow: 0 4px 15px rgba(194, 24, 91, 0.15);
        }

        .brand-name {
            font-family: 'Great Vibes', cursive;
            color: #c2185b;
            font-size: 2.8rem;
            margin-bottom: 2px;
            line-height: 1;
        }

        .form-control-sweet {
            border-radius: 14px;
            padding: 11px 16px;
            border: 1.5px solid #f8bbd0;
            font-size: 0.93rem;
            transition: all 0.2s ease;
        }

        .form-control-sweet:focus {
            border-color: #e91e63;
            box-shadow: 0 0 0 0.25rem rgba(233, 30, 99, 0.15);
        }

        .input-group-text-sweet {
            background-color: #fff;
            border: 1.5px solid #f8bbd0;
            border-left: none;
            border-radius: 0 14px 14px 0 !important;
            cursor: pointer;
            color: #888;
        }

        .input-group-text-sweet:hover {
            color: #c2185b;
        }

        .btn-login {
            background: linear-gradient(135deg, #ff758f 0%, #c2185b 100%);
            color: #fff;
            border-radius: 16px;
            padding: 12px;
            font-weight: 600;
            font-size: 1rem;
            border: none;
            width: 100%;
            transition: all 0.25s ease;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #ff4d6d 0%, #a01048 100%);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(194, 24, 91, 0.25);
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('shop') }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1 small text-muted" title="Kembali ke Toko">
            <i class="bi bi-arrow-left me-1"></i> Ke Toko
        </a>
        <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-1 small fw-semibold">
            Sweetie Dessert
        </span>
    </div>

    <img src="{{ asset('image/logo.png') }}" alt="Logo" class="login-logo" onerror="this.src='https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=100&q=80'">
    <h1 class="brand-name">Sweetie Dessert</h1>
    <h5 class="fw-bold text-dark mb-1">Selamat Datang Kembali</h5>
    <p class="text-muted small mb-4">Silakan masuk ke akun Anda untuk melanjutkan.</p>

    @if(session('success'))
        <div class="alert alert-success py-2 rounded-3 small text-start">{{ session('success') }}</div>
    @endif

    @if(session('info'))
        <div class="alert alert-info py-2 rounded-3 small text-start">{{ session('info') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger py-2 rounded-3 small text-start">
            <i class="bi bi-exclamation-circle-fill me-1"></i> {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('login.post') }}" method="POST">
        @csrf
        <div class="mb-3 text-start">
            <label class="form-label small fw-semibold text-secondary">Username atau Email</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 rounded-start-4" style="border: 1.5px solid #f8bbd0; border-right: none;">
                    <i class="bi bi-person text-muted"></i>
                </span>
                <input type="text" name="login" class="form-control form-control-sweet border-start-0 ps-1" placeholder="Masukkan username atau email Anda" value="{{ old('login') }}" required autofocus style="border-radius: 0 14px 14px 0;">
            </div>
        </div>

        <div class="mb-3 text-start">
            <label class="form-label small fw-semibold text-secondary">Kata Sandi (Password)</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 rounded-start-4" style="border: 1.5px solid #f8bbd0; border-right: none;">
                    <i class="bi bi-lock text-muted"></i>
                </span>
                <input type="password" name="password" id="passwordInput" class="form-control form-control-sweet border-start-0 border-end-0 ps-1" placeholder="Ketik kata sandi Anda" required>
                <button type="button" class="input-group-text input-group-text-sweet" onclick="togglePasswordVisibility('passwordInput', 'toggleEyeIcon')">
                    <i class="bi bi-eye-slash" id="toggleEyeIcon"></i>
                </button>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check text-start mb-0">
                <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                <label class="form-check-label small text-muted" for="rememberMe">
                    Ingat saya
                </label>
            </div>
            <a href="{{ route('shop') }}" class="small text-danger text-decoration-none">
                Lihat Katalog <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <button type="submit" class="btn btn-login mb-4">
            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Sekarang
        </button>
    </form>

    <div class="p-3 bg-light rounded-4 small">
        <span class="text-muted">Belum punya akun pelanggan?</span><br>
        <a href="{{ route('register') }}" class="fw-bold text-danger text-decoration-none">
            <i class="bi bi-person-plus-fill me-1"></i> Daftar Sebagai Member Baru di Sini
        </a>
    </div>
</div>

<script>
    function togglePasswordVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        }
    }
</script>

</body>
</html>
