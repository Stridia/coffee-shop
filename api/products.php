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
                LEFT JOIN reviews r ON r.product_id = p.id";
        $args = [];

        if (!empty($_GET['category'])) {
            $sql .= " WHERE t.name = ?";
            $args[] = $_GET['category'];
        }

        $sql .= " GROUP BY p.id, p.name, p.description, p.price, p.image, p.status, t.id, t.name ORDER BY t.id, p.name";
        $st = $pdo->prepare($sql);
        $st->execute($args);
        $rows = $st->fetchAll();

        $safeUser = is_array($user) ? $user : ['role' => 'guest'];
        foreach ($rows as &$r) {
            $r['price'] = (int) $r['price'];
            $r['final_price'] = final_price($r['price'], $safeUser);
            $r['image'] = image_url($r['image']);
        }

        out([
            'categories' => $pdo->query("SELECT id, name FROM types ORDER BY id")->fetchAll(),
            'products' => $rows
        ]);
        break;

    case 'POST':
        need_role(['owner', 'admin']);
        $in = body();
        $name = trim($in['name'] ?? '');
        $type_id = (int) ($in['type_id'] ?? 0);
        $cafe_id = (int) ($in['cafe_id'] ?? 1);
        $price = (int) ($in['price'] ?? 0);
        $image = trim($in['image'] ?? '');
        $desc = trim($in['description'] ?? '');
        $status = trim($in['status'] ?? 'available');

        if ($name === '' || $type_id < 1 || $price < 1) {
            out(['message' => 'Name, category, and price are required'], 400);
        }

        $st = $pdo->prepare("INSERT INTO products (cafe_id, type_id, name, description, image, price, status) 
                             VALUES (?, ?, ?, ?, ?, ?, ?)");
        $st->execute([$cafe_id, $type_id, $name, $desc, $image, $price, $status]);

        out(['message' => 'Product created', 'id' => (int) $pdo->lastInsertId()], 201);
        break;

    case 'PUT':
        need_role(['owner', 'admin']);
        $id = (int) ($_GET['id'] ?? 0);
        $in = body();

        if ($id < 1) out(['message' => 'Product ID is required'], 400);

        $fields = [];
        $params = [];

        if (array_key_exists('name', $in)) { $fields[] = 'name = ?'; $params[] = trim($in['name']); }
        if (array_key_exists('type_id', $in)) { $fields[] = 'type_id = ?'; $params[] = (int) $in['type_id']; }
        if (array_key_exists('price', $in)) { $fields[] = 'price = ?'; $params[] = (int) $in['price']; }
        if (array_key_exists('status', $in)) { $fields[] = 'status = ?'; $params[] = trim($in['status']); }
        if (array_key_exists('description', $in)) { $fields[] = 'description = ?'; $params[] = trim($in['description']); }
        if (array_key_exists('image', $in)) { $fields[] = 'image = ?'; $params[] = trim($in['image']); }

        if (!$fields) out(['message' => 'No fields to update'], 400);

        $params[] = $id;
        $sql = "UPDATE products SET " . implode(', ', $fields) . " WHERE id = ?";
        $st = $pdo->prepare($sql);
        $st->execute($params);

        out(['message' => 'Product updated successfully']);
        break;

    case 'DELETE':
        need_role(['owner', 'admin']);
        $id = (int) ($_GET['id'] ?? 0);

        if ($id < 1) out(['message' => 'Product ID is required'], 400);

        try {
            $st = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $st->execute([$id]);

            if ($st->rowCount() === 0) {
                out(['message' => 'Product not found'], 404);
            }

            out(['message' => 'Product deleted permanently']);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                out(['message' => 'Cannot delete this item because it is linked to existing order history. Edit its status to unavailable instead.'], 409);
            }
            throw $e;
        }
        break;

    default:
        out(['message' => 'Method not allowed'], 405);
}