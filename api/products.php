<?php
// api/products.php
require_once __DIR__ . '/../helpers.php';

$user = me();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $sql = "SELECT p.id, p.name, p.description, p.price, p.image, p.status, 
                       t.name AS category,
                       ROUND(AVG(r.rating), 1) AS rating, 
                       COUNT(r.id) AS reviews
                FROM products p
                JOIN types t ON t.id = p.type_id
                LEFT JOIN reviews r ON r.product_id = p.id
                WHERE p.status = 'available'";
        $args = [];

        if (!empty($_GET['category'])) {
            $sql .= " AND t.name = ?";
            $args[] = $_GET['category'];
        }

        $sql .= " GROUP BY p.id, t.name ORDER BY t.id, p.name";
        $st = $pdo->prepare($sql);
        $st->execute($args);
        $rows = $st->fetchAll();

        foreach ($rows as &$r) {
            $r['price'] = (int) $r['price'];
            $r['final_price'] = final_price($r['price'], $user);
            $r['image'] = image_url($r['image']);
        }

        out([
            'categories' => $pdo->query("SELECT id, name FROM types ORDER BY id")->fetchAll(),
            'products' => $rows
        ]);

    case 'POST':
        need_role(['owner']);
        $name = trim($_POST['name'] ?? '');
        $type_id = (int) ($_POST['type_id'] ?? $_POST['category_id'] ?? 0);
        $cafe_id = (int) ($_POST['cafe_id'] ?? 1);
        $price = (int) ($_POST['price'] ?? 0);

        if ($name === '' || $type_id < 1 || $price < 1) {
            out(['message' => 'Name, type_id, and price are required'], 400);
        }

        $file = '';
        if (!empty($_FILES['image']['name'])) {
            $f = $_FILES['image'];
            if ($f['error'] !== UPLOAD_ERR_OK || $f['size'] > 2 * 1024 * 1024) {
                out(['message' => 'Image upload failed or exceeds 2MB limit'], 400);
            }
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
            $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
            $ext = $allowed[$mime] ?? null;
            if (!$ext) out(['message' => 'Only JPG, PNG or WebP images are allowed'], 400);

            $file = bin2hex(random_bytes(8)) . '.' . $ext;
            if (!is_dir(__DIR__ . '/../uploads')) {
                mkdir(__DIR__ . '/../uploads', 0777, true);
            }
            move_uploaded_file($f['tmp_name'], __DIR__ . '/../uploads/' . $file);
        }

        $st = $pdo->prepare("INSERT INTO products (cafe_id, type_id, name, description, image, price, status) 
                             VALUES (?, ?, ?, ?, ?, ?, 'available')");
        $st->execute([$cafe_id, $type_id, $name, trim($_POST['description'] ?? ''), $file, $price]);

        out(['message' => 'Product created', 'id' => (int) $pdo->lastInsertId()], 201);

    case 'PUT':
        need_role(['owner']);
        $id = (int) ($_GET['id'] ?? 0);
        $price = (int) (body()['price'] ?? 0);
        $status = body()['status'] ?? 'available';

        if ($id < 1) out(['message' => 'Product ID is required'], 400);

        $st = $pdo->prepare("UPDATE products SET price = ?, status = ? WHERE id = ?");
        $st->execute([$price, $status, $id]);

        if ($st->rowCount() === 0) out(['message' => 'Product not found or unchanged'], 404);
        out(['message' => 'Product updated successfully']);

    default:
        out(['message' => 'Method not allowed'], 405);
}