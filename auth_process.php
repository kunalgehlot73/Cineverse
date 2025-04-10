<?php

session_start();
require_once 'includes/config.php';

if (isset($_POST['register-submit'])) {
    $name = $_POST['register-name'];
    $email = $_POST['register-email'];
    $password = $_POST['register-password'];
    $confirmPassword = $_POST['register-confirm-password'];


    if ($password !== $confirmPassword) {
        $_SESSION['register_error'] = 'Passwords do not match!';
        $_SESSION['active_form'] = 'register';
    } elseif (strlen($password) < 8) {
        $_SESSION['register_error'] = 'Password must be at least 8 characters!';
        $_SESSION['active_form'] = 'register';
    } else {
        $checkName = $conn->query("SELECT username FROM users WHERE username = '$name'");
        $checkEmail = $conn->query("SELECT email FROM users WHERE email = '$email'");
        if ($checkName->num_rows > 0) {
            $_SESSION['register_error'] = 'Username is already registered!';
            $_SESSION['active_form'] = 'register';
        } else if ($checkEmail->num_rows > 0) {
            $_SESSION['register_error'] = 'Email is already registered!';
            $_SESSION['active_form'] = 'register';
        } else {
            try {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $name, $email, $hashedPassword);
                $stmt->execute();
                $_SESSION['register_success'] = "Registration Successfull! 👍";
                $_SESSION['active_form'] = 'login';
                header("Location: auth.php");
                exit();
            } catch (Exception $e) {
                error_log("Register error: " . $e->getMessage());
                $_SESSION['register_error'] = 'Failed to register. Please try again';
                header("Location: auth.php");
                exit();
            }
        }
    }
    $_SESSION['register_data'] = [
        'register_name' => $name,
        'register_email' => $email
    ];
    header("Location: auth.php");
    exit();
}

if(isset($_POST['login-submit'])) {
    $email = $_POST['login-email'];
    $password = $_POST['login-password'];
    $result = $conn->query("SELECT * FROM users WHERE email = '$email'");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['is_admin'] = $user['is_admin'];
            header("Location: profile.php");
            exit();
        }
    }
    $_SESSION['login_data'] = [
        'login_email' => $email,
        'login_password' => $password
    ];
    $_SESSION['login_error'] = 'Incorrect email or password';
    $_SESSION['active_form'] = 'login';
    header("Location: auth.php");
    exit();
}