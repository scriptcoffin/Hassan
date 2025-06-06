<?php
include 'db.php';
include 'header.php';

// Only Admin, DOS, Teacher
if (!in_array($role, ['Admin','DOS','Teacher'])) {
    die("Access denied.");
}

$trades_result = $conn->query("SELECT * FROM Trades ORDER BY Trade_Name ASC");

// Add module
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_module'])) {
    $module_name = trim($_POST['module_name']);
    $trade_id = intval($_POST['trade_id']);

    if ($module_name !== '' && $trade_id > 0) {
        $stmt = $conn->prepare("INSERT INTO Modules (Module_Name, Trade_Id) VALUES (?, ?)");
        $stmt->bind_param("si", $module_name, $trade_id);
        $stmt->execute();
        $stmt->close();
        header("Location: modules.php");
        exit;
    }
}

// Delete module
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM Modules WHERE Module_Id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: modules.php");
    exit;
}

// Edit module
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_module'])) {
    $id = intval($_POST['module_id']);
    $module_name = trim($_POST['module_name']);
    $trade_id = intval($_POST['trade_id']);

    if ($module_name !== '' && $trade_id > 0) {
        $stmt = $conn->prepare("UPDATE Modules SET Module_Name = ?, Trade_Id = ? WHERE Module_Id = ?");
        $stmt->bind_param("sii", $module_name, $trade_id, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: modules.php");
        exit;
    }
}

$result = $conn->query("SELECT m.Module_Id, m.Module_Name, m.Trade_Id, t.Trade_Name FROM Modules m LEFT JOIN Trades t ON m.Trade_Id = t.Trade_Id ORDER BY m.Module_Name ASC");
?>

<h2>Modules</h2>

<h5>Add New Module</h5>
<form method="post" class="row g-3 mb-4">
    <div class="col-md-6">
        <input type="text" name="module_name" class="form-control" placeholder="Module Name" required />
    </div>
    <div class="col-md-4">
        <select name="trade_id" class="form-select" required>
            <option value="">Select Trade</option>
            <?php while ($trade = $trades_result->fetch_assoc()): ?>
                <option value="<?= $trade['Trade_Id'] ?>"><?= htmlspecialchars($trade['Trade_Name']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="col-md-2">
        <button type="submit" name="add_module" class="btn btn-primary w-100">Add Module</button>
    </div>
</form>

<table class="table table-bordered align-middle">
    <thead>
        <tr>
            <th>Module ID</th>
            <th>Module Name</th>
            <th>Trade</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php
    // Reset trades result for dropdown usage
    $trades_result = $conn->query("SELECT * FROM Trades ORDER BY Trade_Name ASC");

    while ($row = $result->fetch_assoc()):
    ?>
        <tr>
            <form method="post">
                <td><?= $row['Module_Id'] ?></td>
                <td>
                    <input type="hidden" name="module_id" value="<?= $row['Module_Id'] ?>" />
                    <input type="text" name="module_name" value="<?= htmlspecialchars($row['Module_Name']) ?>" class="form-control" required />
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
                    <button type="submit" name="edit_module" class="btn btn-success btn-sm mb-1">Update</button>
                    <a href="modules.php?delete=<?= $row['Module_Id'] ?>" onclick="return confirm('Delete this module?')" class="btn btn-danger btn-sm">Delete</a>
                </td>
            </form>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>
