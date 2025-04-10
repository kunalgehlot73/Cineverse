<?php
require_once 'includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM users WHERE id = $user_id");
if($result->num_rows == 0) {
    header("Location: logout.php");
    exit();
}
$user = $result->fetch_assoc();

$_SESSION['username'] = $user['username'];
$_SESSION['email'] = $user['email'];
$_SESSION['is_admin'] = $user['is_admin'];
$join_date = date('Y', strtotime($user['created_at']));
$_SESSION['bio'] = $user['bio'];
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - CineVerse</title>
    <link rel="stylesheet" href="css/headerfooter.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<style>
.profile-container {
    max-width: 1200px;
    margin: 2rem auto;
    background-color: var(--bg);
}
.profile-header {
    display: flex;
    align-items: center;
    gap: 2rem;
    padding: 2rem;
    background-color: var(--mov_list_item_bg);
    border-radius: 10px;
    margin-bottom: 2rem;
}
.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    overflow: hidden;
    border: 4px solid var(--gold);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
    transition: all 0.3s ease;
    flex-shrink: 0;
}
.profile-avatar:hover {
    transform: scale(1.05);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.5), 0 0 0 2px rgba(244, 193, 15, 0.3);
}
.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: all 0.5s ease;
}
.profile-avatar:hover img {
    transform: scale(1.1);
}
.profile-user-info {
    flex: 1;
}
.profile-user-info h1 {
    font-size: 2.2rem;
    margin-bottom: 0.5rem;
    color: white;
}
.profile-user-info p {
    color: #888;
    font-size: 1rem;
}
.profile-content {
    max-width: 800px;
    margin: 0 auto;
}
.profile-section {
    display: block;
    padding: 2.5rem;
    background-color: var(--mov_list_item_bg);
    border-radius: 10px;
    margin-bottom: 2rem;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}
.profile-section h2 {
    color: var(--gold);
    margin-bottom: 2rem;
    font-size: 1.8rem;
    position: relative;
    padding-bottom: 0.5rem;
}
.profile-section h2::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 60px;
    height: 3px;
    background-color: var(--gold);
}
.settings-form {
    display: grid;
    gap: 2rem;
}
.profile-form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}
.profile-form-group label {
    color: white;
    font-size: 1rem;
}
.profile-form-group input,
.profile-form-group textarea {
    padding: 0.8rem 1rem;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid #444;
    border-radius: 5px;
    color: white;
    transition: all 0.3s ease;
}
.profile-form-group input:focus,
.profile-form-group textarea:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 0 2px rgba(244, 193, 15, 0.1);
}
.profile-form-buttons {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}
.save-settings-btn {
    background-color: var(--gold);
    color: var(--header);
    font-weight: bold;
    padding: 0.8rem 2.5rem;
    border: none;
    border-radius: 30px;
    cursor: pointer;
    transition: all 0.3s ease;
}
.cancel-btn {
    background-color: rgba(255, 255, 255, 0.1);
    color: white;
    padding: 0.8rem 2.5rem;
    border: none;
    border-radius: 30px;
    cursor: pointer;
    transition: all 0.3s ease;
}
.save-settings-btn:hover {
    background-color: #e6b50f;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(244, 193, 15, 0.3);
}
.cancel-btn:hover {
    background-color: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
}
</style>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <div class="profile-container">
            <div class="profile-header">
                <div class="profile-avatar">
                    <img src="img/avatar.png" alt="User Avatar">
                </div>
                <div class="profile-user-info">
                    <h1><?php echo htmlspecialchars(isset($_SESSION['username'])?$_SESSION['username']:'Error 💀')?></h1>
                    <p>Cinephile since <?= $join_date . ' 🗿' ?? 'birth 🗿' ?></p>
                </div>
            </div>

            <div class="profile-content">
                <section class="profile-section">
                    <h2>Edit Profile</h2>
                    <?php if (!empty($_SESSION['update_success'])): ?>
                        <div class='alert-success'>
                            <span class='success-text'>
                                <?= $_SESSION['update_success'] ?>
                                <?php unset($_SESSION['update_success']); ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($_SESSION['update_errors'])): ?>
                        <div class="alert">
                            <?php foreach ($_SESSION['update_errors'] as $error): ?>
                                <span class='error-text'><?= $error?></span>
                            <?php endforeach; ?>
                            <?php unset($_SESSION['update_errors']); ?>
                        </div>
                    <?php endif; ?>

                    <form action="profile_process.php" method="post">
                        <div class="settings-form">
                            <div class="profile-form-group">
                                <label for="display-name">Display Name</label>
                                <input type="text" id="display-name" name="profile-name" value="<?php echo htmlspecialchars(isset($_SESSION['username'])?$_SESSION['username']:'')?>" placeholder="Enter Name ...">
                            </div>

                            <div class="profile-form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="profile-email" value="<?php echo htmlspecialchars(isset($_SESSION['email'])?$_SESSION['email']:'')?>" placeholder="Enter Email ...">
                            </div>

                            <div class="profile-form-group">
                                <label for="bio">Bio</label>
                                <textarea id="bio" name="profile-bio" rows="4" placeholder="Description ..."><?php echo htmlspecialchars(isset($_SESSION['bio'])?$_SESSION['bio']:'')?></textarea>
                            </div>

                            <div class="profile-form-buttons">
                                <button type="submit" class="save-settings-btn" name="profile-changes">Save Changes</button>
                                <button type="reset" class="cancel-btn">Cancel</button>
                            </div>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </main>
    <?php include 'includes/footer.php';?>
</body>

</html>