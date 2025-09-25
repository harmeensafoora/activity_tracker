<?php
session_start();
include('includes/db_connect.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch the subjects assigned to the current admin
$sql_subjects = "SELECT subject_id FROM admin_subjects WHERE admin_id = :admin_id";
$stmt_subjects = $dbh->prepare($sql_subjects);
$stmt_subjects->execute([':admin_id' => $_SESSION['admin_id']]);
$admin_subjects = $stmt_subjects->fetchAll(PDO::FETCH_COLUMN, 0);

// Fetch seminars based on the subjects the admin teaches
if (!empty($admin_subjects)) {
    // We use placeholders to prevent SQL injection when using IN clause
    $placeholders = implode(',', array_fill(0, count($admin_subjects), '?'));
    $sql = "SELECT s.*, d.name AS department_name, sem.name AS semester_name, subj.name AS subject_name
            FROM seminars s
            LEFT JOIN departments d ON s.dept_id = d.id
            LEFT JOIN semesters sem ON s.semester_id = sem.id
            LEFT JOIN subjects subj ON s.subject_id = subj.id
            WHERE s.subject_id IN ($placeholders) ORDER BY s.submitted_at DESC";

    $query = $dbh->prepare($sql);
    $query->execute($admin_subjects);
    $seminars = $query->fetchAll(PDO::FETCH_ASSOC);
} else {
    // If the admin has no assigned subjects, show an empty list
    $seminars = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    textarea { width: 100%; height: 50px; }
    .action-form { display: flex; flex-direction: column; gap: 5px; }
    .given { color: green; font-weight: bold; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Welcome Admin</h2>
    <a href="logout.php" class="btn">Logout</a>

    <h3>Submitted Seminars</h3>
    <?php if (empty($admin_subjects)): ?>
        <p>You are not assigned to any subjects. Please contact the system administrator.</p>
    <?php endif; ?>

    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Department</th>
          <th>Semester</th>
          <th>Subject</th>
          <th>Topic</th>
          <th>Status</th>
          <th>Seminar Given</th>
          <th>Submitted At</th>
          <th>Remarks</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($seminars as $row): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['student_name']); ?></td>
            <td><?php echo htmlspecialchars($row['department_name']); ?></td>
            <td><?php echo htmlspecialchars($row['semester_name']); ?></td>
            <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
            <td><?php echo htmlspecialchars($row['topic']); ?></td>
            <td>
              <?php if ($row['status'] === 'Approved'): ?>
                ✅ Approved
              <?php elseif ($row['status'] === 'Rejected'): ?>
                ❌ Rejected
              <?php else: ?>
                ⏳ Pending
              <?php endif; ?>
            </td>
            <td class="given"><?php echo htmlspecialchars($row['seminar_given'] ?? 'N/A'); ?></td>
            <td><?php echo htmlspecialchars($row['submitted_at']); ?></td>
            <td><?php echo htmlspecialchars($row['remarks'] ?? 'N/A'); ?></td>
            <td>
              <?php if ($row['status'] === 'Pending'): ?>
                <form method="post" action="update_seminar.php" class="action-form">
                  <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                  <textarea name="remarks" placeholder="Enter remarks (optional)"></textarea>
                  <button type="submit" name="action" value="approve" class="btn approve small">Approve ✅</button>
                  <button type="submit" name="action" value="reject" class="btn red small">Reject ❌</button>
                </form>
              <?php else: ?>
                ✔ <?php echo $row['status']; ?>
                <br>
                <?php if ($row['seminar_given'] === 'No'): ?>
                  <form method="post" action="update_seminar.php" class="action-form">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="action" value="mark_given" class="btn approve small">Mark as Given</button>
                  </form>
                <?php else: ?>
                    ✔ Given
                <?php endif; ?>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>
</html>