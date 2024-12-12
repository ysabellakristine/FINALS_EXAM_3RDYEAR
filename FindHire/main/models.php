<?php
require_once 'handleForms.php';
require_once 'dbConfig.php'; 
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} 
function getUserByID($pdo, $user_id) {
	$sql = "SELECT * FROM users WHERE user_id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$user_id]);

	if ($executeQuery) {
		return $stmt->fetch();
	}
}

function logAction($pdo, $user_id, $role, $action_type, $action_details) { // for audit logs
    $sql = "INSERT INTO audit_logs (user_id, role, action_type, action_details) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $executeQuery = $stmt->execute([$user_id, $role, $action_type, $action_details]);

    return $executeQuery; // Returns true on success, false otherwise
}

function addUser($conn, $username, $password) {
    // Check if username already exists
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username]);

    if ($stmt->rowCount() == 0) {
        // Hash the password before storing it
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user into the database
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$username, $hashedPassword]);
    } else {
        // Handle the case where the username already exists
        return false; // Optionally, you can throw an exception or return a specific error code
    }
}

function getAllUsers($pdo) {
	$sql = "SELECT * FROM user_accounts 
			WHERE is_admin = 0";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute();

	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}

function getAllAdmins($pdo) {
	$sql = "SELECT * FROM user_accounts 
			WHERE is_admin = 1";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute();

	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}

function insertJobPost($pdo, $title, $description, $location, $salary,$application_deadline, $user_id, $date_posted, $status) {

    $sql = "INSERT INTO job_posts (title, description, location, salary, application_deadline, hr_user_id, date_posted, status) VALUES(?,?,?,?,?,?,?,?)";
    $stmt = $pdo->prepare($sql);
    $executeQuery = $stmt->execute([$title, $description, $location, $salary ,$application_deadline, $user_id, $date_posted, $status]);

    if ($executeQuery) {
        logAction($pdo, $user_id, 'Admin', 'CREATE', "Created Job Post: $title");
        return true;
    }
}

    function getAllApplications($pdo) {
	$sql = "SELECT * FROM applicants";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute();

	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}