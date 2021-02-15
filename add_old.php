<?php

// init password err to blank
$passwordErr = "";

// init logged in status
$loggedIn = false;
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["password"] !== "") {

    $tableNames = array();

    // save pword in variable
    $password = $_POST['password'];

    // check if pword is correct
    if ($password === "test") {
        $loggedIn = true;
        session_start();
        $_SESSION["loggedin"] = true;
    } else {
        $passwordErr = "Incorrect password";
    }

    if ($loggedIn) {
        // import  connection functions
        require('server/db.php');

        $conn = OpenCon();
        if ($conn) {
            // query to get table names to search
            $sql = "show tables WHERE Tables_in_app_t317v LIKE 'uic%'";

            // prepare query
            $stmt = $conn->prepare($sql);

            //  execute params
            $stmt->execute();

            // fetch results
            $result = $stmt->get_result();

            // iterate to push into array
            while ($rowTables = $result->fetch_assoc()) {
                // CHANGE DATABASE NAME
                array_push($tableNames, $rowTables['Tables_in_app_t317v']);
            };
        }
    }
}

?>

<html>

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <?php require_once('css/bootstrap.php'); ?>
    <style type="text/css">
        .main{
            margin: auto;
            width: 50%;
        }
        .main div{
            text-align: center;
        }
        table{
            padding: 10px;
        }
        .field-list table{
            margin: auto;
        }
        th {
            text-align: right;
        }
        .error {
            color: red;
        }
    </style>
</head>

<body>
<?php require_once('components/header-bar.php'); ?>
    <div class="main">
        <?php if (!$loggedIn) : ?>
            <div class="login-box">
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <label>Enter password:</label>
                <input id="password" type="password" name="password">
                <p id="error" class="error"><?= $passwordErr; ?></p>
                <input id="submit" type="submit" name="submit" value="Submit">
            </form>
            </div>
        <?php else : ?>
            <div class="table-list">
                Select table to add to:<br>
                <?php foreach ($tableNames as $table) : ?>
                    <button id="<?= $table; ?>"><?= $table; ?></button>
                <?php endforeach; ?>
            </div>
            <div class="field-list">
                <table class="field-table">
                </table>
                <p class="error"></p>
                <p class="status"></p>
            </div>
        <?php endif; ?>
    </div>
    <script>
        $(document).ready(function() {
            $("button").click(function() {
                var table = this.innerHTML;
                $.ajax({
                    url: "ajax/functions.php",
                    method: "POST",
                    data: {
                        table: table,
                        function: 'getDbFields'
                    },
                    success: function(res) {
                        var fields = JSON.parse(res);
                        $(".field-table").empty();
                        $("#submit").remove();
                        fields.forEach((field) => {
                            $(".field-table").append(`
                            <tr>
                                <th>` + field + `:</th>
                                <td><input type="text" id="` + field + `" name="` + field + `"></td>
                             </tr>
                             `)
                        })
                        $(".field-list").append(`<input id="submit" type="submit" name="submit" value="Submit">`)

                        // add click handler for submit function
                        $("#submit").click(function() {
                            // make sure all fields are filled
                            var blankErr;
                            var allFieldData = [];
                            fields.forEach((field) => {
                                if(!blankErr) {
                                blankErr = $("#" + field).val() === "" ? true : false;
                                allFieldData.push($("#" + field).val());
                                }
                            });
                            if (blankErr) {
                                $(".error").text("All fields must be filled in");
                            } else {
                                $(".status").text("Inserting new data...");

                                stringAllFieldData = JSON.stringify(allFieldData);
                                $.ajax({
                                    url: "ajax/functions.php",
                                    method: "POST",
                                    data: {
                                        function: 'addToDb',
                                        data: stringAllFieldData,
                                        table: table,
                                        numOfColumns: fields.length
                                    },
                                    success: function(res) {
                                        $(".error").remove();
                                        $(".status").text(res);
                                    }
                                });
                            }
                        });
                    }
                });
            });

        });
    </script>
</body>

</html>