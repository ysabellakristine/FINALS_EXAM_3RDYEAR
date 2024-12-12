<?php
require_once '../main/dbConfig.php';
require_once '../main/models.php';
require_once 'authentication.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Job Post</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<div class="topnav">
  <a class="active" href="index.php">Home</a>
  <div class="right"> 
    <a href="create_jobpost.php">Create Job Post</a>
    <a href="posts.php">Job Posts</a>
    <a href="message_list.php">Messages</a> 
    <a href="view_logs.php">View Logs</a>
    <a href="logout.php" class="button">LOGOUT</a>
  </div>
</div>
<body>
    <div class="container">
    <h1>Create a New Job Post!
    </h1>

    <?php if (isset($message)): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form action="../main/handleForms.php" method="POST">
        <label for="title">Job Title:</label>
        <input type="text" id="title" name="title" required><br><br>

        <label for="description">Job Description:</label>
        <textarea id="description" name="description" required></textarea><br><br>

        <label for="location">Job Location:</label>
        <input type="text" id="location" name="location"><br><br>

        <label for="salary">Salary (Optional):</label>
        <input type="number" id="salary" name="salary"><br><br>

        <label for="application_deadline">Application Deadline:</label>
        <input type="date" id="application_deadline" name="application_deadline" required><br><br>

        <label for="status">Status:</label>
        <select id="status" name="status">
            <option value="Accepting Applicants">Accepting Applicants</option>
            <option value="No longer available">No longer available</option>
        </select><br><br>

        <input type="submit" name="insertJobPostBtn" value="Insert Job Post"> </p>
    </form>
</div>
    <p><a href="index.php">Return to Home</a></p>
</body>
</html>
