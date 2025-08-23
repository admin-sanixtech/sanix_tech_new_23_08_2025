<?php
// ---------- bootstrap / auth ----------
session_start();
require_once '../db_connection.php';      // <-- path may vary

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die('Access denied. Admins only.');
}

// ---------- add‑subcategory form handler ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_subcategory'])) {
    $category_id      = $_POST['category_id'] ?? '';
    $subcategory_name = trim($_POST['subcategory_name'] ?? '');

    if ($category_id && $subcategory_name) {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO subcategories (category_id, subcategory_name)
                 VALUES (:cid, :sname)"
            );
            $stmt->execute([
                ':cid'   => $category_id,
                ':sname' => $subcategory_name
            ]);
            $success = 'Sub‑category added!';
        } catch (PDOException $e) {
            $error = 'DB error: ' . $e->getMessage();
        }
    } else {
        $error = 'Please fill in all fields.';
    }
}

// ---------- fetch categories for <select> ----------
$categories = $pdo->query("SELECT category_id, category_name FROM categories ORDER BY category_name")
                  ->fetchAll(PDO::FETCH_ASSOC);

// ---------- fetch ALL sub‑categories for initial table ----------
$subStmt = $pdo->query(
    "SELECT sc.subcategory_name, c.category_name
       FROM subcategories sc
  JOIN categories c ON sc.category_id = c.category_id
   ORDER BY c.category_name, sc.subcategory_name"
);
$subrows = $subStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Manage Sub‑categories</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/admin_styleone.css" />
</head>
<body class="d-flex">

<aside id="sidebar"><?php include 'admin_menu.php'; ?></aside>

<div class="flex-grow-1">
    <?php include 'admin_navbar.php'; ?>

    <main class="container my-4">
        <h2 class="mb-3">Add Sub‑category</h2>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php elseif (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
         
        <form method="POST" class="row g-3 mb-5">
            <div class="col-md-4">  
                <!-- Category Filter Dropdown -->
                <label class="col-sm-2 col-form-label"> Category</label>
                
<select id="filter_category_id" name="category_id" class="form-select" required>
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
               
                        
        <!--   <label class="form-label">Category</label>
                <select name="category_id" class="form-select" required>
                    <option value="">Select…</option>
                   
                </select>  -->
            </div>

            <div class="col-md-4">
                <label class="form-label">Sub‑category name</label>
                <input type="text" name="subcategory_name" class="form-control" required>
            </div>

            <div class="col-md-2 align-self-end">
                <button type="submit" name="add_subcategory" class="btn btn-primary w-100">Add</button>
            </div>
        </form>
            
        <h3 class="mb-3">Existing Sub‑categories</h3>
        <table id="sub_table" class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Category</th>
                    <th>Sub‑category</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($subrows): $i = 1;
                foreach ($subrows as $r): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($r['category_name']) ?></td>
                        <td><?= htmlspecialchars($r['subcategory_name']) ?></td>
                    </tr>
            <?php endforeach; else: ?>
                    <tr><td colspan="3" class="text-center">None found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </main>

    <?php include 'admin_footer.php'; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- AJAX refresh when category dropdown changes -->
<script>
$('#filter_category_id').on('change', function () {
    const cid = this.value;
    $.post('fetch_subcategories.php', { category_id: cid }, function (html) {
        $('#sub_table tbody').html(html);
    });
});
</script>

</body>
</html>
