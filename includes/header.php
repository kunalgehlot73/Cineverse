<header>
    <h2 class="logo">CineVerse</h2>
    <nav class="navigation">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="search.php">Movies</a></li>
            <li><a href="https://github.com/kunalgehlot73">About</a></li>
        </ul>
    </nav>
    <div>
    <?php if (isset($_SESSION['user_id'])): ?>
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
            <a href="admin.php" class="login-button">Admin</a>
        <?php endif; ?>
        <a href="profile.php" class="login-button">Profile</a>
        <a href="logout.php" class="login-button">Logout</a>
    <?php else: ?>
        <a href="auth.php" class="login-button">Login</a>
    <?php endif; ?>
    </div>
</header>