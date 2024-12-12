<?php
require_once '../main/dbConfig.php';
require_once '../main/models.php';

// Authentication Check
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;  // Stop further execution after redirect
}

// Get User Details
$getUserByID = getUserByID($pdo, $_SESSION['user_id']);

// Authorization Check: If user is admin, redirect them
if ($getUserByID['is_admin'] == 1) {
    $_SESSION['message'] = "Redirecting...";  // Store the message in session
    header("Location: ../hr_user/index.php");
    exit;  // Stop further execution after redirect
} ?>

<?php
// Display the message if it is set in the session
if (isset($_SESSION['message'])): ?>
    <div class="alert">
        <p><?php echo htmlspecialchars($_SESSION['message']); ?></p>
    </div>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>
