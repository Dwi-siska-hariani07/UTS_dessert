<?php
require_once 'config.php';
require_once 'csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id    = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $token = $_POST['csrf_token'] ?? '';

    // Validasi CSRF Token
    if (!verify_csrf_token($token)) {
        die("Akses Ditolak: Validasi CSRF Token gagal.");
    }

    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }
}

header("Location: index.php?status=deleted");
exit();
?>