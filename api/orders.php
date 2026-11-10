<?php
// api/orders.php
require_once __DIR__ . '/../helpers.php';

$user = me();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        $in = body();
        $qty = [];
        foreach (($in['items'] ?? []) as $it) {
            $pid = (int) ($it['product_id'] ?? 0);
            $q = (int) ($it['qty'] ?? $it['quantity'] ?? 0);
            if ($pid > 0 && $q > 0) $qty[$pid] = ($qty[$pid] ?? 0) + min($q, 50);
        }

        if (!$qty) out(['message' => 'Your order is empty'], 400);

        $ph = implode(',', array_fill(0, count($qty), '?'));
        $st = $pdo->prepare("SELECT id, name, price FROM products WHERE status = 'available' AND id IN ($ph)");
        $st->execute(array_keys($qty));
        $prods = $st->fetchAll();

        if (count($prods) !== count($qty)) out(['message' => 'Some products are unavailable'], 400);

        $subtotal = 0;
        foreach ($prods as $p) {
            $subtotal += $p['price'] * $qty[$p['id']];
        }

        $discount = $subtotal - array_sum(array_map(
            fn($p) => final_price((int) $p['price'], $user) * $qty[$p['id']],
            $prods
        ));
        $total = $subtotal - $discount;

        // Cashier auto-assignment (role_id = 2)
        $cashierId = $pdo->query(
            "SELECT u.id FROM users u
             LEFT JOIN orders o ON o.cashier_id = u.id AND o.status <> 'done'
             WHERE u.role_id = 2
             GROUP BY u.id ORDER BY COUNT(o.id), u.id LIMIT 1"
        )->fetchColumn();

        // Standard user_id fallback for guest checkout
        $userId = $user['id'] ?? $pdo->query("SELECT id FROM users WHERE role_id = 3 LIMIT 1")->fetchColumn();
        $code = 'KP-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
        $guestName = trim($in['customer_name'] ?? '') ?: 'Guest';

        $pdo->beginTransaction();
        try {
            $stOrder = $pdo->prepare(
                "INSERT INTO orders (order_code, user_id, order_date, customer_name, cashier_id, subtotal, discount, total, status)
                 VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, 'pending')"
            );
            $stOrder->execute([$code, $userId, $user['username'] ?? $guestName, $cashierId ?: null, $subtotal, $discount, $total]);
            $orderId = (int) $pdo->lastInsertId();

            $stDetail = $pdo->prepare(
                "INSERT INTO order_details (order_id, product_id, quantity, product_name, unit_price)
                 VALUES (?, ?, ?, ?, ?)"
            );
            foreach ($prods as $p) {
                $stDetail->execute([$orderId, $p['id'], $qty[$p['id']], $p['name'], $p['price']]);
            }

            // Sync: clear carts table if user is logged in
            if ($user) {
                $stCart = $pdo->prepare("DELETE FROM carts WHERE user_id = ?");
                $stCart->execute([$user['id']]);
            }

            $pdo->commit();
            out(['order_code' => $code, 'subtotal' => $subtotal, 'discount' => $discount, 'total' => $total], 201);
        } catch (Throwable $e) {
            $pdo->rollBack();
            out(['message' => 'Failed to place order: ' . $e->getMessage()], 500);
        }

    case 'GET':
        $u = need_role(['owner', 'cashier', 'customer']);
        $sql = "SELECT o.id, o.order_code, o.order_date, o.customer_name, o.subtotal, o.discount, o.total,
                       o.status, c.username AS cashier,
                       (SELECT GROUP_CONCAT(CONCAT(d.quantity, 'x ', d.product_name) SEPARATOR ', ')
                        FROM order_details d WHERE d.order_id = o.id) AS items
                FROM orders o 
                LEFT JOIN users c ON c.id = o.cashier_id";
        $args = [];

        if ($u['role'] === 'cashier') {
            $sql .= " WHERE o.cashier_id = ?";
            $args[] = $u['id'];
        }
        if ($u['role'] === 'customer') {
            $sql .= " WHERE o.user_id = ?";
            $args[] = $u['id'];
        }

        $st = $pdo->prepare($sql . " ORDER BY o.id DESC LIMIT 200");
        $st->execute($args);
        out($st->fetchAll());

    case 'PUT':
        $u = need_role(['owner', 'cashier']);
        $id = (int) ($_GET['id'] ?? 0);
        $status = body()['status'] ?? '';

        if (!in_array($status, ['pending', 'paid', 'done'], true)) {
            out(['message' => 'Invalid status'], 400);
        }

        $sql = "UPDATE orders SET status = ? WHERE id = ?";
        $args = [$status, $id];
        if ($u['role'] === 'cashier') {
            $sql .= " AND cashier_id = ?";
            $args[] = $u['id'];
        }

        $st = $pdo->prepare($sql);
        $st->execute($args);

        if ($st->rowCount() === 0) out(['message' => 'Order not found'], 404);
        out(['message' => 'Status updated']);

    default:
        out(['message' => 'Method not allowed'], 405);
}