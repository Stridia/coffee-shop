<?php
// signup.php
$currPage = 'signup';
include 'config.php';
include 'auth.php';
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

<div class="container d-flex align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="card shadow-lg border-0 rounded-4" style="max-width: 400px; width: 100%;">
        <div class="card-body p-5">
            <a href="index.php" class="text-decoration-none text-muted mb-4 d-inline-block fw-semibold">&lt;-- Back</a>
            
            <div class="text-center mb-4">
                <h3 class="fw-bold">Create Account</h3>
                <p class="text-black small">Join us to start ordering</p>
            </div>
            
            <?php if ($error): ?>
                <div id="error-alert" class="alert alert-danger py-2 rounded-3 small"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control rounded-3" id="username" name="username" placeholder="Username" required autofocus>
                    <label for="username">Username</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="email" class="form-control rounded-3" id="email" name="email" placeholder="name@example.com" required>
                    <label for="email">Email address</label>
                </div>
                <div class="form-floating mb-4">
                    <input type="password" class="form-control rounded-3" id="password" name="password" placeholder="Password" required>
                    <label for="password">Password</label>
                </div>
                <button type="submit" name="signup" class="btn btn-dark w-100 py-2 rounded-3 fw-bold">Sign Up</button>
            </form>
            
            <div class="text-center mt-4">
                <p class="small text-black mb-0">Already have an account? <a href="signin.php" class="text-decoration-none fw-semibold">Sign In</a></p>
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