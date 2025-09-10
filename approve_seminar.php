<?php
session_start();
include('includes/db_connect.php');

if(!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

if(!isset($_GET['id']) || !isset($_GET['action'])) {
    header("Location: admin_dashboard.php");
    exit;
}

$id = (int)$_GET['id'];
$action = $_GET['action'];

if(!in_array($action, ['approve','reject'])) {
    header("Location: admin_dashboard.php");
    exit;
}

$status = ($action === 'approve') ? 'Approved' : 'Rejected';

$sql = "UPDATE seminars SET status = :status WHERE id = :id";
$stmt = $dbh->prepare($sql);
$stmt->execute([':status' => $status, ':id' => $id]);

$msg = $status === 'approved' ? 'Seminar approved.' : 'Seminar rejected.';
header("Location: admin_dashboard.php?msg=" . urlencode($msg));
exit;
?>
