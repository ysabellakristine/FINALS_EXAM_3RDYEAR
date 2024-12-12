<?php
require_once '../main/dbConfig.php'; // Include your database configuration
require_once 'authentication.php'; // Check authentication (ensure the user is logged in)
require_once '../main/models.php';  // Include any models if necessary

// Prepare SQL query to fetch audit logs along with user details
$stmt = $pdo->prepare("SELECT al.log_id, u.first_name, u.last_name, al.role, al.action_type, al.action_details, al.timestamp
                       FROM audit_logs al
                       JOIN users u ON al.user_id = u.user_id
                       ORDER BY al.log_id ASC"); // Order logs by timestamp, most recent first

// Execute the query
$stmt->execute();
$auditLogs = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all results

// Check if there are audit logs
$message = $stmt->rowCount() === 0 ? "No audit logs found." : "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Logs</title>
    <link rel="stylesheet" href="../styles.css"> <!-- Link your CSS file -->
</head>
<body>
    <h1>Audit Logs</h1>
    
    <!-- Display message if no audit logs are found -->
    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    
    <!-- Audit Logs Table -->
    <div class="container">
        <table border="1">
            <thead>
                <tr>
                    <th>Log ID</th>
                    <th>User</th>
                    <th>Role</th>
                    <th>Action Type</th>
                    <th>Action Details</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($auditLogs as $log): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($log['log_id']); ?></td>
                        <td><?php echo htmlspecialchars($log['first_name']) . ' ' . htmlspecialchars($log['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($log['role']); ?></td>
                        <td><?php echo htmlspecialchars($log['action_type']); ?></td>
                        <td><?php echo htmlspecialchars($log['action_details']); ?></td>
                        <td><?php echo htmlspecialchars($log['timestamp']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <!-- Navigation Links -->
    <hr>
    <a href="view_users.php" class="button">View Users</a>
    <p><a href="index.php">RETURN</a></p>
</body>
</html>
