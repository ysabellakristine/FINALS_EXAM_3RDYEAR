<?php
require_once '../main/handleForms.php';
require_once '../main/models.php'; 
require_once 'authentication.php';

// Default values for the conversation view
$conversation_with = null;
$messages = [];

if (isset($_GET['conversation_with'])) {
    $conversation_with = $_GET['conversation_with'];

    // Validate the conversation_with parameter
    if (!is_numeric($conversation_with)) {
        echo "Invalid user ID.";
        exit;
    }

    // Fetch the conversation messages between the logged-in user and the selected recipient
    $sql = "
        SELECT m.*, u1.first_name AS sender_first_name, u1.last_name AS sender_last_name,
               u2.first_name AS receiver_first_name, u2.last_name AS receiver_last_name
        FROM messages m
        JOIN users u1 ON m.sender_id = u1.user_id
        JOIN users u2 ON m.receiver_id = u2.user_id
        WHERE (m.sender_id = ? AND m.receiver_id = ?)
           OR (m.sender_id = ? AND m.receiver_id = ?)
        ORDER BY m.date_sent ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['user_id'], $conversation_with, $conversation_with, $_SESSION['user_id']]);
    $messages = $stmt->fetchAll();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Private Messages</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>

<div class="topnav">
    <a class="active" href="index.php">Home</a>
    <div class="right">
        <a href="message_list.php">Inbox</a>
    </div>
</div>

<div class="container">
    <h1>Private Conversation</h1>
    
    <!-- Display the conversation header if conversation_with is set -->
    <?php if (isset($conversation_with) && !empty($messages)): ?>
        <h2>Conversation with <?php 
            // Fetch the recipient's name
            $recipient_sql = "SELECT first_name, last_name FROM users WHERE user_id = ?";
            $recipient_stmt = $pdo->prepare($recipient_sql);
            $recipient_stmt->execute([$conversation_with]);
            $recipient = $recipient_stmt->fetch();
            echo htmlspecialchars($recipient['first_name'] . ' ' . $recipient['last_name']);
        ?></h2>

        <!-- Display the conversation messages -->
        <div class="conversation">
            <?php foreach ($messages as $message): ?>
                <div class="message">
                    <hr>
                    <?php if ($message['sender_id'] == $_SESSION['user_id']): ?>
                        <p><strong>You</strong></p>
                    <?php else: ?>
                        <p> <?php echo htmlspecialchars($message['sender_first_name'] . ' ' . $message['sender_last_name']); ?></p>
                    <?php endif; ?>

                    <p><br><?php echo nl2br(htmlspecialchars($message['message']));?></p> <br>

                    <?php if ($message['pdf_file_path']): ?>
                        <p><a href="<?php echo htmlspecialchars($message['pdf_file_path']); ?>" target="_blank">View PDF</a></p>
                    <?php endif; ?>

                    <p><em>Date Sent: <?php echo htmlspecialchars($message['date_sent']); ?></em></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No conversation selected or no messages in this conversation.</p>
    <?php endif; ?>

    <!-- Always show the "Send Message" form -->
    <h3>Send a Message</h3>
    <form action="../main/handleForms.php" method="POST" enctype="multipart/form-data">
        <!-- If there is a selected conversation, pre-fill the recipient ID -->
        <input type="hidden" name="receiver_id" value="<?php echo isset($conversation_with) ? htmlspecialchars($conversation_with) : ''; ?>">
        <textarea name="message" placeholder="Write your message..." required></textarea><br><br>
        <label for="pdf_file">Attach PDF (optional):</label>
        <input type="file" name="pdf_file" id="pdf_file"><br><br>
        <input type="submit" name="send_message" value="Send Message">
    </form>
</div>

</body>
</html>
