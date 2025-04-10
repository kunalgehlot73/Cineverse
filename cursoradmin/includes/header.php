<header>
    <h2 class="logo">CineVerse</h2>
    <nav class="navigation">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="search.php">Movies</a></li>
            <li><a href="about.php">About</a></li>
        </ul>
    </nav>
    <div>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="profile.php" class="login-button"><?= htmlspecialchars($_SESSION['username']) ?></a>
        <a href="logout.php" class="login-button">Logout</a>
    <?php else: ?>
        <a href="auth.php" class="login-button">Login</a>
    <?php endif; ?>
    </div>
</header>