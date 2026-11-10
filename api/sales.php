<?php
// api/sales.php
require_once __DIR__ . '/../helpers.php';
need_role(['owner']);

out([
    'daily' => $pdo->query(
        "SELECT DATE(order_date) AS day, COUNT(*) AS orders, SUM(total) AS revenue
         FROM orders WHERE status IN ('paid', 'done')
         GROUP BY DATE(order_date) ORDER BY day"
    )->fetchAll(),
    'top' => $pdo->query(
        "SELECT d.product_name, SUM(d.quantity) AS qty
         FROM order_details d 
         JOIN orders o ON o.id = d.order_id
         WHERE o.status IN ('paid', 'done')
         GROUP BY d.product_name ORDER BY qty DESC LIMIT 5"
    )->fetchAll()
]);