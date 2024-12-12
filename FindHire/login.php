<?php 
require_once 'main/handleForms.php';
require_once 'main/models.php'; 


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login_input = filter_input(INPUT_POST, 'login_input', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    // Call login function from models.php
    if (login($pdo, $login_input, $password)) {
        // if Login is successful
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['message'] = "Invalid login credentials. Please try again.";
        header("Location: login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css"> 
</head>
<body>
    <?php if (isset($_SESSION['message'])): ?>
        <p style="color: red;"><?php echo $_SESSION['message']; ?></p>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <div class="container">
        <h1>Login</h1>
        <form action="login.php" method="POST">
            <p>
                <label for="login_input">Username or Email</label>
                <input type="text" name="login_input" required placeholder="Enter your username or email">
            </p>
            <p>
                <label for="password">Password</label>
                <input type="password" name="password" required>
            </p>
            <p><input type="submit" value="Login" id="loginBtn" name="loginBtn"></p>
        </form>
        <p>Don't have an account? Register as an applicant: <a href="register.php">Register as User</a>.</p> <br>
        <p>Don't have an account? Register as HR: <a href="register.php">Register as HR</a>.</p>
    </div>
</body>
</html>
