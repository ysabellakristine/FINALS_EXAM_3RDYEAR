<?php
require_once '../main/dbConfig.php';
require_once '../main/models.php';

if (!isset($_SESSION['username'])) {
	header("Location: ../login.php");
}

$getUserByID = getUserByID($pdo, $_SESSION['user_id']);

if ($getUserByID['is_admin'] == 0) {
    $_SESSION['message'] = "You are not authorized to access this page";  // Store the message in session
    header("Location: ../applicant/index.php");
    exit;  // Stop further execution after redirect
    }?>

<?php
// Display the message if it is set in the session
if (isset($_SESSION['message'])): ?>
    <div class="alert">
        <p><?php echo htmlspecialchars($_SESSION['message']); ?></p>
    </div>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>

