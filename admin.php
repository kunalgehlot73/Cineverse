<?php
require_once 'includes/config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}
if ($_SESSION['is_admin'] == 0) {
    header("Location: index.php");
    exit();
}
$admin_search_query = isset($_GET['admin-movie-search']) ? trim($_GET['admin-movie-search']) : '';
$admin_movies = [];

$stmt = $conn->prepare("SELECT * FROM movies WHERE title LIKE ? ORDER BY created_at DESC");
$admin_search_param = "%$admin_search_query%";
$stmt->bind_param("s", $admin_search_param);
$stmt->execute();
$admin_movies_result = $stmt->get_result();
while ($row = $admin_movies_result->fetch_assoc()) {
    $admin_movies[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CineVerse</title>
    <link rel="stylesheet" href="css/headerfooter.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<style>
    .admin-container {
        display: flex;
        min-height: calc(100vh - 80px);
    }

    .admin-sidebar {
        width: 250px;
        background-color: var(--header);
        padding: 2rem;
        border-right: 1px solid rgba(255, 255, 255, 0.1);
    }

    .admin-sidebar-content {
        position: fixed;
    }

    .admin-profile {
        text-align: center;
        margin-bottom: 2rem;
    }

    .admin-avatar {
        width: 80px;
        height: 80px;
        background-color: var(--gold);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
    }

    .admin-avatar i {
        font-size: 2rem;
        color: var(--header);
    }

    .admin-nav ul {
        list-style: none;
    }

    .admin-nav ul li {
        margin-bottom: 1rem;
    }

    .admin-nav ul li a {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.8rem 1rem;
        text-decoration: none;
        color: white;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .admin-nav ul li a i {
        width: 20px;
    }

    .admin-nav ul li a:hover {
        background-color: var(--gold);
        color: var(--header);
    }

    .admin-content {
        flex: 1;
        padding: 2rem;
    }

    .admin-content-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .admin-content-header h1 {
        font-size: 2rem;
        color: var(--gold);
    }

    .admin-movie-form-container {
        background-color: var(--mov_list_item_bg);
        padding: 2rem;
        border-radius: 12px;
        margin-bottom: 2rem;
    }

    .admin-movie-form-container h2 {
        color: var(--gold);
        margin-bottom: 1.5rem;
    }

    .admin-form-group {
        margin-bottom: 1.5rem;
    }

    .admin-form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .admin-form-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: #888;
    }

    .admin-form-group input,
    .admin-form-group textarea {
        width: 100%;
        padding: 0.8rem;
        background-color: var(--mov_ban_bg);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        color: white;
        font-size: 1rem;
    }

    .admin-form-group input:focus,
    .admin-form-group textarea:focus {
        outline: none;
        border-color: var(--gold);
    }

    .admin-form-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }

    .admin-save-btn,
    .admin-cancel-btn {
        padding: 0.8rem 1.5rem;
        border-radius: 8px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .admin-save-btn {
        background-color: var(--gold);
        color: var(--header);
        border: none;
    }

    .admin-cancel-btn {
        background-color: transparent;
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .admin-save-btn:hover,
    .admin-cancel-btn:hover {
        transform: translateY(-2px);
    }

    .admin-movies-table-container {
        background-color: var(--mov_list_item_bg);
        padding: 2rem;
        border-radius: 12px;
    }

    .admin-movies-table-container h2 {
        color: var(--gold);
        margin-bottom: 1.5rem;
    }

    .admin-table-actions {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .admin-search-box {
        flex: 1;
        position: relative;
    }

    .admin-search-box input {
        width: 100%;
        padding: 0.8rem 1rem 0.8rem 2.5rem;
        background-color: var(--mov_ban_bg);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        color: white;
    }

    .admin-search-box button {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #888;
        border: none;
        background: transparent;
        cursor: pointer;
    }

    .admin-movies-table {
        width: 100%;
        border-collapse: collapse;
    }

    .admin-movies-table th,
    .admin-movies-table td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .admin-movies-table th {
        color: var(--gold);
        font-weight: bold;
    }

    .admin-movies-table tr:hover {
        background-color: rgba(255, 255, 255, 0.05);
    }

    .admin-action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .admin-delete-btn {
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .admin-delete-btn:hover {
        color: #ff4444;
        background-color: rgba(255, 68, 68, 0.1);
    }
</style>

<body>
    <?php include 'includes/header.php'; ?>

    <main class="admin-container">
        <div class="admin-sidebar">
            <div class="admin-sidebar-content">
                <div class="admin-profile">
                    <div class="admin-avatar">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h3>Admin Panel</h3>
                </div>
                <nav class="admin-nav">
                    <ul>
                        <li class=""><a href="#movieForm"><i class="fas fa-plus"></i> Add/Edit Movie</a></li>
                        <li class=""><a href="#movie-table"><i class="fas fa-film"></i> Movies</a></li>
                    </ul>
                </nav>
            </div>
        </div>

        <div class="admin-content">
            <?php if (!empty($_SESSION['delete_success'])): ?>
                <div class='alert-success'>
                    <span class='success-text'>
                        <?= $_SESSION['delete_success'] ?>
                        <?php unset($_SESSION['delete_success']); ?>
                    </span>
                </div>
            <?php endif; ?>
            <?php if (!empty($_SESSION['delete_errors'])): ?>
                <div class="alert">
                    <?php foreach ($_SESSION['delete_errors'] as $error): ?>
                        <span class='error-text'><?= $error ?></span>
                    <?php endforeach; ?>
                    <?php unset($_SESSION['delete_errors']); ?>
                </div>
            <?php endif; ?>
            <div class="admin-content-header">
                <h1>Movie Management</h1>
            </div>

            <div class="admin-movie-form-container" id="movieForm">
                <h2>Add Movie</h2>
                <form class="admin-movie-form" method="post" action="admin_process.php">
                    <?php if (!empty($_SESSION['add_success'])): ?>
                        <div class='alert-success'>
                            <span class='success-text'>
                                <?= $_SESSION['add_success'] ?>
                                <?php unset($_SESSION['add_success']); ?>
                            </span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($_SESSION['add_errors'])): ?>
                        <div class="alert">
                            <?php foreach ($_SESSION['add_errors'] as $error): ?>
                                <span class='error-text'><?= $error ?></span>
                            <?php endforeach; ?>
                            <?php unset($_SESSION['add_errors']); ?>
                        </div>
                    <?php endif; ?>
                    <div class="admin-form-group">
                        <label for="movieTitle">Movie Title</label>
                        <input type="text" id="movieTitle" name="admin-movieTitle" required>
                    </div>
                    <div class="admin-form-row">
                        <div class="admin-form-group">
                            <label for="releaseYear">Release Year</label>
                            <input type="number" id="releaseYear" name="admin-releaseYear" required>
                        </div>
                        <div class="admin-form-group">
                            <label for="duration">Duration (minutes)</label>
                            <input type="number" id="duration" name="admin-duration" required>
                        </div>
                    </div>
                    <div class="admin-form-group">
                        <label for="movieDirector">Director</label>
                        <input type="text" id="movieDirector" name="admin-movieDirector" required>
                    </div>
                    <div class="admin-form-group">
                        <label for="movieCast">Cast</label>
                        <input type="text" id="movieCast" name="admin-movieCast" required>
                    </div>
                    <div class="admin-form-group">
                        <label for="genres">Genres</label>
                        <input type="text" id="genres" name="admin-genres" placeholder="Action, Drama, Sci-Fi" required>
                    </div>
                    <div class="admin-form-group">
                        <label for="rating">Rating</label>
                        <input type="number" id="rating" name="admin-rating" min="0" max="10" step="0.1" name="admin-rating" required>
                    </div>
                    <div class="admin-form-group">
                        <label for="synopsis">Synopsis</label>
                        <textarea id="synopsis" rows="4" required name="admin-synopsis"></textarea>
                    </div>
                    <div class="admin-form-group">
                        <label for="posterUrl">Poster URL</label>
                        <input type="text" id="posterUrl" name="admin-poster-url" required>
                    </div>
                    <div class="admin-form-group">
                        <label for="portraitposterUrl">Portrait Poster URL</label>
                        <input type="text" id="portraitposterUrl" name="admin-portrait-poster-url" required>
                    </div>
                    <div class="admin-form-group">
                        <label for="youtube-id">YouTube Video ID</label>
                        <input type="text" id="youtube-id" name="admin-youtube-id" required placeholder="e.g.: HYVxnPmb15E">
                        <small>Found in YouTube URL after ?v=</small>
                    </div>
                    <div class="admin-form-buttons">
                        <button type="submit" name="admin-submit-btn" class="admin-save-btn">Save Movie</button>
                        <button type="reset" class="admin-cancel-btn">Cancel</button>
                    </div>
                </form>
            </div>

            <div class="admin-movies-table-container">
                <h2>Existing Movies</h2>
                <div class="admin-table-actions">
                    <form method="GET" class="admin-search-box">
                        <input type="text" id="admin-movie-search" name="admin-movie-search" placeholder="Search movies...">
                        <button type="submit" class="admin-search-button"><i class="fas fa-search"></i></button>
                    </form>
                </div>
                <table class="admin-movies-table" id="movie-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Year</th>
                            <th>Rating</th>
                            <th>Genres</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (isset($admin_movies)) {
                            foreach ($admin_movies as $movie) {
                                echo "
                                        <tr data-movie-id='{$movie['id']}'>
                                            <td>" . htmlspecialchars($movie['title']) . "</td>
                                            <td>" . htmlspecialchars($movie['release_year']) . "</td>
                                            <td>" . htmlspecialchars(format_rating($movie['rating'])) . "</td>
                                            <td>" . htmlspecialchars($movie['genre']) . "</td>
                                            <td class='admin-action-buttons'>
                                                <form method='post' action='admin_process.php'>
                                                    <input type='hidden' name='movie_id' value='{$movie['id']}'>
                                                    <button type='submit' name='admin-delete-btn' class='admin-delete-btn' onclick='return confirm(\"Are you sure?\")'><i class='fas fa-trash'></i></button>
                                                </form>
                                            </td>
                                        </tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.admin-movies-table tbody tr').forEach(row => {
                row.addEventListener('click', function(e) {
                    if (e.target.closest('button, .admin-delete-btn, .fa-trash')) {
                        return;
                    }
                    const movieId = this.dataset.movieId;
                    if (movieId) {
                        window.location.href = `movie_review.php?id=${movieId}`;
                    }
                });
            });
        });
    </script>
</body>

</html>