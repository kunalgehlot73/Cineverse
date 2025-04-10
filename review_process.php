<?php
require_once 'includes/config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}
if(isset($_POST['post-comment'])){
    $user_id = $_SESSION['user_id'];
    $rating = $_POST['rating'];
    $review = $_POST['comment'];
    $movie_id = $_POST['movie-id'];
    if(!strlen($review)==0){
        $stmt = $conn->prepare("INSERT INTO reviews (movie_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iids",$movie_id,$user_id,$rating,$review);
        $stmt->execute();
        $_SESSION['comment_success'] = "Comment added successfully";
        header("Location: movie_review.php?id=$movie_id");
        exit();
    }
}