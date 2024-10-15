<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ensure the session is only started once
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Include database connection
include 'db_connection.php';

// Add the new column to the questions table if it doesn't already exist
$sql_check_column = "SHOW COLUMNS FROM sanixtec_main_db.questions LIKE 'display_on_dashboard'";
$result_check_column = $conn->query($sql_check_column);

if ($result_check_column->num_rows == 0) {
    $sql_add_column = "ALTER TABLE sanixtec_main_db.questions ADD COLUMN display_on_dashboard TINYINT(1) DEFAULT 0";
    if ($conn->query($sql_add_column) === TRUE) {
        echo "Column 'display_on_dashboard' added successfully!";
    } else {
        echo "Error adding column: " . $conn->error;
    }
}

// Fetch categories and subcategories for the dropdown filters
$categories = $conn->query("SELECT id, category_name FROM sanixtec_main_db.categories");
$subcategories = $conn->query("SELECT id, subcategory_name FROM sanixtec_main_db.subcategories");

// Fetch questions along with category and subcategory from the database
$sql = "SELECT q.id, q.question_text, c.category_name, s.subcategory_name, q.display_on_dashboard
        FROM sanixtec_main_db.questions q
        LEFT JOIN sanixtec_main_db.categories c ON q.category_id = c.id
        LEFT JOIN sanixtec_main_db.subcategories s ON q.subcategory_id = s.id";
$result = $conn->query($sql);

if (!$result) {
    echo "<p>Error: " . $conn->error . "</p>";
} else {
    // Start creating the table with filter dropdowns
    echo "<h2>Quiz Management</h2>";
    echo "<label>Category:</label>
          <select id='categoryFilter'>
              <option value=''>All Categories</option>";
    while ($cat = $categories->fetch_assoc()) {
        echo "<option value='" . $cat['category_name'] . "'>" . $cat['category_name'] . "</option>";
    }
    echo "</select>";

    echo "<label>Subcategory:</label>
          <select id='subcategoryFilter'>
              <option value=''>All Subcategories</option>";
    while ($subcat = $subcategories->fetch_assoc()) {
        echo "<option value='" . $subcat['subcategory_name'] . "'>" . $subcat['subcategory_name'] . "</option>";
    }
    echo "</select>";

    if ($result->num_rows > 0) {
        echo "<table border='1' cellpadding='10' cellspacing='0' id='questionTable'>
                <thead>
                    <tr>
                        <th>Question No</th>
                        <th>Category</th>
                        <th>Subcategory</th>
                        <th>Question</th>
                        <th>Edit</th>
                        <th>Display on User Dashboard</th>
                    </tr>
                </thead>
                <tbody>";
        $question_no = 1;
        while ($row = $result->fetch_assoc()) {
            $display_checked = $row['display_on_dashboard'] ? 'checked' : '';
            echo "<tr>";
            echo "<td>" . $question_no++ . "</td>";  // Display question number
            echo "<td>" . htmlspecialchars($row['category_name']) . "</td>";  // Display category
            echo "<td>" . htmlspecialchars($row['subcategory_name']) . "</td>";  // Display subcategory
            echo "<td>" . htmlspecialchars($row['question_text']) . "</td>";  // Display question text
            echo "<td><a href='admin_dashboard.php?menu=edit_question&id=" . $row['id'] . "'>Edit</a></td>";  // Edit link
            echo "<td><label class='switch'>
                        <input type='checkbox' class='toggle-display' data-id='" . $row['id'] . "' " . $display_checked . ">
                        <span class='slider'></span>
                  </label></td>";  // Toggle switch
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>No questions found.</p>";
    }
}

$conn->close();
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter the table when a category or subcategory is selected
    const categoryFilter = document.getElementById('categoryFilter');
    const subcategoryFilter = document.getElementById('subcategoryFilter');
    const tableRows = document.querySelectorAll('#questionTable tbody tr');

    function filterTable() {
        const categoryValue = categoryFilter.value.toLowerCase();
        const subcategoryValue = subcategoryFilter.value.toLowerCase();

        tableRows.forEach(row => {
            const categoryText = row.cells[1].textContent.toLowerCase();
            const subcategoryText = row.cells[2].textContent.toLowerCase();

            if ((categoryValue === '' || categoryText.includes(categoryValue)) &&
                (subcategoryValue === '' || subcategoryText.includes(subcategoryValue))) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    categoryFilter.addEventListener('change', filterTable);
    subcategoryFilter.addEventListener('change', filterTable);

    // Handle toggle switch for displaying questions on the user dashboard
    const toggles = document.querySelectorAll('.toggle-display');

    toggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const id = this.getAttribute('data-id');
            const display = this.checked ? 1 : 0;

            fetch('update_display_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${id}&display=${display}`
            }).then(response => response.text())
              .then(data => {
                  console.log(data); // For debugging
              }).catch(error => {
                  console.error('Error:', error);
              });
        });
    });
});
</script>

<style>
/* Style for the toggle switch */
.switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: #2196F3;
}

input:checked + .slider:before {
    transform: translateX(26px);
}
</style>
