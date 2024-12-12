<?php
require_once 'main/dbConfig.php';
require_once 'main/models.php'; 
require_once 'authentication.php';


// Fetch users from the database
$stmt = $pdo->prepare("SELECT user_id, username, first_name, last_name, gender, age, date_of_birth, email, address, contact_no,
                        CASE WHEN is_admin = 1 THEN 'Admin' ELSE 'Applicant' END AS role
                        FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if there are users
$message = $stmt->rowCount() === 0 ? "No users found." : "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>User List</h1>
    
    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <div class="table_container">
        <table border="1">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Role</th> 
                    <th>Username</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Gender</th>
                    <th>Age</th>
                    <th>Date of Birth</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Contact Number</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?> </td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['gender']); ?></td>
                        <td><?php echo htmlspecialchars($user['age']); ?></td>
                        <td><?php echo htmlspecialchars($user['date_of_birth']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['address']); ?></td>
                        <td><?php echo htmlspecialchars($user['contact_no']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<hr><hr>
<a href="view_audit_logs.php" class="button">View Audit Logs</a>
    <p><a href="index.php">RETURN</a></p>
</body>
</html>
