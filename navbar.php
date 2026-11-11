<nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
  <div class="container">
    <a class="navbar-brand" href="#">Kopipiro</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarColor01">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link <?= (isset($currPage) && $currPage == 'home') ? 'active' : '' ?>" href="index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= (isset($currPage) && $currPage == 'menu') ? 'active' : '' ?>" href="menu.php">Menu</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= (isset($currPage) && $currPage == 'order_history') ? 'active' : '' ?>"" href="order_history.php">Order History</a>
        </li>
        <?php if (is_staff()):?>
        <li class="nav-item">
          <a class="nav-link <?= (isset($currPage) && $currPage == 'staff_dashboard') ? 'active' : '' ?>"" href="staff_dashboard.php">Staff Dashboard</a>
        </li>
        <?php endif; ?>
        <?php if (is_admin()):?>
        <li class="nav-item">
          <a class="nav-link <?= (isset($currPage) && $currPage == 'menu_management') ? 'active' : '' ?>"" href="menu_management.php">Menu Management</a>
        </li>
        <?php endif; ?>
      </ul>
      
      <!-- Profile Dropdown / Sign In Button -->
      <div class="d-flex align-items-center gap-2">
            <?php if (is_signin()):?>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 text-white pe-3" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">

                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['username']) ?>&background=random&color=fff" alt="Profile" class="rounded-circle" width="36" height="36">
                        
                        <div class="d-flex flex-column text-start lh-sm">
                            <span class="fw-bold"><?= htmlspecialchars($_SESSION['username']) ?></span>
                            <small class="text-white-50" style="font-size: 0.75rem; text-transform: capitalize;">
                                <?= htmlspecialchars($_SESSION['role']) ?>
                            </small>
                        </div>
                    </a>
                    
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="#">Account Info</a></li>
                        <li><a class="dropdown-item" href="#">Order History</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="signout.php">Sign Out</a></li>
                    </ul>
                </li>
            </ul>
            <?php else: ?>
            <a href="signin.php" class="btn btn-secondary btn-sm">Sign In</a>
            <a href="signup.php" class="btn btn-dark btn-sm">Sign Up</a>
            <?php endif; ?>
        </div>
    </div>
  </div>
</nav>