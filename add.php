<?php


if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false ||  $_SESSION["userType"] === "A") {
    header('location:' . $homePath . 'auth/login.php?err=search');
    exit;
}
// import  connection functions
require('php/functions.php');
$conn = OpenCon();

// init array to show approvedTables
$approvedTables = array();

// init no Tables approved error
$noTablesApprovedErr = "";

if ($conn) {
    if ($_SESSION["userType"] === "B") {
        // query to get approved table names to search
        $sql = "SELECT * FROM users_table_approved WHERE email=?";

        // prepare query
        $stmt = $conn->prepare($sql);

        $stmt->bind_param("s", $param_email);

        // set params 
        $param_email = $_SESSION['email'];

        //  execute params
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            // iterate to push into array
            if ($result->num_rows == 0) {
                $noTablesApprovedErr = "Not approved to add to any tables";
            } else {
                while ($rowTables = $result->fetch_assoc()) {
                    // CHANGE DATABASE NAME
                    array_push($approvedTables, $rowTables["uic_table"]);
                };
            }
        }
    } else if ($_SESSION["userType"] === "C"){
        $approvedTables = showTableNamesInDb($conn, $databaseName);
    }
}
?>

    <div class="main">
        <?php if ($noTablesApprovedErr === "") : ?>
            <div class="table-list text-center">
                <p>Select table to add to:</p><br>
                <?php foreach ($approvedTables as $table) : ?>
                    <button class="button-table" id="<?= $table; ?>"><?= $table; ?></button>
                <?php endforeach; ?>
          
            </div>
            <br>
        <?php else : ?>
            <div class="container">
                <p>Not approved to add to any tables</p>
            </div>
        <?php endif; ?>
        <div class="field-list text-center">
            <table class="field-table">
            </table>
            <p class="error"></p>
            <p class="status"></p>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $(".button-table").click(function() {
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
                        $(".field-list").append(`<input id="submit-field" type="submit" name="submit" value="Submit">`)

                        // add click handler for submit function
                        $("#submit-field").click(function() {
                            // make sure all fields are filled
                            var blankErr;
                            var allFieldData = [];
                            fields.forEach((field) => {
                                if (!blankErr) {
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
