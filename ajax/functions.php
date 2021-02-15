<?php
session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {

    require_once '../server/db.php';
    require_once '../server/constants.php';
    $function = $_POST['function'];
    switch ($function) {

        case 'getDbFields':
            $table = $_POST['table'];
            $fieldNames = array();

            // create query to get column names
            $sql = "DESCRIBE " . $table . ";";
            $conn = OpenCon();
            // prepare query
            $stmt = $conn->prepare($sql);

            //  execute params
            $stmt->execute();

            // fetch results
            $result = $stmt->get_result();

            //push results to array
            while ($row = $result->fetch_assoc()) {
                array_push($fieldNames, $row['Field']);
            };
            echo json_encode($fieldNames);
            break;


        case 'addToDb':
            $data = JSON_decode($_POST['data']);
            $table = $_POST['table'];
            $numOfColumns = $_POST['numOfColumns'];
            $conn = OpenCon();

            // get data types from table for prepared statement
            $dataTypeArray = "";
            $sql = "select data_type from information_schema.columns where table_schema = '$databaseName' and table_name = '$table'";
            $stmt = $conn->prepare($sql);
            $stmt->execute();

            // fetch results
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                if ($row['data_type'] === "varchar") {
                    $dataTypeArray .= "s";
                }
                if ($row['data_type'] === "int") {
                    $dataTypeArray .= "i";
                }
            };

            // run insert query
            //build sql query dynamically
            $sql = "INSERT INTO $table VALUES (";
            for ($i = 0; $i < $numOfColumns; $i++) {
                $sql .= "?";
                $sql .= $i === $numOfColumns - 1 ? "" : ", ";
            }
            $sql .= ");";

            // prepare query
            $stmt = $conn->prepare($sql);

            // bind params 
            $stmt->bind_param($dataTypeArray, ...$data);

            //  execute params
            if ($stmt->execute()) {
                echo 'Athlete successfully added!';
            } else {
                echo 'ERROR: ' . $stmt->error;
            }
            break;

        case 'changePassword':
            // save variables
            $currentPassword = $_POST['currentPassword'];
            $newPassword = $_POST['newPassword'];
            $newPasswordRepeat = $_POST['newPasswordRepeat'];
            $sql = "SELECT password FROM users WHERE email = ? LIMIT 1";
            $conn = OpenCon();
            // check if password matches

            // prepare query
            $stmt = $conn->prepare($sql);

            // bind params 
            $stmt->bind_param("s", $param_email);

            // set params 
            $param_email = $_SESSION['email'];

            //  execute params
            if ($stmt->execute()) {
                // fetch results
                $result = $stmt->get_result();
                if ($result->num_rows == 1) {
                    while ($row = $result->fetch_assoc()) {
                        if (password_verify($currentPassword, $row['password'])) {
                            // Password is correct, so update it in DB
                            $sqlUpdatePassword = "UPDATE users SET password=? WHERE email=?";
                            $updatePasswordStmt = $conn->prepare($sqlUpdatePassword);

                            // bind params 
                            $updatePasswordStmt->bind_param("ss", $param_password, $param_email);

                            // set params 
                            $param_password = password_hash($newPassword, PASSWORD_DEFAULT);
                            $param_email = $_SESSION['email'];

                            if ($updatePasswordStmt->execute()) {
                                echo 'Password Updated';
                            } else {
                                echo 'There was an error. Try again later';
                            }
                        } else {
                            // Display an error message if password is not valid
                            echo "The password you entered was not valid.";
                        }
                    }
                }
            }
            break;

        case 'toggleAddTablePrivileges':
            // save variables
            $checkboxTable = $_POST['table'];
            $email = $_POST['email'];

            // check if the record exists in the users_table_approved table
            $sql = "SELECT * FROM users_table_approved WHERE email=? AND uic_table=? LIMIT 1";
            $conn = OpenCon();

            // prepare query
            $stmt = $conn->prepare($sql);

            // bind params 
            $stmt->bind_param("ss", $param_email, $param_table);

            // set params 
            $param_email = $email;
            $param_table = $checkboxTable;

            //  execute params
            if ($stmt->execute()) {
                // fetch results
                $result = $stmt->get_result();
                if ($result->num_rows == 1) {
                    // if found, remove from DB
                    $sqlDelete = "DELETE FROM users_table_approved WHERE email=? AND uic_table=?";

                    // prepare query
                    $stmtDelete = $conn->prepare($sqlDelete);

                    // bind params 
                    $stmtDelete->bind_param("ss", $param_email, $param_table);

                    // set params 
                    $param_email = $email;
                    $param_table = $checkboxTable;

                    //  execute params
                    if ($stmtDelete->execute()) {
                        echo 'deleted';
                    } else {
                        echo 'Error. Try again later';
                    }
                } else {
                    // add to database
                    $sqlInsert = "INSERT INTO users_table_approved (email, uic_table) VALUES (?, ?)";
                    // prepare query
                    $stmtInsert = $conn->prepare($sqlInsert);

                    // bind params 
                    $stmtInsert->bind_param("ss", $param_email, $param_table);

                    // set params 
                    $param_email = $email;
                    $param_table = $checkboxTable;

                    //  execute params
                    if ($stmtInsert->execute()) {
                        echo 'inserted';
                    } else {
                        echo 'Error. Try again later';
                    }
                }
            }
            break;
    }
}
