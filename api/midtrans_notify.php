<?php
// api/midtrans_notify.php
require_once __DIR__ . '/../helpers.php';

$n = body();

$expected = hash('sha512',
    ($n['order_id'] ?? '') . ($n['status_code'] ?? '') .
    ($n['gross_amount'] ?? '') . MIDTRANS_SIGNATURE_KEY
);

if (!hash_equals($expected, $n['signature_key'] ?? '')) {
    out(['message' => 'Invalid signature'], 403);
}

$status = $n['transaction_status'] ?? '';
$fraud = $n['fraud_status'] ?? 'accept';
$paid = ($n['status_code'] ?? '') === '200' && ($status === 'settlement' || ($status === 'capture' && $fraud === 'accept'));

if ($paid) {
    $pdo->prepare("UPDATE orders SET status = 'paid' WHERE order_code = ? AND total = ? AND status = 'pending'")
        ->execute([$n['order_id'], (int) $n['gross_amount']]);
}

out(['ok' => true]);