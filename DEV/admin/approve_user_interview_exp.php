<?php
session_start();
include 'db_connection.php';

// Check if the user is an admin
if ($_SESSION['role'] !== 'admin') {
    echo "Access denied.";
    exit;
}

// Check for POST request to approve an experience
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $experience_id = $_POST['experience_id'];
    $sql = "UPDATE user_interview_experience SET is_approved = 1 WHERE experience_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $experience_id);

    if ($stmt->execute()) {
        // Use session to store a message to avoid header issues
        $_SESSION['message'] = "Experience approved.";
    } else {
        $_SESSION['message'] = "Error: could not approve experience.";
    }

    header("Location: approve_user_interview_exp.php");
    exit;
}

// Fetch pending experiences with user name
$sql = "SELECT e.experience_id, e.experience_text, e.created_at, u.name AS user_name
        FROM user_interview_experience e
        JOIN users u ON e.user_id = u.user_id
        WHERE e.is_approved = 0";
$pendingExperiences = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Interview Experiences</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .approve-button {
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

<h2>Pending Interview Experiences for Approval</h2>

<?php
// Display success/error messages from session
if (isset($_SESSION['message'])) {
    echo "<p>" . $_SESSION['message'] . "</p>";
    unset($_SESSION['message']);
}
?>

<table>
    <thead>
        <tr>
            <th>S.No</th>
            <th>User Name</th>
            <th>Description</th>
            <th>Time Written</th>
            <th>Approve Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $serialNumber = 1;
        while ($experience = $pendingExperiences->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $serialNumber++ . "</td>";
            echo "<td>" . htmlspecialchars($experience['user_name']) . "</td>";
            echo "<td>" . htmlspecialchars($experience['experience_text']) . "</td>";
            echo "<td>" . htmlspecialchars($experience['created_at']) . "</td>";
            echo "<td>Pending</td>";
            echo "<td>
                    <form method='POST' action=''>
                        <input type='hidden' name='experience_id' value='" . htmlspecialchars($experience['experience_id']) . "'>
                        <button type='submit' class='approve-button'>Approve</button>
                    </form>
                  </td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>
