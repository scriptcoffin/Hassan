<?php
session_start();
require 'db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        // Login form
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        if ($username == '' || $password == '') {
            $errors[] = "Please fill in all login fields.";
        } else {
            $stmt = $conn->prepare("SELECT User_Id, Password, Role FROM Users WHERE Username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows === 1) {
                $stmt->bind_result($user_id, $hashed_password, $role);
                $stmt->fetch();
                if (password_verify($password, $hashed_password)) {
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['username'] = $username;
                    $_SESSION['role'] = $role;
                    header("Location: dashboard.php");
                    exit;
                } else {
                    $errors[] = "Invalid username or password.";
                }
            } else {
                $errors[] = "Invalid username or password.";
            }
            $stmt->close();
        }
    } elseif (isset($_POST['signup'])) {
        // Signup form
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $role = $_POST['role']; // Admin, DOS, Teacher

        if ($username == '' || $password == '' || $confirm_password == '') {
            $errors[] = "Please fill in all signup fields.";
        } elseif ($password !== $confirm_password) {
            $errors[] = "Passwords do not match.";
        } elseif (!in_array($role, ['Admin', 'DOS', 'Teacher'])) {
            $errors[] = "Invalid role selected.";
        } else {
            // Check if username exists
            $stmt = $conn->prepare("SELECT User_Id FROM Users WHERE Username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $errors[] = "Username already exists.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt->close();

                $stmt = $conn->prepare("INSERT INTO Users (Username, Password, Role) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $username, $hashed_password, $role);
                if ($stmt->execute()) {
                    $_SESSION['user_id'] = $stmt->insert_id;
                    $_SESSION['username'] = $username;
                    $_SESSION['role'] = $role;
                    header("Location: dashboard.php");
                    exit;
                } else {
                    $errors[] = "Signup failed. Please try again.";
                }
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Login / Signup - GIKONKO TSS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <?php if ($errors): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <ul class="nav nav-tabs mb-3" id="authTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab">Login</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="signup-tab" data-bs-toggle="tab" data-bs-target="#signup" type="button" role="tab">Signup</button>
                </li>
            </ul>

            <div class="tab-content" id="authTabContent">
                <!-- Login -->
                <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
                    <form method="POST" action="auth.php">
                        <div class="mb-3">
                            <label for="loginUsername" class="form-label">Username</label>
                            <input type="text" name="username" id="loginUsername" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <label for="loginPassword" class="form-label">Password</label>
                            <input type="password" name="password" id="loginPassword" class="form-control" required />
                        </div>
                        <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                    </form>
                </div>

                <!-- Signup -->
                <div class="tab-pane fade" id="signup" role="tabpanel" aria-labelledby="signup-tab">
                    <form method="POST" action="auth.php">
                        <div class="mb-3">
                            <label for="signupUsername" class="form-label">Username</label>
                            <input type="text" name="username" id="signupUsername" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <label for="signupPassword" class="form-label">Password</label>
                            <input type="password" name="password" id="signupPassword" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <label for="signupConfirmPassword" class="form-label">Confirm Password</label>
                            <input type="password" name="confirm_password" id="signupConfirmPassword" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select name="role" id="role" class="form-select" required>
                                <option value="">Select Role</option>
                                <option value="Admin">Admin</option>
                                <option value="DOS">DOS</option>
                                <option value="Teacher">Teacher</option>
                            </select>
                        </div>
                        <button type="submit" name="signup" class="btn btn-success w-100">Signup</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
