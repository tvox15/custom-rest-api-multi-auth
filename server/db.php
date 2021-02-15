<?php 

function OpenCon()
    {


        $dbhost = "localhost";
        $dbuser = "root";
        $dbpass = "";
        $db = "mydb";
        
        $conn = new mysqli($dbhost, $dbuser, $dbpass, $db) or die("Connect failed: %s\n" . $conn->error);

        return $conn;
    }

    // function to close mysql db connection
    function CloseCon($conn)
    {
        $conn->close();
    }

?>