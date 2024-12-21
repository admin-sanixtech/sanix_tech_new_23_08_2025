<?php
// Start with the session and database connection if not already included
session_start();


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: https://sanixtech.in");
    exit;
  }
  
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Subscription Plans</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" />
    <script src="https://kit.fontawesome.com/ae360af17e.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/user_styleone.css" />
</head>

<body>
    <div class="wrapper">
        <aside id="sidebar" class="js-sidebar">
            <?php include 'user_menu.php'; ?>
        </aside>

        <div class="main">
            <?php include 'user_navbar.php'; ?>
            <main class="content px-3 py-2">
                <div class="container-fluid">
                    <div class="mb-3">
                        <h4 class="mb-4">Subscription Plans</h4>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Plan</th>
                                        <th>Questions</th>
                                        <th>Categories</th>
                                        <th>Cost (INR)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Silver</td>
                                        <td>50</td>
                                        <td>1</td>
                                        <td>50</td>
                                    </tr>
                                    <tr>
                                        <td>Bronze</td>
                                        <td>100</td>
                                        <td>1</td>
                                        <td>100</td>
                                    </tr>
                                    <tr>
                                        <td>Gold</td>
                                        <td>200</td>
                                        <td>2</td>
                                        <td>200</td>
                                    </tr>
                                    <tr>
                                        <td>Diamond</td>
                                        <td>500</td>
                                        <td>5</td>
                                        <td>500</td>
                                    </tr>
                                    <tr>
                                        <td>Custom</td>
                                        <td>1000</td>
                                        <td>All Categories</td>
                                        <td>1000</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <h3 class="mt-4">Select a Plan</h3>
                        <form method="POST" action="process_subscription.php" class="mt-3">
                            <div class="mb-3">
                                <label for="subscription_plan" class="form-label">Choose a Plan:</label>
                                <select name="subscription_plan" id="subscription_plan" class="form-select" required>
                                    <option value="Silver">Silver - 50 INR</option>
                                    <option value="Bronze">Bronze - 100 INR</option>
                                    <option value="Gold">Gold - 200 INR</option>
                                    <option value="Diamond">Diamond - 500 INR</option>
                                    <option value="Custom">Custom - 1000 INR</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Subscribe</button>
                        </form>
                    </div>
                </div>
            </main>
            <?php include 'user_footer.php'; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>
