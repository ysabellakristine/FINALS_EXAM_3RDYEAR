<?php 
require_once '../main/handleForms.php';
require_once '../main/models.php'; 
require_once 'authentication.php';

// Fetch messages from the database, where the logged-in user is the receiver
$sql = "
    SELECT m.*, u1.first_name AS sender_first_name, u1.last_name AS sender_last_name, 
           u2.first_name AS receiver_first_name, u2.last_name AS receiver_last_name
    FROM messages m
    JOIN users u1 ON m.sender_id = u1.user_id
    JOIN users u2 ON m.receiver_id = u2.user_id
    WHERE m.receiver_id = ? 
    ORDER BY m.date_sent DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['user_id']]);

// Fetch all messages, or initialize as an empty array if no messages exist
$messages = $stmt->fetchAll() ?: [];

// Fetch all users for the recipient dropdown
$users_sql = "SELECT user_id, first_name, last_name FROM users";
$users_stmt = $pdo->prepare($users_sql);
$users_stmt->execute();
$users = $users_stmt->fetchAll();

// Reusable string
$inboxInfo = "This is your general inbox. Private Messages are in the <b>Messages</b> Tab.";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inbox</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
<div class="topnav">
  <a class="active" href="index.php">Home</a>
  <div class="right"> 
    <a href="select_user.php">Private Messages</a> 
  </div>
</div>

<div class="container">
    <h1>Your Inbox</h1>
    <p><?php echo $inboxInfo; ?></p>

    <?php if ($messages): ?>
        <!-- Loop through messages and display them -->
        <?php foreach ($messages as $message): ?>
            <div class="message">
                <hr>
                <p><strong>From:</strong> <?php echo htmlspecialchars($message['sender_first_name'] . ' ' . $message['sender_last_name']); ?></p>
                <p><strong>To:</strong> <?php echo htmlspecialchars($message['receiver_first_name'] . ' ' . $message['receiver_last_name']); ?></p>
                <p><strong>Message:</strong><br><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
                <?php if ($message['pdf_file_path']): ?>
                    <p><a href="<?php echo htmlspecialchars($message['pdf_file_path']); ?>" target="_blank">View PDF</a></p>
                <?php endif; ?>
                <p><em>Date Sent: <?php echo htmlspecialchars($message['date_sent']); ?></em></p>
                
                <!-- Reply Form -->
                <form action="../main/handleForms.php" method="POST">
                    <input type="hidden" name="message_id" value="<?php echo $message['message_id']; ?>">
                    <input type="hidden" name="receiver_id" value="<?php echo $message['sender_id']; ?>"> <!-- Sender becomes the receiver of the reply -->
                    <textarea name="reply_message" placeholder="Write your reply..." required></textarea><br>
                    <input type="submit" name="reply_btn" value="Reply">
                </form>
                <hr>

                <!-- Display all replies to this message -->
                <?php
                // Fetch all replies to this message
                $replies_sql = "
                    SELECT r.*, u.first_name AS sender_first_name, u.last_name AS sender_last_name
                    FROM replies r
                    JOIN users u ON r.sender_id = u.user_id
                    WHERE r.message_id = ?
                    ORDER BY r.date_sent ASC
                ";
                $replies_stmt = $pdo->prepare($replies_sql);
                $replies_stmt->execute([$message['message_id']]);
                $replies = $replies_stmt->fetchAll();

                if ($replies) {
                    foreach ($replies as $reply) {
                        echo '<div class="reply">';
                        echo '<p><strong>Reply from ' . htmlspecialchars($reply['sender_first_name'] . ' ' . $reply['sender_last_name']) . ':</strong><br>' . nl2br(htmlspecialchars($reply['reply_message'])) . '</p>';
                        echo '<p><em>Date Sent: ' . htmlspecialchars($reply['date_sent']) . '</em></p>';
                        echo '</div>';
                        echo '<hr>';
                    }
                } else {
                    echo '<p>No replies yet.</p>';
                }
                ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No messages.</p>
    <?php endif; ?>
</div>

</body>
</html>
