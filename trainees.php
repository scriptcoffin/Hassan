<?php
include 'db.php';
include 'header.php';

// Only allow Admin, DOS, Teacher
if (!in_array($role, ['Admin','DOS','Teacher'])) {
    die("Access denied.");
}

// Fetch trades for dropdown
$trades_result = $conn->query("SELECT * FROM Trades ORDER BY Trade_Name ASC");

// Add trainee
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_trainee'])) {
    $firstnames = trim($_POST['firstnames']);
    $lastname = trim($_POST['lastname']);
    $gender = $_POST['gender'];
    $trade_id = intval($_POST['trade_id']);

    if ($firstnames !== '' && $lastname !== '' && in_array($gender, ['Male', 'Female']) && $trade_id > 0) {
        $stmt = $conn->prepare("INSERT INTO Trainees (FirstNames, LastName, Gender, Trade_Id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $firstnames, $lastname, $gender, $trade_id);
        $stmt->execute();
        $stmt->close();
        header("Location: trainees.php");
        exit;
    }
}

// Delete trainee
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM Trainees WHERE Trainee_Id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: trainees.php");
    exit;
}

// Edit trainee
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_trainee'])) {
    $id = intval($_POST['trainee_id']);
    $firstnames = trim($_POST['firstnames']);
    $lastname = trim($_POST['lastname']);
    $gender = $_POST['gender'];
    $trade_id = intval($_POST['trade_id']);

    if ($firstnames !== '' && $lastname !== '' && in_array($gender, ['Male', 'Female']) && $trade_id > 0) {
        $stmt = $conn->prepare("UPDATE Trainees SET FirstNames = ?, LastName = ?, Gender = ?, Trade_Id = ? WHERE Trainee_Id = ?");
        $stmt->bind_param("sssii", $firstnames, $lastname, $gender, $trade_id, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: trainees.php");
        exit;
    }
}

// Fetch trainees with trades joined
$sql = "SELECT t.Trainee_Id, t.FirstNames, t.LastName, t.Gender, tr.Trade_Name, t.Trade_Id
        FROM Trainees t 
        LEFT JOIN Trades tr ON t.Trade_Id = tr.Trade_Id
        ORDER BY t.LastName ASC, t.FirstNames ASC";

$result = $conn->query($sql);
?>

<h2>Trainees</h2>

<h5>Add New Trainee</h5>
<form method="post" class="row g-3 mb-4">
    <div class="col-md-3">
        <input type="text" name="firstnames" class="form-control" placeholder="First Names" required />
    </div>
    <div class="col-md-3">
        <input type="text" name="lastname" class="form-control" placeholder="Last Name" required />
    </div>
    <div class="col-md-2">
        <select name="gender" class="form-select" required>
            <option value="">Gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>
    </div>
    <div class="col-md-3">
        <select name="trade_id" class="form-select" required>
            <option value="">Select Trade</option>
            <?php while ($trade = $trades_result->fetch_assoc()): ?>
                <option value="<?= $trade['Trade_Id'] ?>"><?= htmlspecialchars($trade['Trade_Name']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="col-md-1">
        <button type="submit" name="add_trainee" class="btn btn-primary w-100">Add</button>
    </div>
</form>

<table class="table table-bordered align-middle">
    <thead>
        <tr>
            <th>ID</th>
            <th>First Names</th>
            <th>Last Name</th>
            <th>Gender</th>
            <th>Trade</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php
    // Reset trades_result to reuse dropdown options later
    $trades_result = $conn->query("SELECT * FROM Trades ORDER BY Trade_Name ASC");

    while ($row = $result->fetch_assoc()):
    ?>
        <tr>
            <form method="post">
                <td><?= $row['Trainee_Id'] ?></td>
                <td>
                    <input type="hidden" name="trainee_id" value="<?= $row['Trainee_Id'] ?>" />
                    <input type="text" name="firstnames" value="<?= htmlspecialchars($row['FirstNames']) ?>" class="form-control" required />
                </td>
                <td><input type="text" name="lastname" value="<?= htmlspecialchars($row['LastName']) ?>" class="form-control" required /></td>
                <td>
                    <select name="gender" class="form-select" required>
                        <option value="Male" <?= $row['Gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= $row['Gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                    </select>
                </td>
                <td>
                    <select name="trade_id" class="form-select" required>
                        <?php
                        $trades_result->data_seek(0);
                        while ($trade = $trades_result->fetch_assoc()):
                        ?>
                            <option value="<?= $trade['Trade_Id'] ?>" <?= $trade['Trade_Id'] == $row['Trade_Id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($trade['Trade_Name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </td>
                <td>
                    <button type="submit" name="edit_trainee" class="btn btn-success btn-sm mb-1">Update</button>
                    <a href="trainees.php?delete=<?= $row['Trainee_Id'] ?>" onclick="return confirm('Delete this trainee?')" class="btn btn-danger btn-sm">Delete</a>
                </td>
            </form>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>
