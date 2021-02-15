<?php
// Include config file
require_once "../server/db.php";
require_once "../server/constants.php";
$conn = OpenCon();

// Define variables and initialize with empty values
$email = $password = $confirm_password = $firstName = $lastName = "";
$email_err = $password_err = $confirm_password_err = $first_name_err = $last_name_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate email
    if (strlen(trim($_POST["email"])) == 0) {
        $email_err = "Please enter an email.";
    } else {
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE email = ?";

        // prepare query
        $stmt = $conn->prepare($sql);

        // bind params 
        $stmt->bind_param("s", $param_email);

        // set params 
        $param_email = $_POST["email"];

        //  execute params
        $stmt->execute();

        // fetch results
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $email_err = "This email is already taken.";
        }
        if (!filter_var($param_email, FILTER_VALIDATE_EMAIL)) {
            $email_err = "Invalid email address. Please try again.";
        } else {
            $email = trim($_POST["email"]);
        }

        // Get usertype based on if checkbox was clicked;
        if(isset($_POST['type-b-checkbox']) && $_POST['type-b-checkbox'] !== null) {
            $userType = "B";
        } else {
            $userType = "A";
        }   
    }
    /////////////////////////
    // Validate password
    if (strlen(trim($_POST["password"])) == 0) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if (strlen(trim($_POST["confirm_password"]))  == 0) {
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    if (strlen(trim($_POST["firstName"]))  == 0) {
        $first_name_err = "Please enter first name.";
    } else {
        $firstName = trim($_POST["firstName"]);
    }

    if (strlen(trim($_POST["lastName"]))  == 0) {
        $last_name_err = "Please enter last name.";
    } else {
        $lastName = trim($_POST["lastName"]);
    }

    // Check input errors before inserting in database
    if (empty($email_err) && empty($password_err) && empty($confirm_password_err) && empty($first_name_err) && empty($last_name_err)) {
        

        // Prepare an insert statement
        $sql = "INSERT INTO users (email, password, userType, selector, FirstName, LastName) VALUES (?, ?, ?, ?, ?, ?)";

        // prepare query
        $stmt = $conn->prepare($sql);

        // bind params 
        $stmt->bind_param("ssssss", $param_email, $param_password, $param_userType, $param_selector, $param_firstName, $param_lastName);

        // set params 
        $param_email = $email; 
        $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
        $param_userType = $userType;
        $param_selector = bin2hex(random_bytes(16));
        $param_firstName = $firstName;
        $param_lastName = $lastName;
        //  execute params
        if ($stmt->execute()) {
            // Send email
             include "confirmationEmail.php";

            // Redirect to login page
            header("location: login.php?v=signup");
        } else {
            echo "Something went wrong. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <?php require_once('../css/bootstrap.php'); ?>
</head>

<body>
    <?php require_once('../components/header-bar.php'); ?>
    <div class="container">
        <div class="login-wrapper center">
            <h2>Sign Up</h2>
            <p>Please fill this form to create an account.</p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                    <label>Email</label>
                    <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
                    <span class="text-danger"><?php echo $email_err; ?></span>
                </div>
                <div class="form-group <?php echo (!empty($first_name_err)) ? 'has-error' : ''; ?>">
                    <label>First Name</label>
                    <input type="text" name="firstName" class="form-control" value="<?php echo $firstName; ?>">
                    <span class="text-danger"><?php echo $first_name_err; ?></span>
                </div>
                <div class="form-group <?php echo (!empty($last_name_err)) ? 'has-error' : ''; ?>">
                    <label>Last Name</label>
                    <input type="text" name="lastName" class="form-control" value="<?php echo $lastName; ?>">
                    <span class="text-danger"><?php echo $last_name_err; ?></span>
                </div>
                <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                    <span class="text-danger"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                    <span class="text-danger"><?php echo $confirm_password_err; ?></span>
                </div>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="type-b-checkbox" name="type-b-checkbox">
                    <label class="form-check-label" for="type-b-checkbox">Request adding privileges</label>
                </div>
                <br>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Submit">
                    <input type="reset" class="btn btn-default" value="Reset">
                </div>
                <p>Already have an account? <a href="login.php">Login here</a>.</p>
                <p>Forgot your password? <a href="forgotPassword.php">Reset it here</a>.</p>
            </form>
        </div>
    </div>
</body>

</html