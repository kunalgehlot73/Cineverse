<?php
session_start();

if(isset($_SESSION['user_id'])) {
    header("Location: profile.php");
    exit();
}

$errors = [
    'login' =>  $_SESSION['login_error'] ?? '',
    'register' =>  $_SESSION['register_error'] ?? ''
];
$activeForm = $_SESSION['active_form'] ?? 'login';
function showError($error) {
    return !empty($error) ? "<div class='alert'><span class='error-text'>$error</span></div>" : '';
}

function isActiveForm($formName, $activeForm) {
    return $formName === $activeForm ? 'active' : '';
}

unset($_SESSION['login_error'], $_SESSION['register_error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Register - CineVerse</title>
    <link rel="stylesheet" href="css/headerfooter.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<style>
.auth-container {
    min-height: calc(100vh - 80px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    background: linear-gradient(135deg, var(--header) 0%, var(--bg) 100%);
}
.auth-box {
    background: var(--header);
    border-radius: 20px;
    padding: 2.5rem;
    width: 100%;
    max-width: 450px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}
.auth-form {
    display: none;
    transition: all 0.3s ease;
}
.auth-form.active {
    display: block;
}
.auth-form h2 {
    color: var(--gold);
    font-size: 2rem;
    margin-bottom: 0.5rem;
    text-align: center;
}
.auth-subtitle {
    color: #888;
    text-align: center;
    margin-bottom: 2rem;
}
.form-group {
    margin-bottom: 1.5rem;
}
.form-group label {
    display: block;
    color: #888;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}
.input-group {
    position: relative;
    display: flex;
    align-items: center;
}
.input-group i {
    position: absolute;
    left: 1rem;
    color: #888;
    z-index: 1;
}
.input-group input {
    width: 100%;
    padding: 0.8rem 1rem 0.8rem 2.5rem;
    border: 1px solid #333;
    border-radius: 8px;
    background: var(--bg);
    color: white;
    font-size: 1rem;
    transition: all 0.3s ease;
}
.input-group input:focus {
    border-color: var(--gold);
    outline: none;
    box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.1);
}
.toggle-password {
    position: absolute;
    right: 1rem;
    left: auto;
    cursor: pointer;
    color: #888;
    transition: color 0.3s ease;
    z-index: 1;
}
.toggle-password:hover {
    color: var(--gold);
}
.auth-button {
    width: 100%;
    padding: 1rem;
    background: var(--gold);
    border: none;
    border-radius: 8px;
    color: var(--header);
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}
.auth-button:hover {
    background: #ffd700;
    transform: translateY(-2px);
}
.auth-divider {
    display: flex;
    align-items: center;
    text-align: center;
    margin: 2rem 0;
}
.auth-divider::before,
.auth-divider::after {
    content: '';
    flex: 1;
    border-bottom: 1px solid #333;
}
.auth-divider span {
    padding: 0 1rem;
    color: #888;
    font-size: 0.9rem;
}
.social-auth {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}
.social-button {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.8rem;
    border: 1px solid #333;
    border-radius: 8px;
    background: var(--bg);
    color: white;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
}
.social-button:hover {
    border-color: var(--gold);
    color: var(--gold);
}
.social-button.google:hover {
    background: #DB4437;
    border-color: #DB4437;
    color: white;
}
.social-button.facebook:hover {
    background: #4267B2;
    border-color: #4267B2;
    color: white;
}
.auth-switch {
    text-align: center;
    margin-top: 2rem;
    color: #888;
}
.auth-switch a {
    color: var(--gold);
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
}
.auth-switch a:hover {
    color: #ffd700;
}
</style>
<body>

    <?php include 'includes/header.php';?>
    <main>

        <div class="auth-container">
            <div class="auth-box">
                <div class="auth-form login-form <?= isActiveForm('login', $activeForm);?>" id="login-form">
                    <h2>Welcome Back</h2>
                    <p class="auth-subtitle">Sign in to continue to CineVerse!</p>
                    <?php if (!empty($_SESSION['register_success'])): ?>
                        <div class='alert-success'>
                            <span class='success-text'>
                                <?= $_SESSION['register_success'] ?>
                                <?php unset($_SESSION['register_success']); ?>
                            </span>
                        </div>
                    <?php endif; ?>
                    <form method="post" action="auth_process.php">
                        <div class="form-group">
                            <label for="login-email">Email</label>
                            <div class="input-group">
                                <i class="fas fa-envelope"></i>
                                <input type="email" id="login-email" name="login-email" value="<?php if(isset($_SESSION['login_data']['login_email'])){ echo $_SESSION['login_data']['login_email']; }?>" required placeholder="Enter your email">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="login-password">Password</label>
                            <div class="input-group">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="login-password" name="login-password" value="<?php if(isset($_SESSION['login_data']['login_password'])){ echo $_SESSION['login_data']['login_password']; }?>" required placeholder="Enter your password">
                                <i class="fas fa-eye-slash toggle-password" style="right: 1rem; left: auto;"></i>
                            </div>
                        </div>
                        <?= showError($errors['login']); ?>
                        <?php unset($_SESSION['login_data']); ?>
                        <button type="submit" name="login-submit" class="auth-button">Sign In</button>
                    </form>
                    
                    <div class="auth-divider">
                        <span>or</span>
                    </div>
                    <div class="social-auth">
                        <button class="social-button google">
                            <i class="fab fa-google"></i>
                            Continue with Google
                        </button>
                        <button class="social-button facebook">
                            <i class="fab fa-facebook-f"></i>
                            Continue with Facebook
                        </button>
                    </div>
                    <p class="auth-switch">
                        Don't have an account? <a href="#" class="switch-form" onclick="showForm('register-form')">Sign Up</a>
                    </p>
                </div>
                
                <div class="auth-form register-form <?= isActiveForm('register', $activeForm);?>" id="register-form">
                    <h2>Create Account</h2>
                    <p class="auth-subtitle">Join our CineVerse community!</p>
                    
                    <form method="post" action="auth_process.php">
                        <div class="form-group">
                            <label for="register-name">Full Name</label>
                            <div class="input-group">
                                <i class="fas fa-user"></i>
                                <input type="text" id="register-name" name="register-name" value="<?php if(isset($_SESSION['register_data']['register_name'])){ echo $_SESSION['register_data']['register_name']; }?>" required placeholder="Enter your full name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="register-email">Email</label>
                            <div class="input-group">
                                <i class="fas fa-envelope"></i>
                                <input type="email" id="register-email" name="register-email" value="<?php if(isset($_SESSION['register_data']['register_email'])){ echo $_SESSION['register_data']['register_email']; }?>" required placeholder="Enter your email">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="register-password">Password</label>
                            <div class="input-group">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="register-password" name="register-password" required placeholder="Create a password">
                                <i class="fas fa-eye-slash toggle-password" style="right: 1rem; left: auto;"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="register-confirm-password">Confirm Password</label>
                            <div class="input-group">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="register-confirm-password" name="register-confirm-password" required placeholder="Confirm your password">
                                <i class="fas fa-eye-slash toggle-password" style="right: 1rem; left: auto;"></i>
                            </div>
                        </div>
                        <?= showError($errors['register']); ?>
                        <?php unset($_SESSION['register_data']); ?>
                        <button type="submit" name="register-submit" class="auth-button">Create Account</button>
                    </form>

                    <div class="auth-divider">
                        <span>or</span>
                    </div>
                    <div class="social-auth">
                        <button class="social-button google">
                            <i class="fab fa-google"></i>
                            Continue with Google
                        </button>
                        <button class="social-button facebook">
                            <i class="fab fa-facebook-f"></i>
                            Continue with Facebook
                        </button>
                    </div>
                    <p class="auth-switch">
                        Already have an account? <a href="#" class="switch-form" onclick="showForm('login-form')">Sign In</a>
                    </p>
                </div>
            </div>
        </div>

    </main>
    <?php include 'includes/footer.php';?>

    <script>
        
        function showForm(formId) {
            document.querySelectorAll(".auth-form").forEach(form => form.classList.remove('active'));
            document.getElementById(formId).classList.add('active');
        };


        document.querySelectorAll('.toggle-password').forEach(toggle => {
            toggle.addEventListener('click', () => {
                const input = toggle.previousElementSibling;
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                toggle.classList.toggle('fa-eye');
                toggle.classList.toggle('fa-eye-slash');
            });
        });

    </script>
</body>
</html>