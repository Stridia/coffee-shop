<?php
// api/auth.php
require_once __DIR__ . '/../helpers.php';

$action = $_GET['action'] ?? '';
$in = body();

switch ($action) {
    case 'register':
        $username = trim($in['username'] ?? '');
        $email = strtolower(trim($in['email'] ?? ''));
        $pass = $in['password'] ?? '';

        if ($username === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 6) {
            out(['message' => 'Username, valid email, and password (min 6 chars) are required'], 400);
        }

        try {
            // Self-registration defaults to Customer (role_id = 3)
            $st = $pdo->prepare("INSERT INTO users (role_id, username, password, email) VALUES (3, ?, ?, ?)");
            $st->execute([$username, password_hash($pass, PASSWORD_DEFAULT), $email]);
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                out(['message' => 'Email already registered'], 409);
            }
            throw $e;
        }
        out(['message' => 'Registration successful. You can log in now.'], 201);

    case 'login':
        $st = $pdo->prepare("SELECT u.id, u.username, u.password, u.role_id, r.name AS role 
                             FROM users u 
                             JOIN roles r ON r.id = u.role_id 
                             WHERE u.email = ? OR u.username = ?");
        $identifier = strtolower(trim($in['email'] ?? $in['username'] ?? ''));
        $st->execute([$identifier, $identifier]);
        $u = $st->fetch();

        if (!$u || !password_verify($in['password'] ?? '', $u['password'])) {
            out(['message' => 'Invalid credentials'], 401);
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $u['id'];
        $_SESSION['username'] = $u['username'];
        $_SESSION['role_id'] = (int) $u['role_id'];
        $_SESSION['role'] = $u['role'];

        out([
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'role' => $_SESSION['role']
        ]);

    case 'logout':
        session_destroy();
        out(['message' => 'Logged out successfully']);

    case 'me':
        out(me() ?? ['role' => 'guest']);

    default:
        out(['message' => 'Unknown action'], 404);
}