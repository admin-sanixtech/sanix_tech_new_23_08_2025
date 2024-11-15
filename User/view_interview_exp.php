<?php
include 'db_connection.php';

// Fetch approved experiences with user details
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
        .experience-box {
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 20px;
            padding: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .experience-box .user-name {
            font-weight: bold;
            padding: 10px 0;
            border-bottom: 2px solid #4CAF50;
            color: #333;
        }
        .experience-text {
            margin: 15px 0;
            font-size: 16px;
            color: #555;
        }
        .comment-box {
            margin-top: 15px;
            display: flex;
            flex-direction: column;
        }
        .comment-box textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            resize: vertical;
        }
        .comment-box button {
            margin-top: 10px;
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<h2>Approved Interview Experiences</h2>

<?php
while ($experience = $approvedExperiences->fetch_assoc()) {
    echo "<div class='experience-box'>";
    echo "<div class='user-name'>" . htmlspecialchars($experience['user_name']) . "</div>";
    echo "<div class='experience-text'>" . nl2br(htmlspecialchars($experience['experience_text'])) . "</div>";
    
    // Comment box
    echo "<div class='comment-box'>";
    echo "<form method='POST' action='submit_comment.php'>";  // Assumes submit_comment.php handles the comment saving
    echo "<input type='hidden' name='experience_id' value='" . htmlspecialchars($experience['experience_id']) . "'>";
    echo "<textarea name='comment_text' rows='3' placeholder='Write a comment...'></textarea>";
    echo "<button type='submit'>Post Comment</button>";
    echo "</form>";
    echo "</div>";

    echo "</div>";
}
?>

</body>
</html>
