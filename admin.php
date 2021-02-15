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
require_once "php/functions.php";
// get all users of userType B
$conn = OpenCon();
$typeBUsers = getAllTypeBUsers($conn);
$tableNames = showTableNamesInDb($conn, $databaseName);

$checkedBoxes = getCheckedBoxes($conn);

// this function will check to see if a box is checked
function isBoxChecked($email, $table, $checkedBoxes) {
    return in_array([$email, $table], $checkedBoxes) ? 'checked' : '';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <?php require_once('css/bootstrap.php'); ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<body>
    <?php require_once('components/header-bar.php'); ?>
    <div class="container border p-4">
        <div class="row">
            <div class="col-12">
                <h3>Admin Panel</h3>
                <p>Use the boxes to add/remove athlete adding privileges to Type B users.</p>
            </div>
        </div>
        <table class="table">
            <thead>
            <tr>
                <th scope="col" >Email</th>
                <?php foreach($tableNames as $table): ?>
                    <th scope="col" class="text-center"><?=$table;?></th>
                <?php endforeach;?>                
            </tr>
            </thead>
            <tbody>
            <?php foreach($typeBUsers as $email): ?>
                <tr>
                    <th scope="row"><?=$email;?></th>
                    <?php foreach($tableNames as $table): ?>
                        <td class="text-center"><div><input type="checkbox" class="form-check-input" name="type-b-checkbox" email="<?=$email;?>" table="<?=$table;?>" <?=  isBoxChecked($email, $table, $checkedBoxes);?>></td>
                    <?php endforeach;?>
                   
                </tr>

            <?php endforeach;?>
           
            </tbody>
        
        </table>

    </div>

    <script>
      $(document).ready(function() {
            $("input:checkbox").click(function() {
                var table = $(this).attr('table');
                var email = $(this).attr('email')
                $.ajax({
                        url: "ajax/functions.php",
                        method: "POST",
                        data: {
                            function: 'toggleAddTablePrivileges',
                            table,
                            email
                        },
                        success: function(res) {
                        }
                    });

            }); 
        });
    </script>
</body>

</html>