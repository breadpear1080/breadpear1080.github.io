<?php
session_start();
require_once('db_config.php'); // Replace with your own database configuration file

$username = $_POST['username'];
$password = $_POST['password'];

// Validate user input
if(empty($username) || empty($password)) {
  header('Location: login.php?error=emptyfields');
  exit();
} else {
  // Query the database to check if the user exists and the password is correct
  $sql = "SELECT * FROM admin_users WHERE username=?;";
  $stmt = mysqli_stmt_init($conn);
  if(!mysqli_stmt_prepare($stmt, $sql)) {
    header('Location: login.php?error=sqlerror');
    exit();
  } else {
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if($row = mysqli_fetch_assoc($result)) {
      $pwdCheck = password_verify($password, $row['password']);
      if($pwdCheck == false) {
        header('Location: login.php?error=wrongpassword');
        exit();
      } elseif($pwdCheck == true) {
        // Start a session and store the user's ID and username
        $_SESSION['admin_id'] = $row['id'];
        $_SESSION['admin_username'] = $row['username'];
        header('Location: admin_panel.php');
        exit();
      }
    } else {
      header('Location: login.php?error=nouser');
      exit();
    }
  }
}
