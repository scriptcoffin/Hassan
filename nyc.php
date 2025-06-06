<?php
include 'db.php';
include 'header.php';

if (!in_array($role, ['Admin','DOS','Teacher'])) {
    die("Access denied.");
}

// Fetch Not Yet Competent trainees (total marks < 70)
$sql = "SELECT t.Trainee_Id, t.FirstNames, t.LastName, tr.Trade_Name,
        m.Module_Name, mk.Formative_Assessment, mk.Summative_Assessment, mk.Total_Marks
        FROM Marks mk
        JOIN Trainees t ON mk.Trainee_Id = t.Trainee_Id
        JOIN Trades tr ON t.Trade_Id = tr.Trade_Id
        JOIN Modules m ON mk.Module_Id = m.Module_Id
        WHERE mk.Total_Marks < 70
        ORDER BY t.LastName, t.FirstNames, m.Module_Name";

$result = $conn->query($sql);
?>

<h2>Not Yet Competent Trainees (Total Marks < 70)</h2>

<a href="marks.php" class="btn btn-secondary mb-3">Back to All Marks</a>

<table class="table table-bordered align-middle">
    <thead>
        <tr>
            <th>Trainee Name</th>
            <th>Trade</th>
            <th>Module</th>
            <th>Formative</th>
            <th>Summative</th>
            <th>Total Marks</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['LastName'] . ', ' . $row['FirstNames']) ?></td>
            <td><?= htmlspecialchars($row['Trade_Name']) ?></td>
            <td><?= htmlspecialchars($row['Module_Name']) ?></td>
            <td><?= $row['Formative_Assessment'] ?></td>
            <td><?= $row['Summative_Assessment'] ?></td>
            <td><?= $row['Total_Marks'] ?></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>
