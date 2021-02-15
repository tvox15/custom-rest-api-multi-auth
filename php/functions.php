<?php


function showTableNamesInDb($conn, $databaseName) {
    $tableNames = array();
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
    return $tableNames;
}

function getAllTypeBUsers ($conn) {
    $typeBUsers = array();
    // query to get table names to search
    $sql = "SELECT * FROM users WHERE userType='B'";

    // prepare query
    $stmt = $conn->prepare($sql);

    //  execute params
    $stmt->execute();

    // fetch results
    $result = $stmt->get_result();

    // iterate to push into array
    while ($row = $result->fetch_assoc()) {
        // CHANGE DATABASE NAME
        array_push($typeBUsers, $row["email"]);
    };
    return $typeBUsers;
}

function getCheckedBoxes($conn) {

    $checkedBoxes = array();
    // query to get table names to search
    $sql = "SELECT * FROM users_table_approved";

    // prepare query
    $stmt = $conn->prepare($sql);

    //  execute params
    $stmt->execute();

    // fetch results
    $result = $stmt->get_result();

    // iterate to push into array
    while ($row = $result->fetch_assoc()) {
        // CHANGE DATABASE NAME
        array_push($checkedBoxes, array($row['email'], $row['uic_table']));
    };
    return $checkedBoxes;
}

?>