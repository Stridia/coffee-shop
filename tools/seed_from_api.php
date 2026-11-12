<?php
// seed/seed_from_api.php
require_once __DIR__ . '/../config.php';

$ch = curl_init('https://api.sampleapis.com/coffee/hot');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 20
]);
$resp = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$list = json_decode((string) $resp, true);
if ($code !== 200 || !is_array($list)) {
    exit("Public Coffee API fetch failed (HTTP $code).\n");
}

$coffeeTypeId = $pdo->query("SELECT id FROM types WHERE name = 'Coffee'")->fetchColumn();
$checkExists = $pdo->prepare("SELECT COUNT(*) FROM products WHERE name = ?");
$stIns = $pdo->prepare("INSERT INTO products (cafe_id, type_id, name, description, image, price, status) VALUES (1, ?, ?, ?, ?, ?, 'available')");

foreach ($list as $c) {
    $checkExists->execute([$c['title']]);
    if ($checkExists->fetchColumn()) continue;

    $desc = substr($c['description'] . ' Ingredients: ' . implode(', ', $c['ingredients'] ?? []), 0, 255);
    $price = 20000 + ($c['id'] % 6) * 2500;
    $stIns->execute([$coffeeTypeId, $c['title'], $desc, $c['image'] ?? '', $price]);
}

// Demo ratings seed
$customerId = $pdo->query("SELECT id FROM users WHERE role_id = 3 LIMIT 1")->fetchColumn();
$stReview = $pdo->prepare("INSERT INTO reviews (product_id, user_id, rating, comment) VALUES (?, ?, ?, 'Great taste!')");
$unreviewed = $pdo->query("SELECT id FROM products WHERE id NOT IN (SELECT product_id FROM reviews)")->fetchAll(PDO::FETCH_COLUMN);

foreach ($unreviewed as $pid) {
    for ($i = 0; $i < 3; $i++) {
        $stReview->execute([$pid, $customerId, random_int(3, 5)]);
    }
}

echo "Imported " . count($list) . " coffee items from public API.\n";