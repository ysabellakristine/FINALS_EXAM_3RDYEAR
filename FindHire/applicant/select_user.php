<?php
require_once '../main/handleForms.php';
require_once '../main/models.php'; 
require_once 'authentication.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch all users except the logged-in user
$sql = "SELECT user_id, first_name, last_name FROM users WHERE user_id != ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['user_id']]);
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select User for Private Messages</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>

<div class="topnav">
    <a class="active" href="index.php">Home</a>
    <div class="right">
        <a href="../open_message.php">Open Chat</a>
        <a href="message_list.php">Messages</a>
    </div>
</div>

<div class="container">
    <h1>Select User for Private Conversation</h1>

    <!-- Dropdown form to select a user for the private message -->
    <form action="private_messages.php" method="get">
        <label for="recipient">Select a User to Message:</label>
        <select name="conversation_with" id="recipient" required>
            <option value="">--Select User--</option>
            <?php foreach ($users as $user): ?>
                <option value="<?php echo htmlspecialchars($user['user_id']); ?>">
                    <?php echo htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>
        <input type="submit" value="Start Conversation">
    </form>
</div>

</body>
</html>
