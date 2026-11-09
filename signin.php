<?php
// signin.php
$currPage = 'signin';
include 'config.php';
include 'auth.php';
include 'header.php';

$error = "";

if (isset($_POST['signin'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = mysqli_prepare($conn, 
            "SELECT u.id, u.username, u.password, r.name AS role 
             FROM users u 
             JOIN roles r ON r.id = u.role_id
             WHERE u.username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true); // Prevent session fixation
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        header("Location: index.php");
        exit;
    } else {
        $error = "Incorrect username or password.";
    }
}
?>

<div class="container d-flex align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="card shadow-lg border-0 rounded-4" style="max-width: 400px; width: 100%;">
        <div class="card-body p-5">
            <a href="index.php" class="text-decoration-none text-muted mb-4 d-inline-block fw-semibold">&lt;-- Back</a>
            
            <div class="text-center mb-4">
                <h3 class="fw-bold">Welcome Back!</h3>
                <p class="text-black small">Please sign in to your account</p>
            </div>
            
            <?php if ($error): ?>
                <div id="error-alert" class="alert alert-danger py-2 rounded-3 small"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control rounded-3" id="username" name="username" placeholder="Username" required autofocus>
                    <label for="username">Username</label>
                </div>
                <div class="form-floating mb-4">
                    <input type="password" class="form-control rounded-3" id="password" name="password" placeholder="Password" required>
                    <label for="password">Password</label>
                </div>
                <button type="submit" name="signin" class="btn btn-primary w-100 py-2 rounded-3 fw-bold">Sign In</button>
            </form>
            
            <div class="text-center mt-4">
                <p class="small text-black mb-0">Don't have an account? <a href="signup.php" class="text-decoration-none fw-semibold">Sign Up</a></p>
            </div>
        </div>
    </div>
</div>

<script>
    setTimeout(function() {
        const alertBox = document.getElementById('error-alert');
        if (alertBox) {
            alertBox.style.transition = "opacity 0.5s ease";
            alertBox.style.opacity = "0";
            setTimeout(() => alertBox.remove(), 500); 
        }
    }, 5000); // 5 seconds
</script>

<?php include 'footer.php' ?>