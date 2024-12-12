<?php 
require_once '../main/handleForms.php';
require_once '../main/models.php'; 
require_once 'authentication.php';
// Get the job post ID from the query string
$job_post_id = $_GET['job_post_id'] ?? null;

if ($job_post_id) {
    $sql = "SELECT * FROM job_posts WHERE job_post_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$job_post_id]);
    $jobPost = $stmt->fetch();
}

if (!$job_post_id || !$jobPost) {
    echo "<p>Job post not found or invalid ID.</p>";
    exit;
}

// Handle file upload and message submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['pdf_file'])) {
    $message = $_POST['message'] ?? '';
    $file = $_FILES['pdf_file'];

    if ($file['type'] != 'application/pdf') {
        echo "<p>Please upload a valid PDF file.</p>";
    } else {
        $uploadDir = '../uploads/';
        $fileName = uniqid() . "_" . basename($file['name']);
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            $sql = "INSERT INTO file_uploads (job_post_id, file_path, message, uploaded_by) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);

            if ($stmt->execute([$job_post_id, $filePath, $message, $_SESSION['user_id']])) {
                echo "<p>File uploaded successfully!</p>";
            } else {
                echo "<p>Failed to save file information to the database.</p>";
            }
        } else {
            echo "<p>There was an error uploading the file.</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
<div class="topnav">
  <a class="active" href="index.php">Home</a>
  <div class ="right"> 
    <a href="posts.php">Job Posts</a>
    <a href="message_list.php">Messages</a>
    <a href="current_applications.php">Applications</a> </div>
    <a href="logout.php" class="button">LOGOUT</a>"
</div>
    <div class="container">
        <h2><?php echo htmlspecialchars($jobPost['title']); ?></h2>
        <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($jobPost['description'])); ?></p>
        <p><strong>Location:</strong> <?php echo htmlspecialchars($jobPost['location']); ?></p>
        <p><strong>Salary:</strong> $<?php echo number_format($jobPost['salary'], 2); ?></p>
        <p><strong>Application Deadline:</strong> <?php echo htmlspecialchars($jobPost['application_deadline']); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($jobPost['status']); ?></p>
        <p><strong>Date Posted:</strong> <?php echo htmlspecialchars($jobPost['date_posted']); ?></p>

        <!-- Upload PDF Section -->
        <div class="upload-section">
            <h3>Upload PDF and Leave a Message</h3>
            <form action="view_post.php?job_post_id=<?php echo htmlspecialchars($jobPost['job_post_id']); ?>" method="POST" enctype="multipart/form-data">
                <textarea name="message" rows="4" cols="50" placeholder="Leave a message..."></textarea><br>
                <input type="file" name="pdf_file" accept="application/pdf" required><br><br>
                <button type="submit">Upload</button>
            </form>
        </div>
    </div>
</body>
</html>
