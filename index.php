<?php
require_once 'config.php';
require_once 'csrf.php';

$success_message = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'created') $success_message = "Produk berhasil ditambahkan! 🍰";
    elseif ($_GET['status'] === 'updated') $success_message = "Produk berhasil diperbarui! ✨";
    elseif ($_GET['status'] === 'deleted') $success_message = "Produk telah dihapus. 🗑️";
}

$stmt = $pdo->prepare("SELECT * FROM products ORDER BY id DESC");
$stmt->execute();
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DESSERT SWEETIE</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Montserrat:wght@500;600;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #ffccd5;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            scroll-behavior: smooth;
        }

        .navbar-custom {
            background-color: #ffccd5;
            padding: 15px 40px;
        }
        
        /* Styling untuk Logo Web */
        .navbar-logo {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 50%; /* Membuat logo tetap berbentuk lingkaran manis */
        border: 2px solid #c2185b;
        background-color: #fff;
    }

        .navbar-brand-text {
            font-family: 'Great Vibes', cursive;
            color: #c2185b;
            font-size: 2.3rem;
            text-decoration: none;
            letter-spacing: 1.5px;
            font-weight: bold;
        }
        .navbar-brand-text:hover {
            color: #880e4f;
        }

        .nav-link-custom {
            font-family: 'Montserrat', sans-serif;
            color: #880e4f;
            font-weight: 600;
            font-size: 1rem;
            letter-spacing: 0.5px;
            margin-right: 25px;
            text-decoration: none;
            cursor: pointer;
            transition: color 0.2s;
            text-transform: uppercase;
        }
        .nav-link-custom:hover {
            color: #d81b60;
        }

        .btn-tambah-top {
            background-color: #e91e63;
            color: white;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
            border-radius: 20px;
            padding: 8px 22px;
            text-decoration: none;
            transition: 0.3s;
            box-shadow: 0 4px 10px rgba(233, 30, 99, 0.3);
        }
        .btn-tambah-top:hover {
            background-color: #c2185b;
            color: white;
        }

        .hero-banner-container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }
        .hero-banner-img {
            width: 100%;
            height: auto;
            max-height: 380px;
            object-fit: cover;
            display: block;
        }

        .carousel-indicators [data-bs-target] {
            background-color: #e91e63;
            height: 8px;
            border-radius: 10px;
            width: 25px;
        }

        .catalog-section {
            padding: 30px 40px 60px 40px;
            background-color: #ffccd5;
        }

        .product-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 10px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: transform 0.2s ease-in-out;
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        .product-card:hover {
            transform: translateY(-4px);
        }

        .product-img {
            width: 100%;
            height: 125px;
            object-fit: cover;
            border-radius: 12px;
            background-color: #f8f9fa;
        }

        .product-category {
            color: #757575;
            font-size: 0.80rem;
            margin-top: 6px;
            margin-bottom: 2px;
        }

        .product-name {
            font-weight: 600;
            font-size: 0.88rem;
            color: #212121;
            margin-bottom: 2px;
        }

        .product-price {
            color: #e91e63;
            font-weight: bold;
            font-size: 0.83rem;
            margin-bottom: 4px;
        }

        .product-stock {
            font-size: 0.75rem;
            color: #495057;
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 3px 8px;
            display: inline-block;
            margin-bottom: 6px;
            border: 1px solid #dee2e6;
        }

        .action-btns {
            margin-top: auto;
            display: flex;
            justify-content: center;
            gap: 6px;
            padding-top: 6px;
            border-top: 1px solid #f5f5f5;
        }
    </style>
</head>
<body id="home-top">

<nav class="navbar navbar-custom d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-3">
        <!-- GAMBAR LOGO DI POJOK KIRI -->
        <a href="#home-top">
            <img src="image/logo.png" alt="Logo" class="navbar-logo" onerror="this.src='https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=100&q=80'">
        </a>

        <a class="navbar-brand-text" href="#home-top">Dessert Sweetie</a>
        
        <a class="nav-link-custom ms-3" href="#home-top">Home</a>
        <a class="nav-link-custom" href="#produk-section">Product</a>
    </div>

    <a href="create.php" class="btn-tambah-top">+ Tambah Produk</a>
</nav>

<div class="container my-2">
    <div id="bannerCarousel" class="carousel slide hero-banner-container" data-bs-ride="carousel" data-bs-interval="3000">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>

        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="image/1.png" onerror="this.src='https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=1200&q=80'" class="d-block w-100 hero-banner-img" alt="Banner 1">
            </div>
            <div class="carousel-item">
                <img src="image/2.png" onerror="this.src='https://images.unsplash.com/photo-1565958011703-44f9829ba187?w=1200&q=80'" class="d-block w-100 hero-banner-img" alt="Banner 2">
            </div>
            <div class="carousel-item">
                <img src="image/3.png" onerror="this.src='https://images.unsplash.com/photo-1535141192574-5d4897c13136?w=1200&q=80'" class="d-block w-100 hero-banner-img" alt="Banner 3">
            </div>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Sebelumnya</span>
        </button>

        <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Selanjutnya</span>
        </button>
    </div>
</div>

<div class="catalog-section" id="produk-section">
    <div class="container-fluid">
        
        <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
                <?= htmlspecialchars($success_message, ENT_QUOTES, "UTF-8"); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3">
            <?php 
            $fallback_img = "https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=300&q=80";

            foreach ($products as $product): 
                $img_name = !empty($product['image']) ? $product['image'] : (!empty($product['foto']) ? $product['foto'] : '');
                $product_img_src = !empty($img_name) ? "image/" . $img_name : $fallback_img;
                
                $stock_val = isset($product['stock']) ? $product['stock'] : 0;
            ?>
                <div class="col">
                    <div class="product-card">
                        <img src="<?= htmlspecialchars($product_img_src, ENT_QUOTES, "UTF-8"); ?>" 
                             onerror="this.src='<?= $fallback_img; ?>'" 
                             class="product-img" 
                             alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, "UTF-8"); ?>">
                        
                        <div class="product-category"><?= htmlspecialchars($product['category'], ENT_QUOTES, "UTF-8"); ?></div>
                        <div class="product-name"><?= htmlspecialchars($product['name'], ENT_QUOTES, "UTF-8"); ?></div>
                        <div class="product-price">Rp <?= number_format($product['price'], 0, ',', '.'); ?></div>
                        
                        <div>
                            <span class="product-stock">Stok: <strong><?= $stock_val; ?></strong></span>
                        </div>

                        <div class="action-btns">
                            <a href="edit.php?id=<?= $product['id']; ?>" class="btn btn-sm btn-light text-secondary border px-2 py-0" style="font-size: 0.75rem;">Edit</a>
                            
                            <form action="delete.php" method="POST" class="d-inline" onsubmit="return confirm('Hapus produk ini?');">
                                <input type="hidden" name="id" value="<?= $product['id']; ?>">
                                <input type="hidden" name="csrf_token" value="<?= generate_csrf_token(); ?>">
                                <button type="submit" class="btn btn-sm btn-light text-danger border px-2 py-0" style="font-size: 0.75rem;">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($products)): ?>
            <div class="text-center text-muted py-5">Belum ada produk. Klik tombol <strong>+ Tambah Produk</strong> di kanan atas untuk menambah data.</div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>