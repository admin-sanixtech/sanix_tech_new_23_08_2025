<?php
// Start the session if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connection.php'; // Include the database connection

// Enable error reporting to help with debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in and has admin role
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. You must be an admin to view this page.");
}

// Handle image upload for editor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['imageUpload'])) {
    $imageDir = "images/";
    if (!is_dir($imageDir)) {
        mkdir($imageDir, 0777, true);
    }

    $imagePath = $imageDir . basename($_FILES['imageUpload']['name']);
    if (move_uploaded_file($_FILES['imageUpload']['tmp_name'], $imagePath)) {
        echo json_encode(["url" => "https://sanixtech.in/" . $imagePath]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Image upload failed."]);
    }
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_FILES['imageUpload'])) {
    // Fetching user inputs
    $subject = $_POST['subject'];
    $message = $_POST['message']; // This now includes rich content with embedded images

    // Fetching user emails
    $sql = "SELECT email FROM users";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Loop through each email and send mail
        while ($row = $result->fetch_assoc()) {
            $to = $row['email'];
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: info@sanixtech.in" . "\r\n";

            // Email content (rich text with images included)
            $emailContent = "
            <html>
            <head><title>{$subject}</title></head>
            <body>
                {$message} <!-- The message includes the rich HTML content -->
            </body>
            </html>";

            // Send email
            mail($to, $subject, $emailContent, $headers);
        }
        $successMessage = "Emails have been sent successfully.";
    } else {
        $errorMessage = "No users found in the database.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Send Email</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" />
    <script src="https://kit.fontawesome.com/ae360af17e.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/admin_styleone.css" />
    <style>
        .editor-toolbar {
            background: #f4f4f4;
            border: 1px solid #ddd;
            padding: 5px;
            display: flex;
            gap: 5px;
        }

        .editor-toolbar button {
            background: #fff;
            border: 1px solid #ddd;
            padding: 5px 10px;
            cursor: pointer;
        }

        .editor-toolbar button:hover {
            background: #e0e0e0;
        }

        .editor {
            border: 1px solid #ddd;
            min-height: 200px;
            padding: 10px;
            overflow-y: auto;
        }

        .editor img {
            max-width: 100%;
            height: auto;
        }

        .hidden-input {
            display: none;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <aside id="sidebar" class="js-sidebar">
        <?php include 'admin_menu.php'; ?>
    </aside>
    <div class="main">
        <?php include 'admin_navbar.php'; ?>
        <main class="content px-3 py-2">
            <div class="container-fluid">
                <div class="card border-0">
                    <div class="content">
                        <h2>Send Email to Users</h2>
                        <?php if (isset($successMessage)) { echo "<p style='color: green;'>{$successMessage}</p>"; } ?>
                        <?php if (isset($errorMessage)) { echo "<p style='color: red;'>{$errorMessage}</p>"; } ?>
                        <form action="" method="POST">
                            <label for="subject">Email Subject:</label>
                            <input type="text" id="subject" name="subject" class="form-control" required><br>

                            <label for="message">Email Content:</label>
                            <div class="editor-toolbar">
                                <button type="button" onclick="formatText('bold')">Bold</button>
                                <button type="button" onclick="formatText('italic')">Italic</button>
                                <button type="button" onclick="formatText('underline')">Underline</button>
                                <button type="button" onclick="formatText('justifyLeft')">Align Left</button>
                                <button type="button" onclick="formatText('justifyCenter')">Align Center</button>
                                <button type="button" onclick="formatText('justifyRight')">Align Right</button>
                                <button type="button" onclick="triggerImageUpload()">Insert Image</button>
                            </div>
                            <div id="editor" class="editor" contenteditable="true"></div>
                            <textarea id="message" name="message" class="hidden-input"></textarea><br>

                            <button type="submit" onclick="submitForm()" class="btn btn-primary">Send Email</button>
                        </form>

                        <!-- Hidden file input for image upload -->
                        <input type="file" id="imageUpload" style="display: none;" accept="image/*" onchange="uploadImage()">
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    function formatText(command) {
        document.execCommand(command, false, null);
    }

    function triggerImageUpload() {
        document.getElementById('imageUpload').click();
    }

    function uploadImage() {
        const fileInput = document.getElementById('imageUpload');
        const file = fileInput.files[0];

        const formData = new FormData();
        formData.append('imageUpload', file);

        fetch('', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.url) {
                document.execCommand('insertImage', false, data.url); // Absolute URL
            } else {
                alert('Image upload failed: ' + data.error);
            }
        })
        .catch(error => {
            alert('Error uploading image: ' + error.message);
        });
    }

    function submitForm() {
        // Copy the editor content into the hidden textarea before submitting
        const editorContent = document.getElementById('editor').innerHTML;
        document.getElementById('message').value = editorContent;
    }
</script>
</body>
</html>
