<?php
require_once 'includes/config.php';

$featured_stmt = $conn->prepare("SELECT * FROM movies WHERE release_year <= YEAR(CURDATE()) ORDER BY RAND() LIMIT 5");
$featured_stmt->execute();
$featured_movies = $featured_stmt->get_result();

$motw_stmt = $conn->prepare("SELECT * FROM movies WHERE release_year <= YEAR(CURDATE()) ORDER BY release_year DESC LIMIT 1");
$motw_stmt->execute();
$movie_of_week = $motw_stmt->get_result()->fetch_assoc();

$latest_stmt = $conn->prepare("SELECT * FROM movies WHERE release_year <= YEAR(CURDATE()) ORDER BY release_year DESC LIMIT 5");
$latest_stmt->execute();
$latest_movies = $latest_stmt->get_result();

$coming_soon_stmt = $conn->prepare("SELECT * FROM movies WHERE release_year > YEAR(CURDATE()) ORDER BY release_year ASC LIMIT 3");
$coming_soon_stmt->execute();
$coming_soon_movies = $coming_soon_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineVerse</title>
    <link rel="stylesheet" href="css/headerfooter.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<style>
.hero-section {
    height: 50vh;
    margin: 2rem;
    overflow: hidden;
    display: flex;
    align-items: center;
}
.slider {
    height: 100%;
    width: 50vw;
    position: relative;
    display: flex;
    align-items: center;
    transition: all 1s cubic-bezier(0.4, 0, 0.2, 1);
    transform: translate(50%);
}
.slide {
    height: 100%;
    min-width: 100%;
    background-repeat: no-repeat;
    background-size: cover;
    transform: scale(0.9);
    opacity: 0.7;
    border-radius: 10px;
    overflow: hidden;
}
.active {
    opacity: 1;
    transform: scale(1);
}
.slide-content {
    position: absolute;
    bottom: 30px;
    left: 30px;
    background-color: rgba(0, 0, 0, 0.4);
    padding: 10px;
    border-radius: 5%;
    min-width: 180px;
}
.slider-movie-title {
    font-weight: 500;
    font-size: 2.5rem;
}
.slider-movie-rating,
.slider-movie-category {
    margin: 0.5rem 0;
}
.slider-review {
    text-decoration: none;
    background: var(--gold);
    color: var(--bg);
    font-weight: bold;
    padding: 5px 12px;
    font-size: 1rem;
    border: none;
    transition: all 0.2s ease-in-out;
    border-radius: 30px;
    overflow: hidden;
    width: 122px;
    height: 29px;
    text-wrap: nowrap;
}
.slider-review:hover {
    width: 159px;
}
.slide-button {
    position: absolute;
    z-index: 10;
    height: 50px;
    width: 50px;
    border-radius: 50%;
    border: none;
    background: rgba(0, 0, 0, 0.5);
    font-size: 30px;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
}
#prev-slide {
    left: 2%;
}
#prev-slide:hover {
    transform: translateX(-5px);
}
#next-slide {
    right: 2%;
}
#next-slide:hover {
    transform: translateX(5px);
}
.movie-of-week {
    background: var(--mov_ban_bg);
    padding: 4rem 2rem;
    margin: 0;
}
.movie-of-week-content {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    align-items: center;
}
.movie-of-week-info h2 {
    color: var(--gold);
    font-size: 2.5rem;
    margin-bottom: 1rem;
}
.movie-of-week-info h3 {
    font-size: 2rem;
    margin-bottom: 1rem;
}
.movie-stats {
    display: flex;
    gap: 2rem;
    margin-bottom: 1.5rem;
    color: #888;
}
.movie-of-week-info p {
    color: #888;
    line-height: 1.6;
    margin-bottom: 2rem;
}
.read-review-btn {
    background: var(--gold);
    color: var(--bg);
    border: none;
    padding: 1rem 2rem;
    border-radius: 30px;
    font-size: 1.1rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
}
.read-review-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(244, 193, 15, 0.3);
}
.movie-of-week-image {
    height: 360px;
    background-size: cover;
    background-position: center;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}
.latest-section {
    width: 100%;
    padding: 4rem 2rem;
    background: var(--bg);
}
.latest-section > h2 {
    text-align: center;
    font-size: 2.5rem;
    margin-bottom: 3rem;
    color: var(--gold);
}
.latest-movies {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}
.latest-movie {
    min-height: 300px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    border-radius: 15px;
    overflow: hidden;
    background-size: cover;
    background-position: center;
}
.latest-movie::after {
    content: "";
    position: absolute;
    width: 100%;
    height: 100%;
    background: linear-gradient(0deg, var(--header) 0%, transparent 60%);
    transition: opacity 0.3s ease;
}
.latest-movie:hover::after {
    opacity: 0.8;
}
.latest-movie .latest-movie-inner {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 1.5rem;
    z-index: 2;
    transform: translateY(20px);
    transition: transform 0.3s ease;
}
.latest-movie:hover .latest-movie-inner {
    transform: translateY(0);
}
.latest-movie-inner h3 {
    font-size: 1.2rem;
    margin-bottom: 0.5rem;
}
.latest-movie-inner span {
    color: var(--gold);
    font-weight: bold;
}
.latest-movie:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}
.coming-soon {
    padding: 4rem 2rem;
    background: var(--bg);
}
.coming-soon h2 {
    text-align: center;
    color: var(--gold);
    font-size: 2.5rem;
    margin-bottom: 3rem;
}
.coming-soon-grid {
    display: flex;
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
}
.coming-soon-card {
    background: var(--mov_list_item_bg);
    max-width: fit-content;
    border-radius: 15px;
    overflow: hidden;
    position: relative;
    transition: all 0.3s ease;
}
.coming-soon-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}
.coming-soon-release-date {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: var(--gold);
    color: var(--bg);
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: bold;
    z-index: 2;
}
.coming-soon-card .movie-image {
    height: 400px;
    background-size: cover;
    background-position: center;
}
.coming-soon-card .movie-info {
    padding: 1.5rem;
}
.coming-soon-card .movie-info h3 {
    margin-bottom: 0.5rem;
}
.coming-soon-card .genre {
    color: #888;
}
@media screen and (max-width:599px) {
    .hero-section {
        height: 25vh;
    }
}
</style>

<body>

    <?php include 'includes/header.php' ?>

    <main>

        <section class="hero-section">

            <div class="slider">
                <?php while ($movie = $featured_movies->fetch_assoc()): ?>
                    <div class="slide" style="background-image: linear-gradient(rgba(0,0,0,0.1), rgba(0,0,0,0.5)), url('<?php echo htmlspecialchars($movie['poster_url']); ?>')">
                        <div class="slide-content">
                            <div class="slider-movie-title"><?php echo htmlspecialchars($movie['title']); ?></div>
                            <div class="slider-movie-rating"><?php echo htmlspecialchars(format_rating($movie['rating'])); ?>/10 ⭐</div>
                            <div class="slider-movie-category"><?php echo htmlspecialchars($movie['genre']); ?></div>
                            <button class="slider-review" type="button" onclick="window.location.href='movie_review.php?id=<?php echo $movie['id']; ?>'">Read Review &raquo;&raquo;</button>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <button type="button" class="slide-button" id="prev-slide">&lt;</button>
            <button type="button" class="slide-button" id="next-slide">&gt;</button>

        </section>

        <section class="movie-of-week">
            <div class="movie-of-week-content">
                <div class="movie-of-week-info">
                    <h2>Movie of the Week</h2>
                    <h3><?php echo htmlspecialchars($movie_of_week['title']); ?></h3>
                    <div class="movie-stats">
                        <span><?php echo htmlspecialchars(format_rating($movie_of_week['rating'])); ?>/10 ⭐</span>
                        <span><?php echo htmlspecialchars($movie_of_week['release_year']); ?></span>
                        <span><?php echo format_duration($movie_of_week['duration']); ?></span>
                    </div>
                    <p><?php echo htmlspecialchars(substr($movie_of_week['description'], 0, 100)); ?>...</p>
                    <button class="read-review-btn" type="button" onclick="window.location.href='movie_review.php?id=<?php echo $movie_of_week['id']; ?>'">Read More</button>
                </div>
                <div class="movie-of-week-image" style="background-image: url('<?php echo htmlspecialchars($movie_of_week['poster_url']); ?>')"></div>
            </div>
        </section>

        <section class="latest-section">

            <h2>Latest Reviews</h2>
            <div class="latest-movies">
                <?php while ($movie = $latest_movies->fetch_assoc()): ?>
                    <div class="latest-movie" onclick="window.location.href='movie_review.php?id=<?php echo $movie['id']; ?>'" style="background-image: url('<?php echo htmlspecialchars($movie['poster_portrait_url']); ?>')">
                        <div class="latest-movie-inner">
                            <h3><?php echo htmlspecialchars($movie['title']); ?></h3>
                            <span><?php echo htmlspecialchars(format_rating($movie['rating'])); ?>/10 ⭐</span>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>

        <section class="coming-soon">
            <?php if ($coming_soon_movies->num_rows > 0): ?>
                <h2>Coming Soon</h2>
                <div class="coming-soon-grid">
                    <?php while ($movie = $coming_soon_movies->fetch_assoc()): ?>
                        <div class="coming-soon-card" onclick="window.location.href='movie_review.php?id=<?php echo $movie['id']; ?>'">
                            <div class="coming-soon-release-date"><?php echo htmlspecialchars($movie['release_year']); ?></div>
                            <div class="movie-image" style="background-image: url('<?php echo htmlspecialchars($movie['poster_portrait_url']); ?>')"></div>
                            <div class="movie-info">
                                <h3><?php echo htmlspecialchars($movie['title']); ?></h3>
                                <div class="genre"><?php echo htmlspecialchars($movie['genre']); ?></div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
        </section>

    </main>
    <?php include 'includes/footer.php' ?>
    <script src="script.js"></script>
</body>

</html>