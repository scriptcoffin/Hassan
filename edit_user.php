<?php
include 'db.php';
include 'header.php';

if ($role !== 'Admin') {
    die("Access denied.");
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header("Location: users.php");
    exit();
}

// Fetch user info
$res = $conn->query("SELECT * FROM Users WHERE User_Id = $id");
if ($res->num_rows === 0) {
    die("User not found.");
}
$user = $res->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $roleInput = $conn->real_escape_string($_POST['role']);
    
    $updatePassword = false;
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $updatePassword = true;
    }

    $sql = "UPDATE Users SET Username = '$username', Role = '$roleInput'";
    if ($updatePassword) {
        $sql .= ", Password = '$password'";
    }
    $sql .= " WHERE User_Id = $id";

    $conn->query($sql);

    header("Location: users.php");
    exit();
}
?>

<h2>Edit User</h2>

<form method="POST" action="edit_user.php?id=<?= $id ?>">
    <div class="mb-3">
        <label>Username</label>
        <input type="text" name="username" value="<?= htmlspecialchars($user['Username']) ?>" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Password (leave blank to keep current)</label>
        <input type="password" name="password" class="form-control">
    </div>
    <div class="mb-3">
        <label>Role</label>
        <select name="role" class="form-select" required>
            <option value="Admin" <?= $user['Role'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
            <option value="DOS" <?= $user['Role'] === 'DOS' ? 'selected' : '' ?>>DOS</option>
            <option value="Teacher" <?= $user['Role'] === 'Teacher' ? 'selected' : '' ?>>Teacher</option>
        </select>
    </div>
    <button type="submit" class="btn btn-success">Update User</button>
    <a href="users.php" class="btn btn-secondary">Cancel</a>
</form>

<?php include 'footer.php'; ?>
