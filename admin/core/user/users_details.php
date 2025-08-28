<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

include 'db_connection.php';

// Verify the connection to the database
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch users from the database
$usersQuery = "SELECT user_id, name, email, role, status, created_at, updated_at, phone_number FROM sanixazs_main_db.users";
$usersResult = $conn->query($usersQuery);

?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard</title>
    <link  rel="stylesheet"  href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css"  />
    <script src="https://kit.fontawesome.com/ae360af17e.js"  crossorigin="anonymous" ></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	  <link rel="stylesheet" href="css/admin_styleone.css" />
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
            <!-- Table Element -->
            <div class="card border-0">
              <div class="card-header">
                <h5 class="card-title">User List</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th>User ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Phone Number</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                </tr>
                                <?php
                                if ($usersResult) {
                                    if ($usersResult->num_rows > 0) {
                                        while ($user = $usersResult->fetch_assoc()) {
                                            echo "<tr>
                                                    <td>" . htmlspecialchars($user['user_id']) . "</td>
                                                    <td>" . htmlspecialchars($user['name']) . "</td>
                                                    <td>" . htmlspecialchars($user['email']) . "</td>
                                                    <td>" . htmlspecialchars($user['role']) . "</td>
                                                    <td>" . htmlspecialchars($user['status']) . "</td>
                                                    <td>" . htmlspecialchars($user['phone_number']) . "</td>
                                                    <td>" . htmlspecialchars($user['created_at']) . "</td>
                                                    <td>" . htmlspecialchars($user['updated_at']) . "</td>
                                                </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='9'>No users found.</td></tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='9'>Error executing query: " . $conn->error . "</td></tr>";
                                }
                                ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>  
<?php include 'admin_footer.php'; ?>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script>
    $("#menu-toggle").click(function (e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });

    function initMenu() {
        $('#menu ul').hide();
        $('#menu li a').click(function () {
            var checkElement = $(this).next();
            if ((checkElement.is('ul')) && (checkElement.is(':visible'))) {
                return false;
            }
            if ((checkElement.is('ul')) && (!checkElement.is(':visible'))) {
                $('#menu ul:visible').toggle('normal');
                checkElement.slideDown('normal');
                return false;
            }
        });
    }
    $(document).ready(function () {
        initMenu();
    });
</script>
</body>
</html>
