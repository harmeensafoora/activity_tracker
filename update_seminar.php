<?php
session_start();
include('includes/db_connect.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $action = $_POST['action'];

    if ($action === "approve" || $action === "reject") {
        $remarks = trim($_POST['remarks']);
        $status = ($action === "approve") ? "Approved" : "Rejected";

        $sql = "UPDATE seminars SET status = :status, remarks = :remarks WHERE id = :id";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([
            ':status' => $status,
            ':remarks' => $remarks,
            ':id' => $id
        ]);
    } elseif ($action === "mark_given") {
        $sql = "UPDATE seminars SET seminar_given = 'Yes' WHERE id = :id";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([
            ':id' => $id
        ]);
    }
}

header("Location: admin_dashboard.php");
exit();
?>