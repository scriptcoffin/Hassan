<?php
include 'db.php';
include 'header.php';

// Only Admin can access Users management
if ($role !== 'Admin') {
    die("Access denied.");
}

// Handle delete user
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM Users WHERE User_Id = $id");
    header("Location: users.php");
    exit();
}

// Handle Add User form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $roleInput = $conn->real_escape_string($_POST['role']);
    
    $conn->query("INSERT INTO Users (Username, Password, Role) VALUES ('$username', '$password', '$roleInput')");
    header("Location: users.php");
    exit();
}

// Fetch all users
$result = $conn->query("SELECT User_Id, Username, Role FROM Users ORDER BY Username");

?>

<h2>Manage Users</h2>

<!-- Add User Form -->
<div class="card mb-4">
    <div class="card-header">Add New User</div>
    <div class="card-body">
        <form method="POST" action="users.php">
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Role</label>
                <select name="role" class="form-select" required>
                    <option value="Admin">Admin</option>
                    <option value="DOS">DOS</option>
                    <option value="Teacher">Teacher</option>
                </select>
            </div>
            <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
        </form>
    </div>
</div>

<!-- Users Table -->
<table class="table table-bordered align-middle">
    <thead>
        <tr>
            <th>Username</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while($user = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($user['Username']) ?></td>
            <td><?= htmlspecialchars($user['Role']) ?></td>
            <td>
                <a href="edit_user.php?id=<?= $user['User_Id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                <a href="users.php?delete=<?= $user['User_Id'] ?>" onclick="return confirm('Delete this user?')" class="btn btn-sm btn-danger">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>
