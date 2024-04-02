<?php
    include("../data/user.php");
    require_once("../database.php");

    $debugMode = false;

    if ($debugMode){
        $listid = 0;
    }else{
        $listid = $_POST["listid"];
    }

    $db = new Database();
    $conn = $db->get_connection();
    if($conn === false){
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode);
    }
    $serverdate = $db->get_server_date();

    // Get list and check if it exists
    $tsql = "SELECT * FROM lists WHERE id = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode);
        return;
    }
    $rescount = sqlsrv_num_rows($stmt);
    if ($rescount == 0){
        die("Error: List doesn't exist or has been deleted");
        return;
    }
    $list = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    // Get list members
    $tsql = "SELECT * FROM listmembers WHERE listid = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode);
        return;
    }
    $rescount = sqlsrv_num_rows($stmt);

    while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
        // Get user
        $tsql = "SELECT * FROM users WHERE id = ?";
        $stmt2 = sqlsrv_query($conn, $tsql, array($row['userid']), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        if ($stmt2 === false){
            $errorCode = sqlsrv_errors()[0]['code'];
            die("Error: " . $errorCode);
            return;
        }
        $rescount2 = sqlsrv_num_rows($stmt2);
        if ($rescount2 == 0){
            die("Error: User " . $row['userid'] . " doesn't exist or has been deleted");
            return;
        }
        $userrow = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC);

        $user = new User(
            $userrow['id'],
            $userrow['username'],
            $userrow['firstname'],
            $userrow['lastname'],
            $userrow['password'],
            $userrow['lastupdated'],
            $userrow['creationdate']
        );
        echo(print_r($user->jsonSerialize(), true)) . "<br />";
    }

    sqlsrv_free_stmt($stmt);
?>