<?php
// Initialize the session
session_start();
// Include config file
require_once "../server/db.php";
require_once "../server/constants.php";
$conn = OpenCon();

$click_email_link = $reset = "";
$search_err = "";
// Check if the user is already logged in, if yes then redirect him to welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: ".$homePath."dashboard.php");
    exit;
}

// Code for if the user just registered
if (isset($_GET["v"])) {
    $click_email_link = '<div class="row text-center"><div class="col-12"><h4 class="text-center">We have sent you a confirmation email<br>Click it to complete signup and log in</h4></div></div><br>';
}

// code for if the user tried to access page hidden behind login
if (isset($_GET["err"]) && $_GET["err"] === "search") {
    $search_err = "You must login or register to search";
}

// Code for if the user just confirmed their email
if (isset($_GET["key"])) {

    $keyValue = $_GET["key"];
    $confirmEmailSQL = "SELECT email FROM users Where selector = ?";

    // prepare statement
    $stmt = $conn->prepare($confirmEmailSQL);

    // bind params 
    $stmt->bind_param("s", $param_key);

    // set params 
    $param_key = $keyValue;

    //  execute params
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $confirmedQuery = "UPDATE users SET emailConfirmed=1 WHERE selector=?";

            // prepare statement
            $stmtConfirmed = $conn->prepare($confirmedQuery);

            // bind params 
            $stmtConfirmed->bind_param("s", $param_selector2);
            $param_selector2 = $keyValue;
            if ($stmtConfirmed->execute()) {
                $email_confirmed_status = 'Email confirmed! You may now log in.';
            } else {
                echo 'Something went wrong';
            }
        }
    } else {
        echo 'no results found';
    }
}

// Define variables and initialize with empty values
$email = $password = "";
$email_err = $password_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if email is empty
    if (strlen(trim($_POST["email"])) == 0) {
        $email_err = "Please enter email.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Check if password is empty
    if (strlen(trim($_POST["password"])) == 0) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if (empty($email_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT id, email, password, emailConfirmed, userType, FirstName, LastName FROM users WHERE email = ? LIMIT 1";

        // prepare query
        $stmt = $conn->prepare($sql);

        // bind params 
        $stmt->bind_param("s", $param_email);

        // set params 
        $param_email = $email;

        //  execute params
        if ($stmt->execute()) {
            // fetch results
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                while ($row = $result->fetch_assoc()) {
                    if ($row['emailConfirmed'] === 1) {
                        if (password_verify($password, $row['password'])) {
                            // Password is correct, so start a new session
                            session_start();
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $row['id'];
                            $_SESSION["email"] = $email;
                            $_SESSION["userType"] = $row['userType'];
                            $_SESSION["FirstName"] = $row['FirstName'];
                            $_SESSION["LastName"] = $row['LastName'];
                            // redirect to home
                                header("location: ../dashboard.php");
                        } else {
                            // Display an error message if password is not valid
                            $password_err = "The password you entered was not valid.";
                        }
                    } else {
                        $unconfirmed_email_err = "Please confirm your email before logging in";
                    }
                };
            } else {
                // Display an error message if email doesn't exist
                $email_err = "No account found with that email.";
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <?php require_once('../css/bootstrap.php'); ?>
</head>

<body>
    <?php require_once('../components/header-bar.php'); ?>
    <div class="container">
        <?= $click_email_link; ?>
        <div class="login-wrapper center">
        <h6 class="text-danger"><?=$search_err;?></h6>
            <?php if (isset($_GET['reset']) && $_GET['reset'] === "success") : ?>
                <div class="row">
                    <h4 class="text-center update-text">Password changed succesfully.</h4>
                </div><br>
            <?php endif; ?>
            <?php if (isset($email_confirmed_status)) {
                echo $email_confirmed_status;
            } ?>
            <h2>Login</h2>
            <p>Please fill in your credentials to login.</p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                    <label>Email</label>
                    <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
                    <span class="text-danger"><?php echo $email_err; ?></span>
                    <span class="text-danger"><?php if (isset($unconfirmed_email_err)) {
                                                    echo $unconfirmed_email_err;
                                                } ?></span>

                </div>
                <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control">
                    <span class="text-danger"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Login">
                </div>
                <p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
                <p>Forgot your password? <a href="forgotPassword.php">Reset it here</a>.</p>
            </form>
        </div>
    </div>
</body>

</html>