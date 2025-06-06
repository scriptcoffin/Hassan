<?php
include 'db.php';
include 'header.php';

if (!in_array($role, ['Admin', 'DOS', 'Teacher', 'Student'])) {
    die("Access denied.");
}

// Fetch Competent and NYC
$competent_sql = "SELECT t.FirstNames, t.LastName, tr.Trade_Name, m.Total_Marks
                  FROM Marks m
                  JOIN Trainees t ON m.Trainee_Id = t.Trainee_Id
                  JOIN Modules mo ON m.Module_Id = mo.Module_Id
                  JOIN Trades tr ON t.Trade_Id = tr.Trade_Id
                  WHERE m.Total_Marks >= 70
                  ORDER BY t.LastName, t.FirstNames";

$nyc_sql = "SELECT t.FirstNames, t.LastName, tr.Trade_Name, m.Total_Marks
            FROM Marks m
            JOIN Trainees t ON m.Trainee_Id = t.Trainee_Id
            JOIN Modules mo ON m.Module_Id = mo.Module_Id
            JOIN Trades tr ON t.Trade_Id = tr.Trade_Id
            WHERE m.Total_Marks < 70
            ORDER BY t.LastName, t.FirstNames";

$competent = $conn->query($competent_sql);
$nyc = $conn->query($nyc_sql);
?>

<h2 class="mb-4">Performance Report</h2>

<div class="row">
    <div class="col-md-6">
        <h4>Competent Trainees (≥ 70%)</h4>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Trade</th>
                    <th>Total Marks</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $competent->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['LastName'] . ', ' . $row['FirstNames']) ?></td>
                        <td><?= htmlspecialchars($row['Trade_Name']) ?></td>
                        <td><?= $row['Total_Marks'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <div class="col-md-6">
        <h4>Not Yet Competent (NYC) Trainees (< 70%)</h4>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Trade</th>
                    <th>Total Marks</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $nyc->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['LastName'] . ', ' . $row['FirstNames']) ?></td>
                        <td><?= htmlspecialchars($row['Trade_Name']) ?></td>
                        <td><?= $row['Total_Marks'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>
