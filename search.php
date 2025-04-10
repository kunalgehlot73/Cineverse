<?php
require_once 'includes/config.php';

// Initialize variables
$search_query = isset($_GET['movie-search']) ? trim($_GET['movie-search']) : '';
$results = [];

// Always search (shows all movies when query is empty)
$stmt = $conn->prepare("SELECT * FROM movies WHERE title LIKE ? ORDER BY release_year DESC");
$search_param = "%$search_query%";
$stmt->bind_param("s", $search_param);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $results[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Movies - CineVerse</title>
    <link rel="stylesheet" href="css/headerfooter.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<style>
    .search-section {
        padding: 3rem 2rem;
        background-color: var(--header);
        border-radius: 8px;
        margin: 2rem;
    }

    .search-container-large {
        max-width: 1200px;
        margin: 0 auto;
    }

    .search-container-large h1 {
        text-align: center;
        margin-bottom: 2rem;
        font-size: 2.5rem;
        color: var(--gold);
    }

    .search-box {
        display: flex;
        max-width: 800px;
        margin: 0 auto 2rem;
    }

    #movie-search {
        flex: 1;
        height: 50px;
        background: rgba(255, 255, 255, 0.1);
        border: 2px solid #444;
        padding: 0 1.5rem;
        border-radius: 25px 0 0 25px;
        color: white;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    #movie-search:focus {
        outline: none;
        border-color: var(--gold);
        background: rgba(255, 255, 255, 0.15);
    }

    .search-button-large {
        height: 50px;
        background: var(--gold);
        color: var(--header);
        border: none;
        padding: 0 2rem;
        border-radius: 0 25px 25px 0;
        font-weight: bold;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .search-button-large:hover {
        background: #e0b00e;
    }

    .search-results {
        padding: 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }

    .search-results h2 {
        font-size: 2rem;
        margin-bottom: 1.5rem;
        color: var(--gold);
    }

    .results-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .movie-card {
        display: flex;
        background: var(--mov_list_item_bg);
        border-radius: 8px;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;

    }

    .movie-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
    }

    .movie-poster {
        width: 200px;
        height: 250px;
        background-size: cover;
        background-position: center;
        flex-shrink: 0;
    }

    .movie-info {
        padding: 1.5rem;
        flex: 1;
        max-height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-around;
    }

    .movie-info h3 {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .movie-meta {
        display: flex;
        gap: 1.5rem;
        margin-bottom: 0.8rem;
    }

    .movie-genres {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .movie-genres span {
        color: black;
        background: var(--gold);
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 800;
    }

    .view-details {
        align-self: flex-start;
        display: inline-block;
        background: transparent;
        border: 2px solid var(--gold);
        padding: 0.5rem 1.5rem;
        border-radius: 20px;
        color: var(--gold);
        font-weight: bold;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .view-details:hover {
        background: var(--gold);
        color: var(--header);
    }

    .no-results p {
        font-size: large;
        color: var(--gold);
    }
</style>

<body>
    <?php include 'includes/header.php' ?>

    <main>
        <section class="search-section">
            <div class="search-container-large">
                <h1>Find Your Perfect Movie</h1>
                <form method="GET" class="search-box">
                    <input type="text"
                        id="movie-search"
                        name="movie-search"
                        placeholder="Enter movie title..."
                        value="<?php echo htmlspecialchars($search_query); ?>">
                    <button type="submit" class="search-button-large">
                        <i class="fas fa-search"></i> Search
                    </button>
                </form>
            </div>
        </section>

        <section class="search-results">
            <?php if (!empty($search_query)): ?>
                <h2>Search Results for "<?php echo htmlspecialchars($search_query); ?>"</h2>
            <?php else: ?>
                <h2>All Movies</h2>
            <?php endif; ?>

            <div class="results-grid">
                <?php if (!empty($results)): ?>
                    <?php foreach ($results as $movie): ?>
                        <div class="movie-card">
                            <div class="movie-poster"
                                style="background-image: url('<?php echo htmlspecialchars($movie['poster_portrait_url']); ?>')">
                            </div>
                            <div class="movie-info">
                                <h3><?php echo htmlspecialchars($movie['title']); ?></h3>
                                <div class="movie-meta">
                                    <span class="year"><?php echo htmlspecialchars($movie['release_year']); ?></span>
                                    <span class="rating">
                                        <?php echo htmlspecialchars(format_rating($movie['rating'])); ?>/10 ⭐
                                    </span>
                                </div>
                                <div class="movie-genres">
                                    <?php
                                    $genres = explode(',', $movie['genre']);
                                    foreach ($genres as $genre) {
                                        echo '<span>' . trim(htmlspecialchars($genre)) . '</span>';
                                    }
                                    ?>
                                </div>
                                <a href="movie_review.php?id=<?php echo $movie['id']; ?>" class="view-details">
                                    View Details
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-results">
                        <p>No movies found<?php echo !empty($search_query) ? ' matching your search' : ''; ?>.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php' ?>
</body>

</html>