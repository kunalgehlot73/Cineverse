<?php
require_once 'includes/config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}

if (isset($_POST['admin-submit-btn'])) {
    $admin_movieTitle = $_POST['admin-movieTitle'];
    $admin_releaseYear = $_POST['admin-releaseYear'];
    $admin_duration = $_POST['admin-duration'];
    $admin_director = $_POST['admin-movieDirector'];
    $admin_cast = $_POST['admin-movieCast'];
    $admin_genres = $_POST['admin-genres'];
    $admin_rating = $_POST['admin-rating'];
    $admin_synopsis = $_POST['admin-synopsis'];
    $admin_posterURL = $_POST['admin-poster-url'];
    $admin_portrait_posterURL = $_POST['admin-portrait-poster-url'];
    $admin_youtubeID = $_POST['admin-youtube-id'];

    $stmt = $conn->prepare("SELECT * FROM movies WHERE title = ? AND director = ?");
    $stmt->bind_param("ss", $admin_movieTitle, $admin_director);
    $stmt->execute();
    $check_movie = $stmt->get_result();
    if ($check_movie->num_rows > 0) {
        $_SESSION['add_error'] = ["Movie already exists in database"];
        header("Location: admin.php");
        exit();
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO movies (title, genre, description, release_year, duration, rating, poster_url, poster_portrait_url, youtube_id, director, cast) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssiidsssss", $admin_movieTitle, $admin_genres, $admin_synopsis, $admin_releaseYear, $admin_duration, $admin_rating, $admin_posterURL, $admin_portrait_posterURL, $admin_youtubeID, $admin_director, $admin_cast);
            $stmt->execute();
            $_SESSION['add_success'] = "Movie added successfully";
            header("Location: admin.php");
            exit();
        } catch (Exception $e) {
            error_log("Add Movie Error: " . $e->getMessage());
            $_SESSION['add_errors'] = ['Failed to add movie. Please try again'];
            header("Location: admin.php");
            exit();
        }
    }
}

if (isset($_POST['admin-delete-btn'])) {
    $movie_id = $_POST['movie_id'];

    if (!is_numeric($movie_id)) {
        header("Location: admin.php");
        exit();
    }

    try {
        $stmt = $conn->prepare("DELETE FROM movies WHERE id = ?");
        $stmt->bind_param("i", $movie_id);
        $stmt->execute();
        $_SESSION['delete_success'] = "Movie deleted successfully";
        header("Location: admin.php");
        exit();
    } catch (Exception $e) {
        error_log("Delete error: " . $e->getMessage());
        $_SESSION['delete_errors'] = ["Movie doesn't exist or Failed to delete movie. Please try again."];
        header("Location: admin.php");
        exit();
    }
}

if (isset($_POST['admin-edit-movie-btn'])) {
    $admin_movieTitle = $_POST['admin-movieTitle'];
    $admin_releaseYear = $_POST['admin-releaseYear'];
    $admin_duration = $_POST['admin-duration'];
    $admin_director = $_POST['admin-movieDirector'];
    $admin_cast = $_POST['admin-movieCast'];
    $admin_genres = $_POST['admin-genres'];
    $admin_rating = $_POST['admin-rating'];
    $admin_synopsis = $_POST['admin-synopsis'];
    $admin_posterURL = $_POST['admin-poster-url'];
    $admin_portrait_posterURL = $_POST['admin-portrait-poster-url'];
    $admin_youtubeID = $_POST['admin-youtube-id'];
    
    $stmt = $conn->prepare("SELECT * FROM movies WHERE title = ? AND director = ?");
    $stmt->bind_param("ss", $admin_movieTitle, $admin_director);
    $stmt->execute();
    $check_movie = $stmt->get_result();
    if ($check_movie->num_rows > 0) {
        try {
            $existing_movie_details = $check_movie->fetch_assoc();
            $existing_movieID = $existing_movie_details['id'];
            $stmt = $conn->prepare("UPDATE movies SET title = ?, genre = ?, description = ?, release_year = ?, duration = ?, rating = ?, poster_url = ?, poster_portrait_url = ?, youtube_id = ?, director = ?, cast = ? WHERE id = ?");
            $stmt->bind_param("sssssdsssssi", $admin_movieTitle, $admin_genres, $admin_synopsis, $admin_releaseYear, $admin_duration, $admin_rating, $admin_posterURL, $admin_portrait_posterURL, $admin_youtubeID, $admin_director, $admin_cast, $existing_movieID);
            $stmt->execute();
            $_SESSION['edit_success'] = "Movie edited successfully";
            header("Location: movie_review.php?id=$existing_movieID");
            exit();
        } catch (Exception $e) {
            error_log("Update error: " . $e->getMessage());
            $_SESSION['edit_errors'] = ["Failed to edit movie. PLease try again"]; 
            header("Location: movie_review.php?id=$existing_movieID");
            exit();
        }
    }
}