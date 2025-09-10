<?php
include('includes/db_connect.php');

// Get filters from the URL, using a fallback to prevent warnings
$department_name_param = $_GET['department'] ?? '';
$semester_id_param = $_GET['semester'] ?? '';
$subject_id_param = $_GET['subject'] ?? '';
$topic_param = $_GET['topic'] ?? '';

// Start the SQL query with JOINS to get department, semester, and subject names
$sql = "SELECT s.*, d.name AS department_name, sem.name AS semester_name, subj.name AS subject_name
        FROM seminars s
        LEFT JOIN departments d ON s.dept_id = d.id
        LEFT JOIN semesters sem ON s.semester_id = sem.id
        LEFT JOIN subjects subj ON s.subject_id = subj.id
        WHERE 1=1";

$params = [];

// Filter by Department Name
if (!empty($department_name_param)) {
    $sql .= " AND d.name = :department_name";
    $params[':department_name'] = $department_name_param;
}

// Filter by Semester ID
if (!empty($semester_id_param)) {
    $sql .= " AND sem.id = :semester_id";
    $params[':semester_id'] = $semester_id_param;
}

// Filter by Subject ID
if (!empty($subject_id_param)) {
    $sql .= " AND s.subject_id = :subject_id";
    $params[':subject_id'] = $subject_id_param;
}

// Filter by Topic (partial search)
if (!empty($topic_param)) {
    $sql .= " AND s.topic LIKE :topic";
    $params[':topic'] = "%" . $topic_param . "%";
}

$query = $dbh->prepare($sql);
$query->execute($params);
$seminars = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Seminars</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    .given {
      color: green;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <header>
    <h1>📑 Seminar Submissions</h1>
    <nav>
      <a href="index.php" class="btn">⬅ Back</a>
    </nav>
  </header>

  <main>
    <?php if (count($seminars) > 0): ?>
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Department</th>
            <th>Semester</th>
            <th>Subject</th>
            <th>Topic</th>
            <th>Status</th>
            <th>Remarks</th>
            <th>Seminar Given</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($seminars as $row): ?>
            <tr>
              <td><?= htmlspecialchars($row['student_name']); ?></td>
              <td><?= htmlspecialchars($row['department_name']); ?></td>
              <td><?= htmlspecialchars($row['semester_name']); ?></td>
              <td><?= htmlspecialchars($row['subject_name']); ?></td>
              <td><?= htmlspecialchars($row['topic']); ?></td>
              <td>
                <?php if ($row['status'] == 'Approved'): ?>
                  ✅ Approved
                <?php elseif ($row['status'] == 'Rejected'): ?>
                  ❌ Rejected
                <?php else: ?>
                  ⏳ Pending
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($row['remarks']); ?></td>
              <td><?= htmlspecialchars($row['seminar_given']); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>No seminars found for your search criteria.</p>
    <?php endif; ?>
  </main>
</body>
</html>