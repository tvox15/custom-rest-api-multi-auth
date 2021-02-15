<?php
// Initialize the session
session_start();

// Include config file
require_once "../server/constants.php";

     
$linkURL=$homePath. "auth/login.php?key=".$param_selector;

$to = $email;
$subject = "Confirm [REMOVED] Email";
$emailText = '<html><body>Please click this link to confirm email: <a href="'.$linkURL.'">Click</a></body></html>';

$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

mail($to,$subject,$emailText, $headers);

