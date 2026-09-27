<?php
require_once 'config.php';
require_once 'csrf.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi CSRF Token
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("Invalid CSRF token!");
    }

    $name     = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price    = trim($_POST['price'] ?? '');
    $stock    = trim($_POST['stock'] ?? 0); 
    $image    = '';

    // VALIDASI KETAT SESUAI PERMINTAAN DOSEN
    if (empty($name) || empty($category) || empty($price)) {
        $error = "Semua kolom wajib diisi!";
    } elseif (strlen($name) < 3) {
        $error = "Nama produk minimal harus 3 karakter!";
    } elseif (!is_numeric($price) || $price <= 0) {
        $error = "Harga harus berupa angka positif dan lebih besar dari 0!";
    } elseif (!is_numeric($stock) || $stock < 0) {
        $error = "Stok tidak boleh bernilai negatif!";
    } else {
        // Cek keunikan nama produk di database
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM products WHERE name = ?");
        $stmt_check->execute([$name]);
        if ($stmt_check->fetchColumn() > 0) {
            $error = "Nama produk sudah terdaftar, gunakan nama yang unik!";
        } else {
            // Proses Upload Gambar ke folder 'image'
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['image']['tmp_name'];
                $fileName = $_FILES['image']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
                if (in_array($fileExtension, $allowedExtensions)) {
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $uploadFileDir = 'image/';
                    
                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0755, true);
                    }

                    $dest_path = $uploadFileDir . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        $image = $newFileName;
                    } else {
                        $error = "Terjadi kesalahan saat meng-upload gambar.";
                    }
                } else {
                    $error = "Format file gambar tidak diizinkan. Gunakan JPG, JPEG, PNG, atau WEBP.";
                }
            }

            if (empty($error)) {
                // Masukkan data ke database menggunakan Prepared Statements
                $stmt = $pdo->prepare("INSERT INTO products (name, category, price, stock, image) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$name, $category, $price, $stock, $image]);

                header("Location: index.php?status=created");
                exit;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - DESSERT SWEETIE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #ffccd5; padding: 40px 0; }
        .form-card { background: #fff; border-radius: 20px; padding: 30px; box-shadow: 0 8px 20px rgba(0,0,0,0.08); max-width: 600px; margin: 0 auto; }
        .btn-submit { background-color: #e91e63; color: white; border-radius: 20px; font-weight: 600; width: 100%; padding: 10px; }
        .btn-submit:hover { background-color: #c2185b; color: white; }
    </style>
</head>
<body>

<div class="container">
    <div class="form-card">
        <h3 class="text-center mb-4" style="color: #d81b60; font-weight: 600;">Tambah Produk Baru 🍰</h3>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger rounded-4"><?= htmlspecialchars($error, ENT_QUOTES, "UTF-8"); ?></div>
        <?php endif; ?>

        <form action="create.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token(); ?>">

            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary">Nama Produk</label>
                <!-- minlength="3" membatasi minimal 3 huruf di sisi browser -->
                <input type="text" name="name" class="form-control rounded-pill px-3" placeholder="Contoh: Red Velvet Cake" minlength="3" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary">Kategori</label>
                <input type="text" name="category" class="form-control rounded-pill px-3" placeholder="Contoh: Whole Cake" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary">Harga (Rp)</label>
                <!-- min="1" mencegah harga 0 atau minus -->
                <input type="number" name="price" class="form-control rounded-pill px-3" placeholder="Contoh: 150000" min="1" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary">Stok Barang</label>
                <!-- min="0" mencegah stok minus -->
                <input type="number" name="stock" class="form-control rounded-pill px-3" placeholder="Contoh: 25" value="0" min="0" required>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold text-secondary">Foto Produk</label>
                <input type="file" name="image" class="form-control rounded-pill px-3">
                <div class="form-text text-muted ps-2">* Format: JPG, PNG, WEBP (Maksimal < 2MB)</div>
            </div>

            <button type="submit" class="btn btn-submit mb-2">Simpan Produk</button>
            <a href="index.php" class="btn btn-light w-100 rounded-pill text-secondary border">Batal</a>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>