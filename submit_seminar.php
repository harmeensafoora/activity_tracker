<?php
session_start();
include('includes/db_connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $department_name = $_POST['department'];
    $semester_id = $_POST['semester'];
    $subject_id = $_POST['subject'];
    $topic = trim($_POST['topic']);

    try {
        // Verify Department
        $sql_dept = "SELECT id FROM departments WHERE name = :name";
        $stmt_dept = $dbh->prepare($sql_dept);
        $stmt_dept->execute([':name' => $department_name]);
        $dept_id = $stmt_dept->fetchColumn();
        if (!$dept_id) {
            $_SESSION['error_message'] = "Error: Department '" . htmlspecialchars($department_name) . "' not found in the database.";
            header("Location: index.php");
            exit();
        }

        // Verify Semester
        $sql_semester = "SELECT id FROM semesters WHERE id = :id";
        $stmt_semester = $dbh->prepare($sql_semester);
        $stmt_semester->execute([':id' => $semester_id]);
        if (!$stmt_semester->fetchColumn()) {
            $_SESSION['error_message'] = "Error: Selected semester ID '" . htmlspecialchars($semester_id) . "' not found.";
            header("Location: index.php");
            exit();
        }

        // Verify Subject
        $sql_subject = "SELECT id FROM subjects WHERE id = :id";
        $stmt_subject = $dbh->prepare($sql_subject);
        $stmt_subject->execute([':id' => $subject_id]);
        if (!$stmt_subject->fetchColumn()) {
            $_SESSION['error_message'] = "Error: Selected subject ID '" . htmlspecialchars($subject_id) . "' not found.";
            header("Location: index.php");
            exit();
        }

        // All foreign keys are valid, proceed with insertion
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
        header("Location: index.php");
        exit();

    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Database Error: " . $e->getMessage();
        header("Location: index.php");
        exit();
    }
}
?>