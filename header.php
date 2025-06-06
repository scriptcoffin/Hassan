<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}
$username = $_SESSION['username'];
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>GIKONKO TSS - Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
<style>
  body {
    min-height: 100vh;
    display: flex;
  }
  #sidebar {
    width: 250px;
    height: 100vh;
    position: fixed;
    background-color: #343a40;
  }
  #sidebar .nav-link {
    color: white;
  }
  #sidebar .nav-link.active {
    background-color: #0d6efd;
  }
  #content {
    margin-left: 250px;
    padding: 20px;
    width: 100%;
  }
</style>
</head>
<body>
<div id="sidebar" class="d-flex flex-column p-3">
    <h4 class="text-white mb-4">GIKONKO TSS</h4>
    <p class="text-white mb-4">Hello, <?=htmlspecialchars($username)?> (<?=htmlspecialchars($role)?>)</p>
    <ul class="nav nav-pills flex-column mb-auto">
      <li><a href="dashboard.php" class="nav-link <?=basename($_SERVER['PHP_SELF'])=='dashboard.php' ? 'active':''?>"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
      <li><a href="trainees.php" class="nav-link <?=basename($_SERVER['PHP_SELF'])=='trainees.php' ? 'active':''?>"><i class="bi bi-people"></i> Trainees</a></li>
      <li><a href="trades.php" class="nav-link <?=basename($_SERVER['PHP_SELF'])=='trades.php' ? 'active':''?>"><i class="bi bi-tools"></i> Trades</a></li>
      <li><a href="modules.php" class="nav-link <?=basename($_SERVER['PHP_SELF'])=='modules.php' ? 'active':''?>"><i class="bi bi-book"></i> Modules</a></li>
      
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle <?=in_array(basename($_SERVER['PHP_SELF']), ['marks.php']) ? 'active' : ''?>" href="#" data-bs-toggle="dropdown"><i class="bi bi-clipboard-data"></i> Marks</a>
        <ul class="dropdown-menu dropdown-menu-dark">
          <li><a class="dropdown-item" href="marks.php">All Marks</a></li>
     
        </ul>
      </li>

      <li><a href="report.php" class="nav-link <?=basename($_SERVER['PHP_SELF'])=='report.php' ? 'active':''?>">Reports</a></li>


      <?php if ($role == 'Admin'): ?>
      <li><a href="users.php" class="nav-link <?=basename($_SERVER['PHP_SELF'])=='users.php' ? 'active':''?>"><i class="bi bi-person-gear"></i> Users</a></li>
      <?php endif; ?>
    </ul>
    <hr>
    <a href="logout.php" class="btn btn-danger w-100"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<div id="content">
