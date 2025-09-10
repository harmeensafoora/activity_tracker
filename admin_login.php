<?php
session_start();
include('includes/db_connect.php');

if(isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $sql = "SELECT ID, Email FROM admins WHERE Email = :email AND Password = :password LIMIT 1";
    $stmt = $dbh->prepare($sql);
    $stmt->execute([':email' => $email, ':password' => $password]);

    if ($stmt->rowCount() === 1) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $_SESSION['admin_id'] = $row['ID'];
    $_SESSION['admin_email'] = $row['Email'];

    // Fetch subjects by their VARCHAR ID
    $sql_subjects = "SELECT subject_id FROM admin_subjects WHERE admin_id = :admin_id";
    $stmt_subjects = $dbh->prepare($sql_subjects);
    $stmt_subjects->execute([':admin_id' => $row['ID']]);
    $subject_ids = $stmt_subjects->fetchAll(PDO::FETCH_COLUMN);

    // Store the list of subject codes in the session
    $_SESSION['admin_subjects'] = $subject_ids;

    header("Location: admin_dashboard.php");
    exit;
} else {
    $error = "Invalid email or password.";
}
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin Login - Academic Tracker</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    body {
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0%;
      background: #f7f9fc;
      color: #333;
    }
    header {
      background: #2c3e50;
      color: white;
      padding: 20px;
      text-align: center;
      position: relative;
    }
    label {
      font-weight: bold;
      display: block;
      margin-bottom: 6px;
      margin-top: 12px;
    }

    input, select {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      margin-bottom: 10px;
      font-size: 14px;
    }
    .btn {
      background: #2980b9;
      color: white;
      border: none;
      padding: 10px;
      width: 30%;
      border-radius: 8px;
      cursor: pointer;
      font-weight: bold;
      transition: 0.3s;
    }

    .btn:hover {
      background: #1c5985;
    }
    .form-section {
      background: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0px 4px 15px rgba(0,0,0,0.1);
      width: 350px;
      transition: transform 0.3s ease;
      margin-left: 40%;
      margin-top: 50px;
    }

    .form-section h2 {
      margin-bottom: 20px;
      font-size: 1.4rem;
      text-align: center;
      color: #2c3e50;
      margin-top: 200px;
    }
    span{
      font-size: 25px;
      font-weight: bolder;
    }
  </style>
</head>
<body>
  <header>
    <h1>🎓 Academic Tracker</h1>
      <h2>Admin Login</h2>
  </header>
    <?php if(!empty($error)): ?>
      <div class="error mt-12"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post" action="" class="form-section">
      <div class="field">
    <p><span>Hello, Admin!</span><br>Login to track and update student activities.</p>
        <label class="small">Email</label>
        <input type="text" name="email" required placeholder="Enter Email">
      </div>
      <div class="field">
        <label class="small">Password</label>
        <input type="password" name="password" required placeholder="Enter Password">
      </div>
      <button type="submit" name="login" class="btn">Login</button>
    </form>

    <p class="small center mt-12">Go back to <a href="index.php">View Seminars</a></p>
  </div>
</body>
</html>