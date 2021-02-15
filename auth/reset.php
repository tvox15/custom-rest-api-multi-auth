<?php
// Include db and constants file
require_once "../server/db.php";
require_once "../server/constants.php";


// Define variables and initialize with empty values
$password = $confirm_password = "";
$password_err = $confirm_password_err = "";
// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['key'];
    $pass = $_POST['password'];

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have atleast 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    // check for errors and insert into database
    if (empty($password_err) && empty($confirm_password_err)) {

        $conn = OpenCon();
        // Prepare an insert statement
        $sql = "UPDATE users SET password=? WHERE md5(email)='" . $email . "';";

        // prepare query
        $stmt = $conn->prepare($sql);
    
        // bind params 
        $stmt->bind_param("s", $param_password);
    
        // set params 
        $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
    
        //  execute params
        if ($stmt->execute()) { 
            // Redirect to login page
            header("location: ".$homePath."auth/login.php?reset=success");
        } else {
            echo "Something went wrong. Please try again later.";
        }
    }
} 

$match = false;
if ($_GET['key'] && $_GET['reset']) {
    $email = $_GET['key'];
    $pass = $_GET['reset'];

    $conn = OpenCon();
    // prepare select statement
    $sqlQuery = "SELECT email, password FROM users WHERE md5(email)=? AND md5(password)=?;";

    // prepare query
    $stmt = $conn->prepare($sqlQuery);

    // bind params 
    $stmt->bind_param("ss", $param_email, $param_pass);
    
    // set params 
    $param_email = $email;
    $param_pass = $pass;

    //  execute params
    if ($stmt->execute()) { 
        $result = $stmt->get_result();
            if ($result->num_rows === 1) {
             $match = true;
            }
        }
}

if ($match===true) : ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <?php require_once("../css/bootstrap.php"); ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<body>
    <?php require_once("../components/header-bar.php"); ?>
                    <div class="container ">

                        <div class="center login-wrapper">
                            <h2>Reset your password</h2>

                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?" . $_SERVER["QUERY_STRING"]); ?>" method="post">

                                <input type="hidden" name="key" class="form-control" value="<?php echo $email; ?>">

                                <div class="form-group <?php echo (!empty($password_err)) ? "has-error" : ""; ?>">
                                    <label>Password</label>
                                    <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                                    <span class="help-block"><?php echo $password_err; ?></span>
                                </div>
                                <div class="form-group <?php echo (!empty($confirm_password_err)) ? "has-error" : ""; ?>">
                                    <label>Confirm Password</label>
                                    <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                                    <span class="help-block"><?php echo $confirm_password_err; ?></span>
                                </div>
                                <div class="form-group">
                                    <input type="submit" class="btn btn-primary" name="submit_password" value="Submit">
                                </div>

                            </form>
                        </div>
                    </div>
                </body>

                </html>
                <?php else: ?>
        
    
 <!DOCTYPE html>
 <head>
 <meta charset="UTF-8">
 <title>Profile</title>
 <?php require_once("../css/bootstrap.php"); ?>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<body>
 <?php require_once("../components/header-bar.php"); ?>
 <div class="container">
    <h1>This password reset link has expired </h1>
    </div>
  </body>
</html>
        <?php endif;

?>
