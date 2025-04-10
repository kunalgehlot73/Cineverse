<?php
require_once 'includes/config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: profile.php");
    exit();
}

if ((!isset($_GET['id']) || !is_numeric($_GET['id'])) && ($_SESSION['is_admin'] == 1)) {
    header("Location: admin.php");
    exit();
}

$movie_id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$result = $stmt->get_result();
$movie = $result->fetch_assoc();

if (!$movie) {
    header("Location: admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($movie['title']); ?> - Cineverse</title>
    <link rel="stylesheet" href="css/headerfooter.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<style>
    .movie-review-container {
        background: var(--bg);
        min-height: calc(100vh - 80px);
    }

    .movie-hero {
        height: 60vh;
        position: relative;
        display: flex;
        align-items: center;
        padding: 0 2rem;
    }

    .movie-hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to right, rgba(0, 0, 0, 0.9), rgba(0, 0, 0, 0.4));
    }

    .movie-hero-content {
        position: relative;
        z-index: 2;
        max-width: 1200px;
        margin: 0 auto;
        width: 100%;
    }

    .movie-title-section h1 {
        font-size: 4rem;
        margin-bottom: 1rem;
        color: white;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }

    .movie-meta {
        display: flex;
        gap: 2rem;
        margin-bottom: 1rem;
        color: #888;
        font-size: 1.2rem;
    }

    .movie-genres {
        display: flex;
        gap: 1rem;
    }

    .movie-genres span {
        background: var(--gold);
        color: var(--bg);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: bold;
    }

    .movie-details {
        padding: 2rem 1rem;
        background: var(--bg);
    }

    .movie-info-grid {
        display: flex;
        align-items: center;
        gap: 3rem;
        margin: 0 auto;
    }

    .movie-trailer iframe {
        border-radius: 15px;
    }

    .movie-stats {
        display: grid;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        color: #888;
    }

    .stat-item i {
        color: var(--gold);
        font-size: 1.2rem;
    }

    .review-section {
        padding: 4rem 2rem;
        background: var(--mov_ban_bg);
    }

    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .review-rating {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .rating-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: var(--gold);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: var(--bg);
    }

    .rating-number {
        font-size: 2rem;
        font-weight: bold;
    }

    .rating-label {
        font-size: 1rem;
    }

    .rating-stars {
        color: var(--gold);
    }

    .storyline {
        max-width: 800px;
        margin: 0 auto;
    }

    .storyline p {
        color: #888;
        line-height: 1.6;
        margin-bottom: 1rem;
    }

    .user-comment-section {
        background-color: var(--mov_list_item_bg);
        padding: 2rem;
        border-radius: 10px;
        margin: 2rem 0;
    }

    .user-comment-section h2 {
        color: var(--gold);
        margin-bottom: 1.5rem;
        font-size: 1.8rem;
    }

    .comment-form {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .review-form-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .review-form-group label {
        color: #fff;
        font-size: 1rem;
        font-weight: 500;
    }

    .review-form-group input,
    .review-form-group textarea {
        background-color: var(--mov_ban_bg);
        border: 1px solid #444;
        padding: 0.8rem;
        border-radius: 5px;
        font-size: 1rem;
        color: #fff;
        transition: all 0.3s ease;
    }

    .review-form-group input:focus,
    .review-form-group textarea:focus {
        outline: none;
        border-color: var(--gold);
        box-shadow: 0 0 0 2px rgba(244, 193, 15, 0.2);
    }

    .review-form-group textarea {
        min-height: 120px;
        resize: vertical;
    }

    .submit-comment {
        background-color: var(--gold);
        color: var(--header);
        border: none;
        padding: 1rem 2rem;
        border-radius: 5px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        align-self: flex-start;
    }

    .submit-comment:hover {
        background-color: #e6b50f;
        transform: translateY(-2px);
    }

    .user-reviews {
        padding: 4rem 2rem;
        background: var(--bg);

    }

    .user-reviews h1 {
        color: var(--gold);
        margin-bottom: 2rem;
        text-align: center;
    }

    .user-review-list {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
    }

    .no-reviews {
        font-size: large;
    }

    .user-review {
        background: var(--mov_list_item_bg);
        padding: 2rem;
        border-radius: 15px;
        margin: 2rem;
        transition: all 0.3s ease-in-out;
    }

    .user-review:hover {
        box-shadow: 0 0 20px 5px rgba(244, 193, 15, 0.1);
        transform: translateY(-5px);
    }

    .user-review-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #888;
    }

    .user-info i {
        font-size: 1.5rem;
        color: var(--gold);
    }

    .review-date {
        color: #888;
        font-size: 0.9rem;
    }

    .user-rating {
        margin-bottom: 1rem;
    }

    .user-rating i {
        color: var(--gold);
    }

    .user-review-text {
        color: #888;
        line-height: 1.6;
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
</style>

<body>

    <?php include 'includes/header.php' ?>

    <main>

        <div class="movie-review-container">
            <div class="movie-hero" style="background: url('<?php echo htmlspecialchars($movie['poster_url']); ?>') center/cover no-repeat;">
                <div class="movie-hero-overlay"></div>
                <div class="movie-hero-content">
                    <div class="movie-title-section">
                        <h1><?php echo htmlspecialchars($movie['title']); ?></h1>
                        <div class="movie-meta">
                            <span class="year"><?php echo htmlspecialchars($movie['release_year']); ?></span>
                            <span class="duration"><?php echo format_duration($movie['duration']); ?></span>
                            <span class="rating"><?php echo htmlspecialchars(format_rating($movie['rating'])); ?>/10 ⭐</span>
                        </div>
                        <div class="movie-genres">
                            <?php
                            $genres = explode(',', $movie['genre']);
                            foreach ($genres as $genre) {
                                echo '<span>' . trim(htmlspecialchars($genre)) . '</span>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="movie-details">
                <div class="movie-info-grid">
                    <div class="movie-trailer">
                        <iframe width="640" height="360" src="https://www.youtube.com/embed/<?php echo htmlspecialchars($movie['youtube_id']); ?>?autoplay=1&mute=1" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                    </div>
                    <div class="movie-info">
                        <div class="movie-stats">
                            <div class="stat-item">
                                <i class="fas fa-calendar"></i>
                                <span>Release Year: <?php echo htmlspecialchars($movie['release_year']); ?></span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-film"></i>
                                <span>Director: <?php echo htmlspecialchars($movie['director']); ?></span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-users"></i>
                                <span>Cast: <?php echo htmlspecialchars($movie['cast']); ?></span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-globe"></i>
                                <span>Language: English</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="review-section">
                <div class="review-header">
                    <h2>Storyline</h2>
                    <div class="review-rating">
                        <div class="rating-circle">
                            <span class="rating-number"><?php echo htmlspecialchars(format_rating($movie['rating'])); ?></span>
                            <span class="rating-label">/10</span>
                        </div>
                        <div class="rating-stars">
                            <?php for ($i = 0; $i < floor($movie['rating']); $i++) {
                                echo '<i class="fas fa-star"></i> ';
                            } ?>
                        </div>
                    </div>
                </div>
                <div class="storyline">
                    <p><?php echo htmlspecialchars($movie['description']); ?></p>
                </div>
            </div>
            <div class="user-comment-section">
                <h2>Leave a Comment</h2>
                <?php if (!empty($_SESSION['comment_success'])): ?>
                    <div class='alert-success'>
                        <span class='success-text'>
                            <?= $_SESSION['comment_success'] ?>
                            <?php unset($_SESSION['comment_success']); ?>
                        </span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($_SESSION['comment_errors'])): ?>
                    <div class="alert">
                        <?php foreach ($_SESSION['comment_errors'] as $error): ?>
                            <span class='error-text'><?= $error ?></span>
                        <?php endforeach; ?>
                        <?php unset($_SESSION['comment_errors']); ?>
                    </div>
                <?php endif; ?>
                <form class="comment-form" method="post" action="review_process.php">
                    <div class="review-form-group">
                        Username: <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </div>
                    <div class="review-form-group">
                        Email: <?php echo htmlspecialchars($_SESSION['email']); ?>
                    </div>
                    <div class="review-form-group">
                        <label for="rating">Rating</label>
                        <input type="number" name="rating" max="5" min="0" step="1" id="rating">
                    </div>
                    <div class="review-form-group">
                        <label for="comment">Your Comment</label>
                        <textarea id="comment" name="comment" required
                            placeholder="Share your thoughts about the movie..."></textarea>
                        <input type="number" name="movie-id" value="<?php echo $movie_id ?>" hidden>
                    </div>
                    <button type="submit" class="submit-comment" name="post-comment">Post Comment</button>
                </form>
            </div>
            <div class="user-reviews">
                <h1>User Reviews</h1>
                <div class="user-review-list">
                    <?php
                    $review_stmt = $conn->prepare("
            SELECT reviews.*, users.username 
            FROM reviews 
            JOIN users ON reviews.user_id = users.id 
            WHERE movie_id = ?
            ORDER BY created_at DESC
        ");
                    $review_stmt->bind_param("i", $movie_id);
                    $review_stmt->execute();
                    $reviews = $review_stmt->get_result();

                    if ($reviews->num_rows === 0) {
                        echo '<p class="no-reviews">No reviews yet. Be the first to share your thoughts!</p>';
                    } else {
                        while ($review = $reviews->fetch_assoc()) {

                    ?>
                            <div class="user-review">
                                <div class="user-review-header">
                                    <div class="user-info">
                                        <i class="fas fa-user-circle"></i>
                                        <span><?php echo htmlspecialchars($review['username']); ?></span>
                                    </div>
                                    <div class="review-date">
                                        <?php echo date('F j, Y', strtotime($review['created_at'])); ?>
                                    </div>
                                </div>
                                <div class="user-rating">
                                    <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                                        <i class="fas fa-star"></i>
                                    <?php endfor; ?>
                                </div>
                                <p class="user-review-text"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                            </div>
                    <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
            <div class="admin-movie-form-container" id="movieForm">
                <h2>Edit Movie</h2>
                <?php if (!empty($_SESSION['edit_success'])): ?>
                    <div class='alert-success'>
                        <span class='success-text'>
                            <?= $_SESSION['edit_success'] ?>
                            <?php unset($_SESSION['edit_success']); ?>
                        </span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($_SESSION['edit_errors'])): ?>
                    <div class="alert">
                        <?php foreach ($_SESSION['edit_errors'] as $error): ?>
                            <span class='error-text'><?= $error ?></span>
                        <?php endforeach; ?>
                        <?php unset($_SESSION['edit_errors']); ?>
                    </div>
                <?php endif; ?>
                <form class="admin-movie-form" method="post" action="admin_process.php">
                    <div class="admin-form-group">
                        <label for="movieTitle">Movie Title</label>
                        <input type="text" id="movieTitle" name="admin-movieTitle" value="<?php echo htmlspecialchars(isset($movie['title']) ? $movie['title'] : '') ?>" required>
                    </div>
                    <div class="admin-form-row">
                        <div class="admin-form-group">
                            <label for="releaseYear">Release Year</label>
                            <input type="number" id="releaseYear" name="admin-releaseYear" value="<?php echo htmlspecialchars(isset($movie['release_year']) ? $movie['release_year'] : '') ?>" required>
                        </div>
                        <div class="admin-form-group">
                            <label for="duration">Duration (minutes)</label>
                            <input type="number" id="duration" name="admin-duration" value="<?php echo htmlspecialchars(isset($movie['duration']) ? $movie['duration'] : '') ?>" required>
                        </div>
                    </div>
                    <div class="admin-form-group">
                        <label for="movieDirector">Director</label>
                        <input type="text" id="movieDirector" name="admin-movieDirector" value="<?php echo htmlspecialchars(isset($movie['director']) ? $movie['director'] : '') ?>" required>
                    </div>
                    <div class="admin-form-group">
                        <label for="movieCast">Cast</label>
                        <input type="text" id="movieCast" name="admin-movieCast" value="<?php echo htmlspecialchars(isset($movie['cast']) ? $movie['cast'] : '') ?>" required>
                    </div>
                    <div class="admin-form-group">
                        <label for="genres">Genres</label>
                        <input type="text" id="genres" name="admin-genres" placeholder="Action, Drama, Sci-Fi" value="<?php echo htmlspecialchars(isset($movie['genre']) ? $movie['genre'] : '') ?>" required>
                    </div>
                    <div class="admin-form-group">
                        <label for="rating">Rating</label>
                        <input type="number" id="rating" name="admin-rating" min="0" max="10" step="0.1" name="admin-rating" value="<?php echo htmlspecialchars(isset($movie['rating']) ? $movie['rating'] : '') ?>" required>
                    </div>
                    <div class="admin-form-group">
                        <label for="synopsis">Synopsis</label>
                        <textarea id="synopsis" rows="4" required name="admin-synopsis"><?php echo htmlspecialchars(isset($movie['description']) ? $movie['description'] : '') ?></textarea>
                    </div>
                    <div class="admin-form-group">
                        <label for="posterUrl">Poster URL</label>
                        <input type="text" id="posterUrl" name="admin-poster-url" value="<?php echo htmlspecialchars(isset($movie['poster_url']) ? $movie['poster_url'] : '') ?>" required>
                    </div>
                    <div class="admin-form-group">
                        <label for="portraitposterUrl">Portrait Poster URL</label>
                        <input type="text" id="portraitposterUrl" name="admin-portrait-poster-url" value="<?php echo htmlspecialchars(isset($movie['poster_portrait_url']) ? $movie['poster_portrait_url'] : '') ?>" required>
                    </div>
                    <div class="admin-form-group">
                        <label for="youtube-id">YouTube Video ID</label>
                        <input type="text" id="youtube-id" name="admin-youtube-id" value="<?php echo htmlspecialchars(isset($movie['youtube_id']) ? $movie['youtube_id'] : '') ?>" required placeholder="e.g.: HYVxnPmb15E">
                        <small>Found in YouTube URL after ?v=</small>
                    </div>
                    <div class="admin-form-buttons">
                        <button type="submit" name="admin-edit-movie-btn" class="admin-save-btn">Save Movie</button>
                        <button type="reset" class="admin-cancel-btn">Cancel</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </main>
    <?php include 'includes/footer.php' ?>
</body>

</html>