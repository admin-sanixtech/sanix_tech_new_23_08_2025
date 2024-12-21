<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: access_denied.php');
    exit();
}

// Include database connection
include 'db_connection.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Fetch categories and subcategories for the dropdown filters
$categories = $conn->query("SELECT category_id, category_name FROM sanixazs_main_db.categories");
$subcategories = $conn->query("SELECT subcategory_id, subcategory_name, category_id FROM sanixazs_main_db.subcategories");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin_styles.css">
</head>
<body>

<?php
include 'header.php';
include 'sidebar.php';
?>

<div class="main-content">
    <label>Category:</label>
    <select id="categoryFilter" onchange="filterQuestions()">
        <option value="">All Categories</option>
        <?php while ($category = $categories->fetch_assoc()) { ?>
            <option value="<?php echo $category['category_id']; ?>"><?php echo $category['category_name']; ?></option>
        <?php } ?>
    </select>

    <label>Subcategory:</label>
    <select id="subcategoryFilter" onchange="filterQuestions()">
        <option value="">All Subcategories</option>
        <?php while ($subcategory = $subcategories->fetch_assoc()) { ?>
            <option value="<?php echo $subcategory['subcategory_id']; ?>" data-category="<?php echo $subcategory['category_id']; ?>">
                <?php echo $subcategory['subcategory_name']; ?>
            </option>
        <?php } ?>
    </select>

    <div class="box-container">
        <div class="left-box">
            <h3>Available Questions</h3>
            <select id="leftBox" multiple size="10">
                <!-- Dynamically populated questions will appear here -->
            </select>
        </div>

        <div class="buttons">
            <button id="moveToRightBtn">Move to Right &gt;&gt;</button>
            <button id="moveToLeftBtn">&lt;&lt; Move to Left</button>
        </div>

        <div class="right-box">
            <h3>Selected Questions</h3>
            <select id="rightBox" multiple size="10"></select>
        </div>
    </div>
</div>

<script>
// Function to fetch filtered questions using AJAX
function filterQuestions() {
    var category_id = document.getElementById("categoryFilter").value;
    var subcategory_id = document.getElementById("subcategoryFilter").value;

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "fetch_questions.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            document.getElementById('leftBox').innerHTML = xhr.responseText;
        }
    };
    xhr.send("category_id=" + category_id + "&subcategory_id=" + subcategory_id);
}

// Move questions between boxes
function moveToRightBox() {
    let leftBox = document.getElementById('leftBox');
    let rightBox = document.getElementById('rightBox');
    let selectedOptions = Array.from(leftBox.selectedOptions);

    selectedOptions.forEach(option => {
        rightBox.appendChild(option);
    });
}

function moveToLeftBox() {
    let rightBox = document.getElementById('rightBox');
    let leftBox = document.getElementById('leftBox');
    let selectedOptions = Array.from(rightBox.selectedOptions);

    selectedOptions.forEach(option => {
        leftBox.appendChild(option);
    });
}

document.getElementById('moveToRightBtn').addEventListener('click', moveToRightBox);
document.getElementById('moveToLeftBtn').addEventListener('click', moveToLeftBox);
</script>

<style>
.box-container {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}

.left-box, .right-box {
    width: 40%;
}

.buttons {
    display: flex;
    flex-direction: column;
    justify-content: center;
    margin: 0 10px;
}

button {
    margin: 5px 0;
}
</style>

</body>
</html>
