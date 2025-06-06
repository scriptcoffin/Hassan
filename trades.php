<?php
include 'db.php';
include 'header.php';

// Only allow Admin, DOS, Teacher
if (!in_array($role, ['Admin','DOS','Teacher'])) {
    die("Access denied.");
}

// Add trade
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_trade'])) {
    $trade_name = trim($_POST['trade_name']);
    if ($trade_name !== '') {
        $stmt = $conn->prepare("INSERT INTO Trades (Trade_Name) VALUES (?)");
        $stmt->bind_param("s", $trade_name);
        $stmt->execute();
        $stmt->close();
        header("Location: trades.php");
        exit;
    }
}

// Delete trade
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM Trades WHERE Trade_Id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: trades.php");
    exit;
}

// Edit trade
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_trade'])) {
    $id = intval($_POST['trade_id']);
    $trade_name = trim($_POST['trade_name']);
    if ($trade_name !== '') {
        $stmt = $conn->prepare("UPDATE Trades SET Trade_Name = ? WHERE Trade_Id = ?");
        $stmt->bind_param("si", $trade_name, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: trades.php");
        exit;
    }
}

$result = $conn->query("SELECT * FROM Trades ORDER BY Trade_Name ASC");
?>

<h2>Trades</h2>

<form method="post" class="mb-3">
    <div class="input-group">
        <input type="text" name="trade_name" class="form-control" placeholder="New Trade Name" required>
        <button type="submit" name="add_trade" class="btn btn-primary">Add Trade</button>
    </div>
</form>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Trade ID</th>
            <th>Trade Name</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['Trade_Id'] ?></td>
            <td>
                <form method="post" class="d-flex">
                    <input type="hidden" name="trade_id" value="<?= $row['Trade_Id'] ?>" />
                    <input type="text" name="trade_name" value="<?= htmlspecialchars($row['Trade_Name']) ?>" class="form-control me-2" required />
                    <button type="submit" name="edit_trade" class="btn btn-sm btn-success">Update</button>
                </form>
            </td>
            <td>
                <a href="trades.php?delete=<?= $row['Trade_Id'] ?>" onclick="return confirm('Delete this trade?')" class="btn btn-sm btn-danger">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>
