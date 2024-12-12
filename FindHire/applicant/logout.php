<?php
require_once '../main/dbconfig.php';
require_once '../main/models.php';


    // Log the logout action to the audit log
    logAction($pdo, $_SESSION['user_id'], 'Applicant', 'LOG OUT', "Logged out: " . $_SESSION['username']);
    
    // Unset all session variables
    session_unset();

    // Destroy the session
    session_destroy();

    // Redirect to login page
    header('Location: ../login.php');
    exit();  // Ensure the script stops executing after the redirect

?>
