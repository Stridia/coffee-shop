<?php
// signup.php
$currPage = 'signup';
include 'config.php';
include 'helpers.php';
include 'header.php';

$error = "";

if (isset($_POST['signup'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $role_id = 3;

    $stmt = mysqli_prepare($conn, "SELECT username from users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);

    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$user) {
        $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password, role_id, email) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssis", $username, $password, $role_id, $email);
        mysqli_stmt_execute($stmt);

        $user_id = mysqli_insert_id($conn);

        session_regenerate_id(true); // Prevent session fixation
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = "customer";

        header("Location: index.php");
        exit;
    } else {
        $error = "Username taken. Please try another username.";
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
                <h3 class="fw-bold text-black">Create Account</h3>
                <p class="text-black small">Join us to get a discount!</p>
            </div>
            
            <?php if ($error): ?>
                <div id="error-alert" class="alert alert-danger py-2 rounded-3 small border-0"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control rounded-3 text-black" id="username" name="username" placeholder="Username" required autofocus>
                    <label for="username" class="text-dark">Username</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="email" class="form-control rounded-3 text-black" id="email" name="email" placeholder="name@example.com" required>
                    <label for="email" class="text-dark">Email</label>
                </div>
                <div class="form-floating mb-4">
                    <input type="password" class="form-control rounded-3 text-black" id="password" name="password" placeholder="Password" required>
                    <label for="password" class="text-dark">Password</label>
                </div>
                <button type="submit" name="signup" class="btn btn-dark w-100 py-2 rounded-3 fw-bold">Sign Up</button>
            </form>
            
            <div class="text-center mt-4">
                <p class="small text-black mb-0">Already have an account? <a href="signin.php" class="text-decoration-none fw-bold text-dark">Sign In</a></p>
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