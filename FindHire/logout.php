<?php
require_once 'main/dbconfig.php';
require_once 'main/models.php';

// Ensure the user is logged in before proceeding
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if the user is an admin or an applicant
if ($user['is_admin'] == 1) {
    // Admin: Log out and redirect to admin dashboard
    logAction($pdo, $_SESSION['user_id'], 'Admin', 'LOG OUT', "Logged out: " . $_SESSION['username']);
} else {
    // Applicant: Log out and redirect to applicant dashboard
    logAction($pdo, $_SESSION['user_id'], 'Applicant', 'LOG OUT', "Logged out: " . $_SESSION['username']);
}

// Unset all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to login page
header('Location: login.php');
exit();  // Ensure the script stops executing after the redirect
?>
