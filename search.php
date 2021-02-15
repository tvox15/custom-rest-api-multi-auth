<?php
require_once "server/constants.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false) {
    header('location:' . $homePath . 'auth/login.php?err=search');
    exit;
}
// init empty arrays
$queryResult = "";
$fieldNames = array();
// Set errors to blank
$error = "";
$notFound = true;
$noRowsError = false;
$tableContainingAthlete = "";
$tableNames = array();

// handle POST method
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["uic-number"] !== "") {


    $conn = OpenCon();
    if ($conn) {
        // query to get table names to search
        $sql = "show tables WHERE Tables_in_$databaseName LIKE 'uic%'";

        // prepare query
        $stmt = $conn->prepare($sql);

        //  execute params
        $stmt->execute();

        // fetch results
        $result = $stmt->get_result();

        // iterate to push into array
        while ($rowTables = $result->fetch_assoc()) {
            // CHANGE DATABASE NAME
            array_push($tableNames, $rowTables["Tables_in_$databaseName"]);
        };
    }


    // data validation on server side
    if (strlen($_POST["uic-number"]) !== 20) {
        $error = "Invalid UIC Number";
    } else {
        $error = "";
    }


    if ($conn) {


        while ($notFound) {

            foreach ($tableNames as $table) {
                // create query to get athlete information
                $sql = "SELECT * FROM " . $table . " WHERE UICNumber=? LIMIT 1";

                // prepare query
                $stmt = $conn->prepare($sql);

                // bind params 
                $stmt->bind_param("s", $UICNumber);

                // set params 
                $UICNumber = $_POST["uic-number"];

                //  execute params
                $stmt->execute();

                // fetch results
                $result = $stmt->get_result();

                // If the result is found, do this
                if ($result->num_rows !== 0) {

                    $queryResult = $result->fetch_assoc();

                    // set not found flag to false to exit while loop
                    $notFound = false;

                    // save variable containing table name
                    $tableContainingAthlete = $table;

                    // create query to get column names
                    $sql = "DESCRIBE " . $table . ";";


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
                    break;
                }
            }
            if ($notFound) {
                $noRowsError = true;
            }
            break;
        }
    }
}
?>




<head>
    <style type="text/css">
        .search-box,
        .add-button-wrapper {
            margin: auto;
            width: 50%;
        }

        .add-button-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .search-box-content h3,
        p,
        form {
            text-align: center;
        }

        .enter-uic-number-box {
            margin-bottom: 3px;
        }

        .error {
            color: red;
        }

        .list-option {
            margin: auto;
            width: 50%;
            display: flex;
            justify-content: space-around;
            text-align: center;
        }

        .label-checkbox {
            text-align: right;
            padding-right: 10px;
            width: 100%;
        }

        .input-checkbox {
            text-align: left;
            padding-left: 10px;
            width: 100%;
        }
    </style>
    <?php require_once('css/bootstrap.php'); ?>
</head>


    <?php require_once('components/header-bar.php'); ?>
    <?php if (!$noRowsError) : ?>
        <div class="table-wrapper">
            <?php if (count($fieldNames) !== 0) : ?>
                <h1 class="title">Athlete Information</h1>
            <?php endif; ?>
            <table>
                <?php foreach ($fieldNames as $key => $fieldName) : ?>
                    <tr>
                        <th><?= $fieldName; ?></th>
                        <td><?= $queryResult[$fieldName] ?></td>
                    </tr>
                <?php endforeach; ?>

            </table>
                    </div>
        <?php /* This is shown if the uic number is not found */ else : ?>
            <div class="no-rows-found-wrapper">
                <h4 class="no-rows-text">Athlete not found</h4>
                <p> Try again?</p>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <p class="enter-uic-box">Enter UIC Number:</p>
                    <input id="uic-number-field" type="text" name="uic-number">
                    <p id="error" class="error"></p>
                    <input id="submit" type="submit" name="submit" value="Submit">
                </form>
            </div>
        <?php endif; ?>
        <div class="search-box">
            <div class="search-box-content">
                <h3><i>Search Athletes</i></h3>
                <p class="enter-uic-number-box">Enter UIC Number:</p>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input id="uic-number-field" type="text" name="uic-number">
                    <p id="error" class="error"></p>
                    <input id="submit" type="submit" name="submit" value="Search">
                </form>
            </div>
        </div>
        <br>
       

        <script type="text/javascript" src="DataValidation.js"></script>
