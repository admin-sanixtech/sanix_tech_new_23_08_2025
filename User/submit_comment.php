<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: https://sanixtech.in");
    exit;
  }

$sql = "SELECT e.experience_id, e.experience_text, e.created_at, u.name AS user_name
        FROM user_interview_experience e
        JOIN users u ON e.user_id = u.user_id
        WHERE e.is_approved = 1";
$approvedExperiences = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approved Interview Experiences</title>
    <style>
        /* Add your CSS styling here */
    </style>
</head>
<body>

<h2>Approved Interview Experiences</h2>

<?php
while ($experience = $approvedExperiences->fetch_assoc()) {
    echo "<div class='experience-box'>";
    echo "<div class='user-name'>" . htmlspecialchars($experience['user_name']) . "</div>";
    echo "<div class='experience-text'>" . nl2br(htmlspecialchars($experience['experience_text'])) . "</div>";
    
    // Display comments for this experience
    $experience_id = $experience['experience_id'];
    $comments = $conn->query("SELECT c.comment_text, c.created_at, u.name AS commenter_name 
                              FROM comments c
                              JOIN users u ON c.user_id = u.user_id
                              WHERE c.experience_id = $experience_id
                              ORDER BY c.created_at ASC");
    echo "<div class='comments-section'>";
    while ($comment = $comments->fetch_assoc()) {
        echo "<p><strong>" . htmlspecialchars($comment['commenter_name']) . "</strong>: " 
             . htmlspecialchars($comment['comment_text']) . "<br><small>" 
             . $comment['created_at'] . "</small></p>";
    }
    echo "</div>";

    // Comment box
    echo "<div class='comment-box'>";
    echo "<form method='POST' action='submit_comment.php'>";
    echo "<input type='hidden' name='experience_id' value='" . htmlspecialchars($experience_id) . "'>";
    echo "<textarea name='comment_text' rows='3' placeholder='Write a comment...'></textarea>";
    echo "<button type='submit'>Post Comment</button>";
    echo "</form>";
    echo "</div>";

    echo "</div>";
}
?>

</body>
</html>
