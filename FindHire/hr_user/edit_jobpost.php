<?php 
require_once '../main/handleForms.php';
require_once '../main/models.php'; 
require_once 'authentication.php';

// Fetch job post details by ID
function getPostsByID($pdo, $job_post_id) {
    $sql = "SELECT * FROM job_posts WHERE job_post_id = ?";
    $stmt = $pdo->prepare($sql);
    $executeQuery = $stmt->execute([$job_post_id]);

    if ($executeQuery) {
        $result = $stmt->fetch();
        return $result !== false ? $result : null; // Return the job post or null
    }

    return null; // Return null if query fails
}

// Function to send message to applicant
function sendAcceptMessage($pdo, $sender_id, $receiver_id, $job_post_id, $message) {
    $sql = "INSERT INTO messages (sender_id, receiver_id, message, job_post_id) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$sender_id, $receiver_id, $message, $job_post_id]);
}

// Function to remove the applicant from the list after accepting
function removeApplicant($pdo, $applicant_id, $job_post_id) {
    // Use 'uploaded_by' to identify the applicant and 'job_post_id' to filter by the job post
    $sql = "DELETE FROM file_uploads WHERE uploaded_by = ? AND job_post_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$applicant_id, $job_post_id]);
}


// Ensure you get the job_post_id from the URL
if (isset($_GET['job_post_id'])) {
    $job_post_id = $_GET['job_post_id'];

    // Fetch job post details to pre-fill the form
    $job_post = getPostsByID($pdo, $job_post_id);
} else {
    // Handle the error if job_post_id is missing
    $error_message = "Invalid job post ID.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Job Post</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
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
<div class="container">
    <h1>Edit the Job Post</h1>

    <!-- Display error message if exists -->
    <?php if (isset($error_message)): ?>
        <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
    <?php endif; ?>

    <form action="../main/handleForms.php?job_post_id=<?php echo $job_post_id; ?>" method="POST">
        <p>
            <label for="title">Job Title:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($job_post['title']); ?>" required><br><br>

            <label for="description">Job Description:</label>
            <textarea id="description" name="description" required><?php echo htmlspecialchars($job_post['description']); ?></textarea><br><br>

            <label for="location">Job Location:</label>
            <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($job_post['location']); ?>"><br><br>

            <label for="salary">Salary (Optional):</label>
            <input type="number" id="salary" name="salary" value="<?php echo htmlspecialchars($job_post['salary']); ?>"><br><br>

            <label for="application_deadline">Application Deadline:</label>
            <input type="date" id="application_deadline" name="application_deadline" value="<?php echo htmlspecialchars($job_post['application_deadline']); ?>" required><br><br>

            <label for="status">Status:</label>
            <select id="status" name="status">
                <option value="Accepting Applicants" <?php echo ($job_post['status'] == 'Accepting Applicants') ? 'selected' : ''; ?>>Accepting Applicants</option>
                <option value="No longer available" <?php echo ($job_post['status'] == 'No longer available') ? 'selected' : ''; ?>>No longer available</option>
            </select><br><br>

            <input type="submit" name="updateJobPostBtn" value="Update Job Post">
        </p>
    </form>

    <p><a href="index.php">Return to Home</a></p>
        <hr>
    <h2> APPLICANTS </h2>
    <hr>
    <?php
    // Fetch uploaded files and messages for this job post
    $sql = "SELECT file_uploads.*, users.first_name, users.last_name, users.user_id FROM file_uploads 
            JOIN users ON file_uploads.uploaded_by = users.user_id
            WHERE job_post_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$job_post_id]);
    $uploads = $stmt->fetchAll();

    if ($uploads) {
        foreach ($uploads as $upload) {
            // Display the uploader's name
            echo '<p>Uploaded by: ' . htmlspecialchars($upload['first_name']) . ' ' . htmlspecialchars($upload['last_name']) . '</p>';
            echo '<div class="uploaded-file">';
            echo '<p><strong>Message:</strong> ' . htmlspecialchars($upload['message']) . '</p>';
            echo '<p><a href="' . $upload['file_path'] . '" target="_blank">View PDF</a></p>';
            echo '<form action="" method="POST">';
            echo '<input type="hidden" name="applicant_id" value="' . $upload['user_id'] . '">';
            echo '<input type="submit" name="acceptBtn" value="Accept Applicant">';
            echo '</form>';
            echo '</div>';
            echo'<hr>';
        }
    }

    // Handle accept applicant logic
    if (isset($_POST['acceptBtn'])) {
        $applicant_id = $_POST['applicant_id'];
        $sender_id = $_SESSION['user_id']; // Assuming HR user is logged in
        $message = "Congratulations! Your application for the job post has been accepted.";
        sendAcceptMessage($pdo, $sender_id, $applicant_id, $job_post_id, $message);

        // Remove the applicant from the list
        removeApplicant($pdo, $applicant_id, $job_post_id);
    }
    ?>
</div>
</body>
</html>
