<?php
session_start();

// Include config file
require_once "../server/db.php";
require_once "../server/constants.php";
$email_err = "";

if(isset($_GET['email']) && $_GET['email'] === "invalid")
{
    $email_err = "Invalid Email";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Your Password</title>
    <?php require_once('../css/bootstrap.php'); ?>
</head>
<body>
<?php require_once('../components/header-bar.php');?>
 <div class="container ">

    <div class="login-wrapper center">
    <form method="post" action="send_link.php">
      <h2>Reset Password</h2>
       <p>Enter Email Address To Send Password Link</p>
        <label>Email:</label>
      <input type="text" name="email" class="form-control" >
       <span class="text-danger my-3"><?=$email_err;?></span>
      <br>
      <input type="submit" name="submit_email" class="btn btn-primary">
    </form>
    </div>
    </div>
  </body>
</html>
