<?php
// helpers.php
require_once __DIR__ . '/config.php';

function out($data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function body(): array {
    static $b = null;
    if ($b === null) {
        $b = json_decode(file_get_contents('php://input'), true) ?? [];
    }
    return $b;
}

function me(): ?array {
    if (!isset($_SESSION['user_id'])) return null;
    return [
        'id' => (int) $_SESSION['user_id'],
        'username' => $_SESSION['username'] ?? 'User',
        'role_id' => (int) ($_SESSION['role_id'] ?? 3),
        'role' => $_SESSION['role'] ?? 'customer'
    ];
}

function need_role(array $roles): array {
    $u = me();
    if (!$u) out(['message' => 'Please log in'], 401);
    if (!in_array($u['role'], $roles, true)) out(['message' => 'Forbidden'], 403);
    return $u;
}

function final_price(int $price, ?array $user): int {
    $member = $user && $user['role'] === 'customer';
    return $member ? (int) round($price * (1 - DISCOUNT_RATE)) : $price;
}

function image_url(?string $img): ?string {
    if (!$img) return null;
    return preg_match('#^https?://#', $img) ? $img : 'uploads/' . $img;
}

if (!$_SESSION) {
    $_SESSION['role'] = '';
}

function is_signin() {
    return isset($_SESSION['username']);
}

function is_admin() {
    return $_SESSION['role'] === 'owner';
}

function is_customer() {
    return $_SESSION['role'] === 'customer';
}

function require_admin() {
    if (!is_admin()) {
        http_response_code(403);
        die("403 - Access denied. This page is for admins only.");
    }
}

function get_username() {
    return $_SESSION['username'];
}