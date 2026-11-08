<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">CSK</a>
        <div class="d-flex align-items-center gap-2">
            <?php if (is_signin()):?>
            <span class="text-white small">
                <?= htmlspecialchars($_SESSION['username']) ?>
                <span class="badge bg-secondary"><?= $_SESSION['role'] ?></span>
            </span>
            <a href="signout.php" class="btn btn-danger btn-sm">Sign Out</a>
            <?php else: ?>
            <a href="signin.php" class="btn btn-primary btn-sm">Sign In</a>
            <a href="signup.php" class="btn btn-secondary btn-sm">Sign Up</a>
            <?php endif; ?>
        </div>
    </div>
</nav>