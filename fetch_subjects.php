<?php
// fetch_subjects.php
include('includes/db_connect.php');

$semester_id = $_GET['semester_id'] ?? '';
$dept_name = $_GET['dept_name'] ?? '';

// Get the department ID based on the name
$dept_id = null;
if (!empty($dept_name)) {
    $sql_dept = "SELECT id FROM departments WHERE name = :name";
    $stmt_dept = $dbh->prepare($sql_dept);
    $stmt_dept->execute([':name' => $dept_name]);
    $dept_id = $stmt_dept->fetchColumn();
}

$sql = "SELECT id, name FROM subjects WHERE 1=1";
$params = [];

if (!empty($semester_id)) {
    $sql .= " AND semester_id = :semester_id";
    $params[':semester_id'] = $semester_id;
}

if (!empty($dept_id)) {
    $sql .= " AND dept_id = :dept_id";
    $params[':dept_id'] = $dept_id;
}

$sql .= " ORDER BY name ASC";
$stmt = $dbh->prepare($sql);
$stmt->execute($params);
$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($subjects);
exit;
?>