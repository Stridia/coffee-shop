<?php
// seed/seed_base.php
require_once __DIR__ . '/../config.php';

// 1. Seed Cafe
$pdo->exec("INSERT IGNORE INTO cafe (id, name, address, contact_number) 
            VALUES (1, 'Kopipiro Menteng', 'Jl. Menteng No. 12, Surabaya', '081234567890')");

// 2. Seed Roles
$roles = [
    [1, 'owner'],
    [2, 'cashier'],
    [3, 'customer']
];
$stRole = $pdo->prepare("INSERT IGNORE INTO roles (id, name) VALUES (?, ?)");
foreach ($roles as [$id, $name]) {
    $stRole->execute([$id, $name]);
}

// 3. Seed Users
$users = [
    [1, 'Kopipiro Owner', 'owner@kopipiro.test', 'owner123'],
    [2, 'Cashier Ani', 'cashier1@kopipiro.test', 'cashier123'],
    [2, 'Cashier Budi', 'cashier2@kopipiro.test', 'cashier123'],
    [3, 'Demo Customer', 'customer@kopipiro.test', 'customer123'],
];
$stUser = $pdo->prepare("INSERT IGNORE INTO users (role_id, username, password, email) VALUES (?, ?, ?, ?)");
foreach ($users as [$role_id, $username, $email, $pass]) {
    $stUser->execute([$role_id, $username, password_hash($pass, PASSWORD_DEFAULT), $email]);
}

// 4. Seed Types (Product Categories)
foreach (['Coffee', 'Non-Coffee', 'Food'] as $t) {
    $pdo->prepare("INSERT IGNORE INTO types (name) VALUES (?)")->execute([$t]);
}

$findType = $pdo->prepare("SELECT id FROM types WHERE name = ?");
$getTypeId = function ($name) use ($findType) {
    $findType->execute([$name]);
    return $findType->fetchColumn();
};

// 5. Seed Base Non-Coffee & Food Items
$items = [
    ['Non-Coffee', 'Chocolate Milk', 'Cold chocolate with fresh milk', 22000],
    ['Non-Coffee', 'Matcha Latte', 'Japanese green tea with steamed milk', 26000],
    ['Non-Coffee', 'Lemon Tea', 'Black tea with fresh lemon', 15000],
    ['Food', 'Butter Toast', 'Thick toast with butter and sugar', 18000],
    ['Food', 'Chocolate Toast', 'Thick toast with chocolate and cheese', 20000],
];

$stProd = $pdo->prepare("INSERT INTO products (cafe_id, type_id, name, description, image, price, status) VALUES (1, ?, ?, ?, '', ?, 'available')");
foreach ($items as [$typeName, $name, $desc, $price]) {
    $stProd->execute([$getTypeId($typeName), $name, $desc, $price]);
}

echo "Base database seed completed successfully.\n";