<?php
// Initialize the session
session_start();
require_once "server/constants.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false) {
    header('location:' . $homePath . 'auth/login.php');
    exit;
}
// Include config file
require_once "server/db.php";
require_once "server/constants.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <?php require_once('css/bootstrap.php'); ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<body>
    <?php require_once('components/header-bar.php'); ?>
    <div class="container border p-4">
        <div class="row">
            <div class="col-12">
                <h1>Dashboard</h1>
                <p>Hello, <?= $_SESSION['email']; ?>!</p>
            </div>
        </div>

        <hr>
        <div class="row justify-content-center">
            <div class="col-3 text-center">
                <button id="search-tab" class="btn btn-primary tab w-100"><?= $_SESSION['userType'] === 'A' ? 'Search' : 'Search/Update'; ?></button>
            </div>
            <div class="col-3 text-center">
                <button id="instructions-tab" class="btn btn-primary tab w-100">Instructions</button>
            </div>
            <div class="col-3 text-center">
                <button id="account-info-tab" class="btn btn-primary tab w-100">Account Info</button>
            </div>
            <div class="col-3 text-center">
                <button id="change-password-tab" class="btn btn-primary tab w-100">Change Password</button>
            </div>
        </div>
        <div id="search" class="row">
            <div class="col-12 mt-4">
                <?php include 'search.php'; ?>
                <br>
                <?php if ($_SESSION['userType'] === 'B' || $_SESSION['userType'] === 'C') : ?>
                    <?php include 'add.php'; ?>
                <?php endif; ?>
            </div>
        </div>
        <div id="instructions" class="row d-none">
            <div class="col-12 mt-4 text-center">
                <h3>Instructions</h3>
                <?php if ($_SESSION['userType'] === 'A') : ?>
                    <!-- write instructions for type A users here -->
                    <p>type A instructions</p>
                <?php else : ?>
                    <!-- write instructions for type B users here -->
                    <p>type B instructions</p>
                <?php endif; ?>

            </div>
        </div>
        <div id="account-info" class="row d-none">
            <div class="col-12 mt-4">
                <p><b>Email:</b> <?= $_SESSION['email']; ?></p>
                <p><b>Name:</b> <?=$_SESSION['FirstName'] . ' ' . $_SESSION['LastName'] ;?></p>
            </div>
        </div>
        <div id="change-password" class="row d-none justify-content-center">
            <div class="col-6 mt-4">
                <h4>Change password:</h4>
                <div class="form-group col-12">
                    <label for="currentPassword">Current Password:</label>
                    <input type="password" class="form-control" id="currentPassword" name="currentPassword">
                </div>
                <div class="form-group col-12">
                    <label for="newPassword">New Password:</label>
                    <input type="password" class="form-control" id="newPassword" name="newPassword">
                </div>
                <div class="form-group col-12">
                    <label for="newPasswordRepeat">Retype new Password:</label>
                    <input type="password" class="form-control" id="newPasswordRepeat" name="newPasswordRepeat">
                </div>
                <div class="form-group col-12">
                    <button id="change-password-submit" name="submit" class="btn btn-primary">Change Password</button>
                </div>
                <p class="text-info" id="serverResponse"></p>
                <p class="text-danger" id="resultMessage"></p>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // table toggle
            $("#account-info-tab").click(function() {
                if ($("#account-info").hasClass('d-none')) {
                    $("#account-info").toggleClass('d-none')
                    $("#change-password").addClass('d-none')
                    $("#search").addClass('d-none')
                    $("#instructions").addClass('d-none')

                }
            });
            $("#change-password-tab").click(function() {
                if ($("#change-password").hasClass('d-none')) {
                    $("#change-password").toggleClass('d-none')
                    $("#account-info").addClass('d-none')
                    $("#search").addClass('d-none')
                    $("#instructions").addClass('d-none')
                }
            });
            $("#search-tab").click(function() {
                if ($("#search").hasClass('d-none')) {
                    $("#search").toggleClass('d-none')
                    $("#account-info").addClass('d-none')
                    $("#change-password").addClass('d-none')
                    $("#instructions").addClass('d-none')
                }
            });
            $("#instructions-tab").click(function() {
                if ($("#instructions").hasClass('d-none')) {
                    $("#instructions").toggleClass('d-none')
                    $("#account-info").addClass('d-none')
                    $("#change-password").addClass('d-none')
                    $("#search").addClass('d-none')
                }
            });

            // function for changing password
            $("#change-password-submit").click(function() {
                // init errors to blank
                var error = "";

                // get values
                var currentPassword = $("#currentPassword").val();
                var newPassword = $("#newPassword").val();
                var newPasswordRepeat = $("#newPasswordRepeat").val();

                // validate values
                if (currentPassword.length === 0 || newPassword.length === 0 || newPasswordRepeat.length === 0) {
                    error = "Must fill in all fields";
                } else if (newPassword !== newPasswordRepeat) {
                    error = "Passwords do not match";
                } else if (newPassword.length < 6) {
                    error = "Password must be more than 6 characters";
                }

                // send ajax if no errors found
                if (error === "") {
                    $("#resultMessage").empty();
                    $.ajax({
                        url: "ajax/functions.php",
                        method: "POST",
                        data: {
                            function: 'changePassword',
                            currentPassword,
                            newPassword,
                            newPasswordRepeat
                        },
                        success: function(res) {
                            $("#serverResponse").text(res);
                        }
                    });
                } else {
                    // set result to error message
                    $("#resultMessage").text(error);
                }
            });
        });
    </script>
</body>

</html>