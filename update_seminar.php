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
    $remarks = trim($_POST['remarks']);
    
    // Default values
    $status = null;
    $seminar_given = null;
    
    $update_fields = [];
    $update_params = [':id' => $id];

    if ($action === 'approve') {
        $status = 'Approved';
        $update_fields[] = "status = :status";
        $update_params[':status'] = $status;
    } elseif ($action === 'reject') {
        $status = 'Rejected';
        $update_fields[] = "status = :status";
        $update_params[':status'] = $status;
    } elseif ($action === 'mark_given') {
        $seminar_given = 'Yes';
        $update_fields[] = "seminar_given = :seminar_given";
        $update_params[':seminar_given'] = $seminar_given;
    }

    if (!empty($remarks) && ($action === 'approve' || $action === 'reject')) {
        $update_fields[] = "remarks = :remarks";
        $update_params[':remarks'] = $remarks;
    }
    
    if (!empty($update_fields)) {
        $sql = "UPDATE seminars SET " . implode(', ', $update_fields) . " WHERE id = :id";
        $stmt = $dbh->prepare($sql);
        $stmt->execute($update_params);
    }
}

header("Location: admin_dashboard.php");
exit();
?>