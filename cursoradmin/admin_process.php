<?php
require_once 'includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}

// Function to validate and sanitize input
function sanitize_input($data)
{
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

// Handle Movie Operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add or Update Movie
    if (isset($_POST['action']) && ($_POST['action'] === 'add_movie' || $_POST['action'] === 'edit_movie')) {
        $title = sanitize_input($_POST['title']);
        $description = sanitize_input($_POST['synopsis']);
        $release_year = (int)$_POST['release_year'];
        $duration = (int)$_POST['duration'];
        $rating = floatval($_POST['rating']);
        $poster_url = sanitize_input($_POST['poster_url']);
        $poster_potrait_url = sanitize_input($_POST['poster_potrait_url']);
        $youtube_id = sanitize_input($_POST['youtube_id']);
        $featured = isset($_POST['featured']) ? 1 : 0;
        $categories = isset($_POST['categories']) ? $_POST['categories'] : [];

        if ($_POST['action'] === 'add_movie') {
            $sql = "INSERT INTO movies (title, description, release_year, duration, rating, poster_url, poster_potrait_url, youtube_id, featured) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssiidsssi", $title, $description, $release_year, $duration, $rating, $poster_url, $poster_potrait_url, $youtube_id, $featured);

            if ($stmt->execute()) {
                $movie_id = $conn->insert_id;
                // Add categories
                foreach ($categories as $category_id) {
                    $sql = "INSERT INTO movie_categories (movie_id, category_id) VALUES (?, ?)";
                    $cat_stmt = $conn->prepare($sql);
                    $cat_stmt->bind_param("ii", $movie_id, $category_id);
                    $cat_stmt->execute();
                }
                echo json_encode(['status' => 'success', 'message' => 'Movie added successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error adding movie']);
            }
        } else {
            $movie_id = (int)$_POST['movie_id'];
            $sql = "UPDATE movies SET title=?, description=?, release_year=?, duration=?, rating=?, 
                    poster_url=?, poster_potrait_url=?, youtube_id=?, featured=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "ssiidsssii",
                $title,
                $description,
                $release_year,
                $duration,
                $rating,
                $poster_url,
                $poster_potrait_url,
                $youtube_id,
                $featured,
                $movie_id
            );

            if ($stmt->execute()) {
                // Update categories
                $sql = "DELETE FROM movie_categories WHERE movie_id = ?";
                $del_stmt = $conn->prepare($sql);
                $del_stmt->bind_param("i", $movie_id);
                $del_stmt->execute();

                foreach ($categories as $category_id) {
                    $sql = "INSERT INTO movie_categories (movie_id, category_id) VALUES (?, ?)";
                    $cat_stmt = $conn->prepare($sql);
                    $cat_stmt->bind_param("ii", $movie_id, $category_id);
                    $cat_stmt->execute();
                }
                echo json_encode(['status' => 'success', 'message' => 'Movie updated successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error updating movie']);
            }
        }
    }

    // Delete Movie
    if (isset($_POST['action']) && $_POST['action'] === 'delete_movie') {
        $movie_id = (int)$_POST['movie_id'];

        // First delete from movie_categories
        $sql = "DELETE FROM movie_categories WHERE movie_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $movie_id);
        $stmt->execute();

        // Then delete the movie
        $sql = "DELETE FROM movies WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $movie_id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Movie deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error deleting movie']);
        }
    }

    // Category Operations
    if (isset($_POST['action']) && $_POST['action'] === 'add_category') {
        $name = sanitize_input($_POST['name']);
        $fa_icon = sanitize_input($_POST['fa_icon']);

        $sql = "INSERT INTO categories (name, fa_icon) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $name, $fa_icon);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Category added successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error adding category']);
        }
    }

    if (isset($_POST['action']) && $_POST['action'] === 'edit_category') {
        $category_id = (int)$_POST['category_id'];
        $name = sanitize_input($_POST['name']);
        $fa_icon = sanitize_input($_POST['fa_icon']);

        $sql = "UPDATE categories SET name=?, fa_icon=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $name, $fa_icon, $category_id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Category updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error updating category']);
        }
    }

    if (isset($_POST['action']) && $_POST['action'] === 'delete_category') {
        $category_id = (int)$_POST['category_id'];

        // First delete from movie_categories
        $sql = "DELETE FROM movie_categories WHERE category_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $category_id);
        $stmt->execute();

        // Then delete the category
        $sql = "DELETE FROM categories WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $category_id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Category deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error deleting category']);
        }
    }
}

// Handle GET requests for fetching data
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get all movies
    if (isset($_GET['action']) && $_GET['action'] === 'get_movies') {
        $sql = "SELECT m.*, GROUP_CONCAT(c.name) as categories 
                FROM movies m 
                LEFT JOIN movie_categories mc ON m.id = mc.movie_id 
                LEFT JOIN categories c ON mc.category_id = c.id 
                GROUP BY m.id";
        $result = $conn->query($sql);
        $movies = [];

        while ($row = $result->fetch_assoc()) {
            $movies[] = $row;
        }

        echo json_encode($movies);
    }

    // Get all categories
    if (isset($_GET['action']) && $_GET['action'] === 'get_categories') {
        $sql = "SELECT * FROM categories";
        $result = $conn->query($sql);
        $categories = [];

        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }

        echo json_encode($categories);
    }

    // Get single movie details
    if (isset($_GET['action']) && $_GET['action'] === 'get_movie' && isset($_GET['id'])) {
        $movie_id = (int)$_GET['id'];
        $sql = "SELECT m.*, GROUP_CONCAT(c.id) as category_ids, GROUP_CONCAT(c.name) as category_names 
                FROM movies m 
                LEFT JOIN movie_categories mc ON m.id = mc.movie_id 
                LEFT JOIN categories c ON mc.category_id = c.id 
                WHERE m.id = ?
                GROUP BY m.id";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $movie_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $movie = $result->fetch_assoc();

        echo json_encode($movie);
    }
}
