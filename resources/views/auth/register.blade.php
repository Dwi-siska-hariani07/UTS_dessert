<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Member - Sweetie Dessert</title>
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
            padding: 30px 16px;
        }

        .register-card {
            background: #ffffff;
            border-radius: 28px;
            box-shadow: 0 18px 40px rgba(194, 24, 91, 0.12);
            max-width: 520px;
            width: 100%;
            padding: 36px 32px;
            border: 1px solid #ffccd5;
            text-align: center;
        }

        .login-logo {
            width: 80px;
            height: 80px;
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
            padding: 10px 16px;
            border: 1.5px solid #f8bbd0;
            font-size: 0.92rem;
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

        .btn-register {
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

        .btn-register:hover {
            background: linear-gradient(135deg, #ff4d6d 0%, #a01048 100%);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(194, 24, 91, 0.25);
        }
    </style>
</head>
<body>

<div class="register-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('login') }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1 small text-muted" title="Kembali ke Login">
            <i class="bi bi-arrow-left me-1"></i> Ke Login
        </a>
        <a href="{{ route('shop') }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1 small text-muted" title="Ke Toko">
            <i class="bi bi-shop me-1"></i> Etalase Toko
        </a>
    </div>

    <img src="{{ asset('image/logo.png') }}" alt="Logo" class="login-logo" onerror="this.src='https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=100&q=80'">
    <h1 class="brand-name">Sweetie Dessert</h1>
    <h5 class="fw-bold text-dark mb-1">Daftar Akun Member Baru</h5>
    <p class="text-muted small mb-4">Nikmati kemudahan pesan dessert manis favoritmu kapan saja!</p>

    @if($errors->any())
        <div class="alert alert-danger py-2 rounded-3 small text-start">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('register.post') }}" method="POST">
        @csrf

        <div class="row g-2 text-start mb-3">
            <div class="col-12">
                <label class="form-label small fw-semibold text-secondary">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control form-control-sweet" placeholder="Contoh: Clarissa Aurelia" value="{{ old('name') }}" required autofocus>
            </div>

            <div class="col-md-6">
                <label class="form-label small fw-semibold text-secondary">Username Login <span class="text-danger">*</span></label>
                <input type="text" name="username" class="form-control form-control-sweet" placeholder="clarissa01" value="{{ old('username') }}" required>
            </div>

            <div class="col-md-6">
                <label class="form-label small fw-semibold text-secondary">Nomor WhatsApp / HP <span class="text-danger">*</span></label>
                <input type="text" name="phone" class="form-control form-control-sweet" placeholder="081234567890" value="{{ old('phone') }}" required>
            </div>

            <div class="col-12">
                <label class="form-label small fw-semibold text-secondary">Alamat Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control form-control-sweet" placeholder="clarissa@gmail.com" value="{{ old('email') }}" required>
            </div>

            <div class="col-md-6">
                <label class="form-label small fw-semibold text-secondary">Kata Sandi <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="password" name="password" id="regPassword" class="form-control form-control-sweet border-end-0" placeholder="Min. 6 karakter" required style="border-radius: 14px 0 0 14px;">
                    <button type="button" class="input-group-text input-group-text-sweet" onclick="togglePass('regPassword', 'eyeIcon1')">
                        <i class="bi bi-eye-slash" id="eyeIcon1"></i>
                    </button>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label small fw-semibold text-secondary">Ulangi Kata Sandi <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="password" name="password_confirmation" id="regConfirmPassword" class="form-control form-control-sweet border-end-0" placeholder="Ketik ulang password" required style="border-radius: 14px 0 0 14px;">
                    <button type="button" class="input-group-text input-group-text-sweet" onclick="togglePass('regConfirmPassword', 'eyeIcon2')">
                        <i class="bi bi-eye-slash" id="eyeIcon2"></i>
                    </button>
                </div>
            </div>

            <div class="col-12">
                <label class="form-label small fw-semibold text-secondary">Alamat Pengiriman (Domisili)</label>
                <textarea name="address" class="form-control form-control-sweet" rows="2" placeholder="Nama jalan, nomor rumah, kelurahan, kota...">{{ old('address') }}</textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-register mb-3">
            <i class="bi bi-heart-fill me-1"></i> Daftar & Mulai Belanja Dessert
        </button>
    </form>

    <div class="p-3 bg-light rounded-4 small">
        <span class="text-muted">Sudah memiliki akun?</span> <a href="{{ route('login') }}" class="fw-bold text-danger text-decoration-none">Masuk di sini</a>
    </div>
</div>

<script>
    function togglePass(inputId, iconId) {
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
