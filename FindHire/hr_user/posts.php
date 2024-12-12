<?php
require_once '../main/handleForms.php';
require_once '../main/models.php'; 
require_once 'authentication.php';

// Function to get all job posts
function getAllPosts($pdo) {
    $sql = "SELECT * FROM job_posts";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute()) {
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Use associative array for easier access
    } else {
        // Handle error if query fails
        return [];
    }
}

// Fetch all job posts
$jobPosts = getAllPosts($pdo);
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
  <div class="right"> 
    <a href="create_jobpost.php">Create Job Post</a>
    <a href="posts.php">Job Posts</a>
    <a href="message_list.php">Messages</a> 
    <a href="view_logs.php">View Logs</a>
    <a href="logout.php" class="button">LOGOUT</a>
  </div>
</div>

<div class="job-posts-container">
    <h2>Job Posts</h2>

    <?php if (count($jobPosts) > 0): ?>
        <?php foreach ($jobPosts as $jobPost): ?>
            <div class="job-post-card">
                <h3 class="job-title"><?php echo htmlspecialchars($jobPost['title']); ?></h3>
                <p class="job-description"><?php echo nl2br(htmlspecialchars($jobPost['description'])); ?></p>

                <?php if (!empty($jobPost['location'])): ?>
                    <p class="job-location"><strong>Location:</strong> <?php echo htmlspecialchars($jobPost['location']); ?></p>
                <?php endif; ?>

                <?php if ($jobPost['salary']): ?>
                    <p class="job-salary"><strong>Salary:</strong> $<?php echo number_format($jobPost['salary'], 2); ?></p>
                <?php else: ?>
                    <p class="job-salary"><strong>Salary:</strong> Negotiable</p>
                <?php endif; ?>

                <?php if ($jobPost['application_deadline']): ?>
                    <p class="application-deadline"><strong>Application Deadline:</strong> <?php echo date("F j, Y", strtotime($jobPost['application_deadline'])); ?></p>
                <?php endif; ?>

                <p class="job-status"><strong>Status:</strong> <?php echo htmlspecialchars($jobPost['status']); ?></p>
                <p class="job-date-posted"><strong>Date Posted:</strong> <?php echo date("F j, Y", strtotime($jobPost['date_posted'])); ?></p>
                
                <div class="job-actions">
                    <a href="edit_jobpost.php?job_post_id=<?php echo $jobPost['job_post_id']; ?>" class="edit-link">EDIT</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No job posts available.</p>
    <?php endif; ?>
</div>

</body>
</html>
