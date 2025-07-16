<?php
session_start();

// --- Role check helpers -----------------------------------------------------
function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function is_interviewer() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'interviewer';
}

// --- DB connection ----------------------------------------------------------
require_once 'db_connection.php';
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// --- Handle form submission -------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (is_admin() || is_interviewer())) {
    $name      = $_POST['full_name']  ?? '';
    $skills    = $_POST['skills']     ?? '';
    $timeSlots = $_POST['time_slots'] ?? '';
    $photoPath = null;

    if (!empty($_FILES['photo']['name'])) {
        $targetDir = 'uploads/interviewers/';
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true); // Create folder if not exists
        }

        $fileName = time() . '_' . basename($_FILES['photo']['name']);
        $photoPath = $targetDir . $fileName;
        move_uploaded_file($_FILES['photo']['tmp_name'], $photoPath);
    }

    $stmt = $pdo->prepare("
        INSERT INTO interviewers (full_name, photo, skills, time_slots)
        VALUES (:n, :p, :s, :t)
    ");
    $stmt->execute([
        ':n' => $name,
        ':p' => $photoPath,
        ':s' => $skills,
        ':t' => $timeSlots,
    ]);

    header('Location: mock_interviews.php?added=1');
    exit;
}

// --- Fetch interviewers -----------------------------------------------------
$interviewers = $pdo->query("SELECT * FROM interviewers ORDER BY created_at DESC")
                    ->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mock Interviews - Sanix Technologies</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS Links -->
    <link rel="stylesheet" href="css/user_styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>

<div class="container my-5">
    <h2 class="text-center mb-4">Mock Interviewers</h2>

    <?php if (isset($_GET['added'])): ?>
        <div class="alert alert-success text-center">Interviewer added successfully!</div>
    <?php endif; ?>

    <!-- Interviewer cards -->
    <div class="row g-4">
        <?php foreach ($interviewers as $iv): ?>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <?php if (!empty($iv['photo']) && file_exists($iv['photo'])): ?>
                        <img src="<?= $iv['photo']; ?>" class="card-img-top" alt="<?= htmlspecialchars($iv['full_name']); ?>">
                    <?php else: ?>
                        <img src="images/default_avatar.png" class="card-img-top" alt="avatar">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($iv['full_name']); ?></h5>
                        <p class="card-text"><strong>Skills:</strong> <?= nl2br(htmlspecialchars($iv['skills'])); ?></p>
                        <p class="card-text"><strong>Available:</strong> <?= htmlspecialchars($iv['time_slots']); ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (is_admin() || is_interviewer()): ?>
        <div class="text-end mt-4">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fa fa-plus"></i> Add Interviewer
            </button>
        </div>
    <?php endif; ?>
</div>

<!-- Modal: Add Interviewer -->
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" enctype="multipart/form-data">
            <div class="modal-header">
                <h5 class="modal-title">Add Interviewer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Photo</label>
                    <input type="file" name="photo" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Skills</label>
                    <textarea name="skills" rows="3" class="form-control" placeholder="e.g. Spark, SQL, AWS" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Available Time Slots</label>
                    <input type="text" name="time_slots" class="form-control" placeholder="Mon–Fri 5pm–8pm" required>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" type="submit">Save</button>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
