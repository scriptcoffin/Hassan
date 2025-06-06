<?php
include 'db.php';
include 'header.php';

// Restrict access
//session_start();
$role = $_SESSION['role'] ?? '';
if (!in_array($role, ['Admin', 'DOS', 'Teacher'])) {
    die("Access denied.");
}

// Fetch trainees and modules
$trainees = $conn->query("SELECT Trainee_Id, FirstNames, LastName FROM Trainees ORDER BY LastName, FirstNames");
$modules = $conn->query("SELECT Module_Id, Module_Name FROM Modules ORDER BY Module_Name");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mark_id = intval($_POST['mark_id'] ?? 0);
    $trainee_id = intval($_POST['trainee_id']);
    $module_id = intval($_POST['module_id']);
    $formative = floatval($_POST['formative']);
    $summative = floatval($_POST['summative']);
    $total = $formative + $summative;

    if ($mark_id > 0) {
        $stmt = $conn->prepare("UPDATE Marks SET Trainee_Id=?, Module_Id=?, Formative_Assessment=?, Summative_Assessment=?, Total_Marks=? WHERE Mark_Id=?");
        $stmt->bind_param("iidddi", $trainee_id, $module_id, $formative, $summative, $total, $mark_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO Marks (Trainee_Id, Module_Id, Formative_Assessment, Summative_Assessment, Total_Marks) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiidd", $trainee_id, $module_id, $formative, $summative, $total);
    }
    $stmt->execute();
    $stmt->close();
    header("Location: marks.php");
    exit;
}

// Handle deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM Marks WHERE Mark_Id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: marks.php");
    exit;
}

// Fetch all marks
$marks = $conn->query("
    SELECT m.Mark_Id, m.Formative_Assessment, m.Summative_Assessment, m.Total_Marks,
           t.Trainee_Id, t.FirstNames, t.LastName,
           mo.Module_Id, mo.Module_Name
    FROM Marks m
    JOIN Trainees t ON m.Trainee_Id = t.Trainee_Id
    JOIN Modules mo ON m.Module_Id = mo.Module_Id
    ORDER BY t.LastName, t.FirstNames, mo.Module_Name
");
?>

<div class="container mt-4">
    <h2>Marks Management</h2>

    <ul class="nav nav-tabs mb-4">
        <li class="nav-item"><a class="nav-link active" href="marks.php">All Marks</a></li>
        <li class="nav-item"><a class="nav-link" href="competent.php">Competent</a></li>
        <li class="nav-item"><a class="nav-link" href="nyc.php">Not Yet Competent</a></li>
    </ul>

    <h5>Add / Edit Mark</h5>
    <form method="post" class="row g-3 mb-4">
        <input type="hidden" name="mark_id" id="mark_id" />
        <div class="col-md-3">
            <select name="trainee_id" id="trainee_id" class="form-select" required>
                <option value="">Select Trainee</option>
                <?php while ($t = $trainees->fetch_assoc()): ?>
                    <option value="<?= $t['Trainee_Id'] ?>">
                        <?= htmlspecialchars($t['LastName'] . ', ' . $t['FirstNames']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="module_id" id="module_id" class="form-select" required>
                <option value="">Select Module</option>
                <?php while ($m = $modules->fetch_assoc()): ?>
                    <option value="<?= $m['Module_Id'] ?>">
                        <?= htmlspecialchars($m['Module_Name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-2">
            <input type="number" name="formative" id="formative" min="0" max="50" step="0.1" class="form-control" placeholder="Formative (/50)" required>
        </div>
        <div class="col-md-2">
            <input type="number" name="summative" id="summative" min="0" max="50" step="0.1" class="form-control" placeholder="Summative (/50)" required>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Save</button>
        </div>
    </form>

    <table class="table table-bordered align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Trainee</th>
                <th>Module</th>
                <th>Formative</th>
                <th>Summative</th>
                <th>Total</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $marks->fetch_assoc()): ?>
            <tr>
                <td><?= $row['Mark_Id'] ?></td>
                <td><?= htmlspecialchars($row['LastName'] . ', ' . $row['FirstNames']) ?></td>
                <td><?= htmlspecialchars($row['Module_Name']) ?></td>
                <td><?= $row['Formative_Assessment'] ?></td>
                <td><?= $row['Summative_Assessment'] ?></td>
                <td><?= $row['Total_Marks'] ?></td>
                <td>
                    <button class="btn btn-warning btn-sm btn-edit"
                        data-id="<?= $row['Mark_Id'] ?>"
                        data-trainee="<?= $row['Trainee_Id'] ?>"
                        data-module="<?= $row['Module_Id'] ?>"
                        data-formative="<?= $row['Formative_Assessment'] ?>"
                        data-summative="<?= $row['Summative_Assessment'] ?>"
                    >Edit</button>
                    <a href="marks.php?delete=<?= $row['Mark_Id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this mark?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('mark_id').value = btn.dataset.id;
        document.getElementById('trainee_id').value = btn.dataset.trainee;
        document.getElementById('module_id').value = btn.dataset.module;
        document.getElementById('formative').value = btn.dataset.formative;
        document.getElementById('summative').value = btn.dataset.summative;
    });
});
</script>

<?php include 'footer.php'; ?>
