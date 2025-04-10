<?php
require_once 'includes/config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}

// Fetch all categories
$categories_query = "SELECT * FROM categories";
$categories_result = $conn->query($categories_query);
$categories = [];
while ($row = $categories_result->fetch_assoc()) {
    $categories[] = $row;
}

// Fetch all movies with their categories
$movies_query = "SELECT m.*, GROUP_CONCAT(c.name) as categories 
                FROM movies m 
                LEFT JOIN movie_categories mc ON m.id = mc.movie_id 
                LEFT JOIN categories c ON mc.category_id = c.id 
                GROUP BY m.id";
$movies_result = $conn->query($movies_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CineVerse</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <main class="admin-container">
        <div class="admin-sidebar">
            <div class="admin-profile">
                <div class="admin-avatar">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h3>Admin Panel</h3>
            </div>
            <nav class="admin-nav">
                <ul>
                    <li class="active"><a href="#movies"><i class="fas fa-film"></i> Movies</a></li>
                    <li><a href="#categories"><i class="fas fa-tags"></i> Categories</a></li>
                </ul>
            </nav>
        </div>

        <div class="admin-content">
            <!-- Movies Section -->
            <div id="movies" class="admin-section">
                <div class="admin-content-header">
                    <h1>Movie Management</h1>
                    <button class="admin-add-movie-btn" onclick="showAddMovieForm()">
                        <i class="fas fa-plus"></i> Add New Movie
                    </button>
                </div>

                <div class="admin-movie-form-container" id="movieForm" style="display: none;">
                    <h2 id="movieFormTitle">Add New Movie</h2>
                    <form class="admin-movie-form" id="movieAddEditForm" onsubmit="handleMovieSubmit(event)">
                        <input type="hidden" id="movieId" name="movie_id">
                        <input type="hidden" name="action" id="movieAction" value="add_movie">

                        <div class="admin-form-group">
                            <label for="movieTitle">Movie Title</label>
                            <input type="text" id="movieTitle" name="title" required>
                        </div>
                        <div class="admin-form-row">
                            <div class="admin-form-group">
                                <label for="releaseYear">Release Year</label>
                                <input type="number" id="releaseYear" name="release_year" required>
                            </div>
                            <div class="admin-form-group">
                                <label for="duration">Duration (minutes)</label>
                                <input type="number" id="duration" name="duration" required>
                            </div>
                        </div>
                        <div class="admin-form-group">
                            <label>Categories</label>
                            <div class="admin-categories-checkbox">
                                <?php foreach ($categories as $category): ?>
                                    <label>
                                        <input type="checkbox" name="categories[]" value="<?php echo $category['id']; ?>">
                                        <?php echo $category['name']; ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="admin-form-group">
                            <label for="rating">Rating</label>
                            <input type="number" id="rating" name="rating" min="0" max="10" step="0.1" required>
                        </div>
                        <div class="admin-form-group">
                            <label for="synopsis">Synopsis</label>
                            <textarea id="synopsis" name="synopsis" rows="4" required></textarea>
                        </div>
                        <div class="admin-form-group">
                            <label for="posterUrl">Poster URL</label>
                            <input type="text" id="posterUrl" name="poster_url" required>
                        </div>
                        <div class="admin-form-group">
                            <label for="potraitposterUrl">Portrait Poster URL</label>
                            <input type="text" id="potraitposterUrl" name="poster_potrait_url" required>
                        </div>
                        <div class="admin-form-group">
                            <label for="youtubeId">YouTube Video ID</label>
                            <input type="text" id="youtubeId" name="youtube_id" required
                                placeholder="e.g.: HYVxnPmb15E">
                            <small>Found in YouTube URL after ?v=</small>
                        </div>
                        <div class="admin-form-group">
                            <label>
                                <input type="checkbox" name="featured" id="featured"> Featured Movie
                            </label>
                        </div>
                        <div class="admin-form-buttons">
                            <button type="submit" class="admin-save-btn">Save Movie</button>
                            <button type="button" class="admin-cancel-btn" onclick="hideAddMovieForm()">Cancel</button>
                        </div>
                    </form>
                </div>

                <div class="admin-movies-table-container">
                    <h2>Existing Movies</h2>
                    <div class="admin-table-actions">
                        <div class="admin-search-box">
                            <input type="text" id="movieSearch" placeholder="Search movies..." onkeyup="filterMovies()">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                    <table class="admin-movies-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Year</th>
                                <th>Rating</th>
                                <th>Categories</th>
                                <th>Featured</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($movie = $movies_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($movie['title']); ?></td>
                                    <td><?php echo $movie['release_year']; ?></td>
                                    <td><?php echo $movie['rating']; ?></td>
                                    <td><?php echo htmlspecialchars($movie['categories'] ?? ''); ?></td>
                                    <td><?php echo $movie['featured'] ? 'Yes' : 'No'; ?></td>
                                    <td class="admin-action-buttons">
                                        <button class="admin-edit-btn" onclick="editMovie(<?php echo $movie['id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="admin-delete-btn" onclick="deleteMovie(<?php echo $movie['id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Categories Section -->
            <div id="categories" class="admin-section" style="display: none;">
                <div class="admin-content-header">
                    <h1>Category Management</h1>
                    <button class="admin-add-category-btn" onclick="showAddCategoryForm()">
                        <i class="fas fa-plus"></i> Add New Category
                    </button>
                </div>

                <div class="admin-category-form-container" id="categoryForm" style="display: none;">
                    <h2 id="categoryFormTitle">Add New Category</h2>
                    <form class="admin-category-form" id="categoryAddEditForm" onsubmit="handleCategorySubmit(event)">
                        <input type="hidden" id="categoryId" name="category_id">
                        <input type="hidden" name="action" id="categoryAction" value="add_category">

                        <div class="admin-form-group">
                            <label for="categoryName">Category Name</label>
                            <input type="text" id="categoryName" name="name" required>
                        </div>
                        <div class="admin-form-group">
                            <label for="faIcon">Font Awesome Icon</label>
                            <input type="text" id="faIcon" name="fa_icon" required placeholder="e.g.: fa-film">
                        </div>
                        <div class="admin-form-buttons">
                            <button type="submit" class="admin-save-btn">Save Category</button>
                            <button type="button" class="admin-cancel-btn" onclick="hideAddCategoryForm()">Cancel</button>
                        </div>
                    </form>
                </div>

                <div class="admin-categories-table-container">
                    <h2>Existing Categories</h2>
                    <table class="admin-categories-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Icon</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($category['name']); ?></td>
                                    <td><i class="fas <?php echo htmlspecialchars($category['fa_icon']); ?>"></i></td>
                                    <td class="admin-action-buttons">
                                        <button class="admin-edit-btn" onclick="editCategory(<?php echo $category['id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="admin-delete-btn" onclick="deleteCategory(<?php echo $category['id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Navigation
        document.querySelectorAll('.admin-nav a').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const targetId = e.currentTarget.getAttribute('href').substring(1);
                document.querySelectorAll('.admin-section').forEach(section => {
                    section.style.display = 'none';
                });
                document.getElementById(targetId).style.display = 'block';
                document.querySelectorAll('.admin-nav li').forEach(li => li.classList.remove('active'));
                e.currentTarget.parentElement.classList.add('active');
            });
        });

        // Movie Management
        function showAddMovieForm() {
            document.getElementById('movieFormTitle').textContent = 'Add New Movie';
            document.getElementById('movieAction').value = 'add_movie';
            document.getElementById('movieId').value = '';
            document.getElementById('movieAddEditForm').reset();
            document.getElementById('movieForm').style.display = 'block';
        }

        function hideAddMovieForm() {
            document.getElementById('movieForm').style.display = 'none';
            document.getElementById('movieAddEditForm').reset();
        }

        function editMovie(movieId) {
            fetch(`admin_process.php?action=get_movie&id=${movieId}`)
                .then(response => response.json())
                .then(movie => {
                    document.getElementById('movieFormTitle').textContent = 'Edit Movie';
                    document.getElementById('movieAction').value = 'edit_movie';
                    document.getElementById('movieId').value = movie.id;
                    document.getElementById('movieTitle').value = movie.title;
                    document.getElementById('releaseYear').value = movie.release_year;
                    document.getElementById('duration').value = movie.duration;
                    document.getElementById('rating').value = movie.rating;
                    document.getElementById('synopsis').value = movie.description;
                    document.getElementById('posterUrl').value = movie.poster_url;
                    document.getElementById('potraitposterUrl').value = movie.poster_potrait_url;
                    document.getElementById('youtubeId').value = movie.youtube_id;
                    document.getElementById('featured').checked = movie.featured == 1;

                    // Handle categories
                    const categoryIds = movie.category_ids ? movie.category_ids.split(',') : [];
                    document.querySelectorAll('input[name="categories[]"]').forEach(checkbox => {
                        checkbox.checked = categoryIds.includes(checkbox.value);
                    });

                    document.getElementById('movieForm').style.display = 'block';
                });
        }

        function deleteMovie(movieId) {
            if (confirm('Are you sure you want to delete this movie?')) {
                const formData = new FormData();
                formData.append('action', 'delete_movie');
                formData.append('movie_id', movieId);

                fetch('admin_process.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.status === 'success') {
                            location.reload();
                        } else {
                            alert('Error deleting movie: ' + result.message);
                        }
                    });
            }
        }

        function handleMovieSubmit(event) {
            event.preventDefault();
            const formData = new FormData(event.target);

            fetch('admin_process.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success') {
                        location.reload();
                    } else {
                        alert('Error: ' + result.message);
                    }
                });
        }

        // Category Management
        function showAddCategoryForm() {
            document.getElementById('categoryFormTitle').textContent = 'Add New Category';
            document.getElementById('categoryAction').value = 'add_category';
            document.getElementById('categoryId').value = '';
            document.getElementById('categoryAddEditForm').reset();
            document.getElementById('categoryForm').style.display = 'block';
        }

        function hideAddCategoryForm() {
            document.getElementById('categoryForm').style.display = 'none';
            document.getElementById('categoryAddEditForm').reset();
        }

        function editCategory(categoryId) {
            const row = event.target.closest('tr');
            const name = row.cells[0].textContent;
            const faIcon = row.cells[1].querySelector('i').className.replace('fas ', '');

            document.getElementById('categoryFormTitle').textContent = 'Edit Category';
            document.getElementById('categoryAction').value = 'edit_category';
            document.getElementById('categoryId').value = categoryId;
            document.getElementById('categoryName').value = name;
            document.getElementById('faIcon').value = faIcon;
            document.getElementById('categoryForm').style.display = 'block';
        }

        function deleteCategory(categoryId) {
            if (confirm('Are you sure you want to delete this category? This will remove the category from all movies.')) {
                const formData = new FormData();
                formData.append('action', 'delete_category');
                formData.append('category_id', categoryId);

                fetch('admin_process.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.status === 'success') {
                            location.reload();
                        } else {
                            alert('Error deleting category: ' + result.message);
                        }
                    });
            }
        }

        function handleCategorySubmit(event) {
            event.preventDefault();
            const formData = new FormData(event.target);

            fetch('admin_process.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success') {
                        location.reload();
                    } else {
                        alert('Error: ' + result.message);
                    }
                });
        }

        // Search functionality
        function filterMovies() {
            const searchText = document.getElementById('movieSearch').value.toLowerCase();
            const rows = document.querySelectorAll('.admin-movies-table tbody tr');

            rows.forEach(row => {
                const title = row.cells[0].textContent.toLowerCase();
                const categories = row.cells[3].textContent.toLowerCase();
                row.style.display = title.includes(searchText) || categories.includes(searchText) ? '' : 'none';
            });
        }
    </script>
</body>

</html>