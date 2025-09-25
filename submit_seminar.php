<?php
session_start();
include('includes/db_connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $department_name = $_POST['department'];
    $semester_id = $_POST['semester'];
    $subject_id = $_POST['subject'];
    $topic = trim($_POST['topic']);

    // Get dept_id from the department name
    $sql_dept = "SELECT id FROM departments WHERE name = :name";
    $stmt_dept = $dbh->prepare($sql_dept);
    $stmt_dept->execute([':name' => $department_name]);
    $dept_id = $stmt_dept->fetchColumn();

    if ($dept_id) {
        // Insert new seminar topic using IDs
        $sql = "INSERT INTO seminars (student_name, dept_id, semester_id, subject_id, topic, status) 
                VALUES (:student_name, :dept_id, :semester_id, :subject_id, :topic, 'Pending')";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([
            ':student_name' => $name,
            ':dept_id' => $dept_id,
            ':semester_id' => $semester_id,
            ':subject_id' => $subject_id,
            ':topic' => $topic
        ]);

        $_SESSION['success_message'] = "✅ Topic '$topic' submitted successfully!";
    } else {
        $_SESSION['error_message'] = "❌ Error: Department not found.";
    }

    header("Location: index.php");
    exit();
}
?>