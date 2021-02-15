<?php
// Initialize the session
session_start();
require_once "server/constants.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false) {
    header('location:' .$homePath .'auth/login.php');
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

    require('server/db.php');

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
                    $sql = "DESCRIBE ".$table.";";


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

<html>

<head>
    <style type="text/css">
        body {
            font-family: Arial, Helvetica, sans-serif;
        }

        .table-wrapper {
            margin: auto;
            width: 80%;
        }

        @media (max-width:1000px) {
            .table-wrapper {
                width: 100%;
            }
        }

        .title,
        .subtitle,
        .table-name {
            color: #6495ED;
            text-align: center;
        }

        .info {
            margin: auto;
            text-align: center;
        }

        table {
            border-spacing: 0;
            margin: auto;
        }

        td {
            padding-top: 5px;
            padding-bottom: 5px;
            font-size: 15px;
            padding-left: 10px;
        }

        th {
            color: white;
            background-color: #66b5ff;
            /* color for table headers */
            padding: 5px;
            width: 50%;
            text-align: right;
            border-bottom: white solid 1px;
        }

        tr:nth-child(even) {
            background-color: #cce6ff;
            /* color for alternating rows */
        }

        .procedure-row-wrapper {
            text-align: center;
            border-right: 1px solid #b3daff;
            /* color for line in between rows */
        }

        .description-row-wrapper {
            padding-left: 10px;
            padding-right: 10px;
            border-right: 1px solid #b3daff;
            /* color for line in between rows */
        }

        .fee-row-wrapper {
            width: 20%;
        }

        .fee-row-wrapper div {
            width: 100%;
            display: flex;
            justify-content: space-apart;
        }

        .fee-row-data {
            display: flex;
            justify-content: flex-end;
            padding-right: 5px;
        }

        .fee-row-dollar {
            padding-left: 5px;
        }

        .back-button-wrapper {
            margin: auto;
            margin-top: 20px;
            margin-bottom: 20px;
            text-align: center;
        }

        .no-rows-found-wrapper {
            margin: auto;
            width: 50%;
            text-align: center;
        }

        .search-box-wrapper {
            text-align: center;
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

<body>
<?php require_once('components/header-bar.php');?>
    <?php if (!$noRowsError) : ?>
        <div class="table-wrapper">
            <h1 class="title">Athlete Information</h1>
            <table>
                <?php foreach ($fieldNames as $key => $fieldName) : ?>
                    <tr>
                        <th><?= $fieldName; ?></th>
                        <td><?= $queryResult[$fieldName] ?></td>
                    </tr>
                <?php endforeach; ?>

            </table>
            <div class="search-box-wrapper">
                <h4 class="search-box-title">Search another athlete:</h4>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <p class="enter-uic-box">Enter UIC Number:</p>
                    <input id="uic-number-field" type="text" name="uic-number">
                    <p id="error" class="error"></p>
                    <input id="submit" type="submit" name="submit" value="Submit">
                </form>
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
        <script type="text/javascript" src="DataValidation.js"></script>
</body>

</html>