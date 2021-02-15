<?php
// Initialize the session
session_start();

// Include db and constants file
require_once "../server/db.php";
require_once "../server/constants.php";

$email_pattern = '/^[^@\s<&>]+@([-a-z0-9]+\.)+[a-z]{2,}$/i';

if (isset($_POST['submit_email']) && $_POST['email'] && ctype_print($_POST['email']) && preg_match($email_pattern, $_POST['email'])) {

    //set flag for html
    $emailSentSuccess = false;
    $conn = OpenCon();
    // save variable
    $email = $_POST['email'];

    // prepare select statement
    $sql = "SELECT password FROM users WHERE email=?;";

    // prepare query
    $stmt = $conn->prepare($sql);

    // bind params 
    $stmt->bind_param("s", $param_email);

    // set params 
    $param_email = $email;

    //  execute params
    if ($stmt->execute()) {

        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            while ($row = $result->fetch_assoc()) {
                $emailEncoded = md5($email);
                $pass = md5($row['password']);

                $linkURL = $homePath. "auth/reset.php?key=" . $emailEncoded . "&reset=" . $pass;
                $to = $email;
                $subject = "Reset Account Password";
                $emailText = 'Please click this link to reset your password: <a href="' . $linkURL . '">Click</a>';
                $headers = "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
            }
        }
    }
} else {
    header('location:' . $homePath . 'auth/forgotPassword.php?email=invalid');
}

