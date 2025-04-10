<?php
session_start();
require_once 'includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}

$username = trim($_POST['profile-name'] ?? '');
$email = trim($_POST['profile-email'] ?? '');
$bio = trim($_POST['profile-bio'] ?? '');
$user_id = (int)$_SESSION['user_id'];

$errors = [];
if (empty($username)) {
    $errors[] = "Username is required.";
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format.";
}

if (!empty($errors)) {
    $_SESSION['update_errors'] = $errors;
    header("Location: profile.php");
    exit();
}

try {
    $stmt = $conn->prepare("
        UPDATE users 
        SET 
            username = ?, 
            email = ?, 
            bio = ? 
        WHERE id = ?
    ");
    $stmt->bind_param("sssi", $username, $email, $bio, $user_id);
    $stmt->execute();

    $_SESSION['username'] = $username;
    $_SESSION['email'] = $email;
    $_SESSION['bio'] = $bio;

    $_SESSION['update_success'] = "Profile updated successfully!";
    header("Location: profile.php");
    exit();

} catch (Exception $e) {
    error_log("Update error: " . $e->getMessage());
    $_SESSION['update_errors'] = ["Failed to update profile. Please try again."];
    header("Location: profile.php");
    exit();
}
?>