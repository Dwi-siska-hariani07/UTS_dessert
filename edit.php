<?php
require_once 'config.php';
require_once 'csrf.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute([':id' => $id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: index.php");
    exit();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price    = filter_var($_POST['price'] ?? 0, FILTER_VALIDATE_FLOAT);
    $stock    = filter_var($_POST['stock'] ?? 0, FILTER_VALIDATE_INT);

    if (strlen($name) < 3) {
        $errors[] = "Nama produk minimal harus 3 karakter.";
    } else {
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM products WHERE name = :name AND id != :id");
        $stmt_check->execute([':name' => $name, ':id' => $id]);
        if ($stmt_check->fetchColumn() > 0) {
            $errors[] = "Nama produk sudah digunakan oleh produk lain.";
        }
    }

    if ($price === false || $price <= 0) $errors[] = "Harga harus lebih besar dari 0.";
    if ($stock === false || $stock < 0) $errors[] = "Stok harus berupa angka positif atau 0.";

    if (empty($errors)) {
        $stmt_update = $pdo->prepare("UPDATE products SET name = :name, category = :category, price = :price, stock = :stock WHERE id = :id");
        $stmt_update->execute([':name' => $name, ':category' => $category, ':price' => $price, ':stock' => $stock, ':id' => $id]);
        header("Location: index.php?status=updated");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - Sweetie Dessert</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { background-color: #ffccd5; font-family: 'Poppins', sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card-custom { border: none; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); background: #ffffff; width: 100%; max-width: 500px; }
        .btn-pink { background-color: #e91e63; color: white; border-radius: 25px; font-weight: 600; }
        .btn-pink:hover { background-color: #c2185b; color: white; }
    </style>
</head>
<body>

<div class="container p-3">
    <div class="card card-custom mx-auto p-4">
        <h4 class="fw-bold mb-4 text-center" style="color: #e91e63;">Edit Dessert</h4>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger rounded-3">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error, ENT_QUOTES, "UTF-8"); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="edit.php?id=<?= $id; ?>" method="POST">
            <div class="mb-3">
                <label class="form-label text-muted small fw-bold">Nama Dessert</label>
                <input type="text" name="name" class="form-control rounded-3" value="<?= htmlspecialchars($_POST['name'] ?? $product['name'], ENT_QUOTES, "UTF-8"); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted small fw-bold">Kategori</label>
                <input type="text" name="category" class="form-control rounded-3" value="<?= htmlspecialchars($_POST['category'] ?? $product['category'], ENT_QUOTES, "UTF-8"); ?>" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted small fw-bold">Harga (Rp)</label>
                    <input type="number" step="0.01" name="price" class="form-control rounded-3" value="<?= htmlspecialchars($_POST['price'] ?? $product['price'], ENT_QUOTES, "UTF-8"); ?>" required>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label text-muted small fw-bold">Stok</label>
                    <input type="number" name="stock" class="form-control rounded-3" value="<?= htmlspecialchars($_POST['stock'] ?? $product['stock'], ENT_QUOTES, "UTF-8"); ?>" required>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center pt-2">
                <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4">Batal</a>
                <button type="submit" class="btn btn-pink px-4 py-2">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>