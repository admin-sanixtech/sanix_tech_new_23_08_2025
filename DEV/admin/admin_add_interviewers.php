<?php
session_start();
require_once '../db_connection.php'; // Adjust the path if needed

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized access");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['full_name'];
    $skills = $_POST['skills'];
    $timeSlots = $_POST['time_slots'];

    $photoPath = null;
    if (!empty($_FILES['photo']['name'])) {
        $uploadDir = '../uploads/interviewers/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $fileName = time() . '_' . basename($_FILES['photo']['name']);
        $photoPath = $uploadDir . $fileName;
        move_uploaded_file($_FILES['photo']['tmp_name'], $photoPath);
    }

    $stmt = $pdo->prepare("INSERT INTO interviewers (full_name, photo, skills, time_slots) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $photoPath, $skills, $timeSlots]);

    header("Location: admin_add_interviewers.php?added=1");
    exit;
}

// Fetch existing interviewers
$interviewers = $pdo->query("SELECT * FROM interviewers ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'admin_header.php'; ?>
<?php include 'admin_menu.php'; ?>

<div class="container mt-4">
    <h2>Manage Interviewers</h2>

    <?php if (isset($_GET['added'])): ?>
        <div class="alert alert-success">Interviewer added successfully.</div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="mb-5">
        <div class="mb-3">
            <label>Full Name</label>
            <input type="text" name="full_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Photo</label>
            <input type="file" name="photo" class="form-control">
        </div>
        <div class="mb-3">
            <label>Skills</label>
            <textarea name="skills" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label>Time Slots</label>
            <input type="text" name="time_slots" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Interviewer</button>
    </form>

    <h4>All Interviewers</h4>
    <div class="row">
        <?php foreach ($interviewers as $iv): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <?php if ($iv['photo']): ?>
                        <img src="<?= $iv['photo'] ?>" class="card-img-top" alt="photo">
                    <?php else: ?>
                        <img src="../images/default_avatar.png" class="card-img-top" alt="default photo">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5><?= htmlspecialchars($iv['full_name']) ?></h5>
                        <p><strong>Skills:</strong> <?= nl2br(htmlspecialchars($iv['skills'])) ?></p>
                        <p><strong>Time Slots:</strong> <?= htmlspecialchars($iv['time_slots']) ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'admin_footer.php'; ?>
