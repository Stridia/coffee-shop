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

<style>
    body {
        background: url('https://static.vecteezy.com/system/resources/thumbnails/007/100/608/small/abstract-geometric-white-and-gray-on-light-silver-gradient-background-modern-banner-design-illustration-free-vector.jpg') no-repeat center center fixed;
        background-size: cover;
    }
    
    .bg-blur-overlay {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        background-color: rgba(255, 255, 255, 0.1); 
        z-index: -1;
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.65) !important;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2) !important;
    }

    .glass-card .form-control {
        background: rgba(255, 255, 255, 0.7);
        border: 1px solid rgba(255, 255, 255, 0.9);
    }
</style>

<div class="bg-blur-overlay"></div>

<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="card glass-card rounded-4" style="max-width: 400px; width: 100%;">
        <div class="card-body p-5">
            <a href="index.php" class="text-decoration-none text-muted mb-4 d-inline-block fw-semibold">&lt;-- Back</a>
            
            <div class="text-center mb-4">
                <h3 class="fw-bold text-black">Welcome Back</h3>
                <p class="text-black small">Please sign in to your account</p>
            </div>
            
            <?php if ($error): ?>
                <div id="error-alert" class="alert alert-danger py-2 rounded-3 small border-0"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control rounded-3 text-black" id="username" name="username" placeholder="Username" required autofocus>
                    <label for="username" class="text-dark">Username</label>
                </div>
                <div class="form-floating mb-4">
                    <input type="password" class="form-control rounded-3 text-black" id="password" name="password" placeholder="Password" required>
                    <label for="password" class="text-dark">Password</label>
                </div>
                <button type="submit" name="signin" class="btn btn-primary w-100 py-2 rounded-3 fw-bold">Sign In</button>
            </form>
            
            <div class="text-center mt-4">
                <p class="small text-black mb-0">Don't have an account? <a href="signup.php" class="text-decoration-none fw-bold text-primary">Sign Up</a></p>
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