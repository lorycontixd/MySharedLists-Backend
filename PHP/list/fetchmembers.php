<?php
    require_once("../data/user.php");
    require_once("../database.php");
    require_once("../errorcodes.php");

    $debugMode = false;

    if ($debugMode){
        $listid = 0;
    }else{
        $listid = $_POST["listid"];
    }

    $db = new Database();
    $conn = $db->get_connection();
    if($conn === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $serverdate = $db->get_server_date();

    // Get list and check if it exists
    $tsql = "SELECT * FROM lists WHERE id = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $rescount = sqlsrv_num_rows($stmt);
    if ($rescount == 0){
        print_error(ErrorCodes::ListNotFoundError->value, "List doesn't exist or has been deleted");
        return;
    }
    $list = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    // Get list members
    $tsql = "SELECT * FROM listmembers WHERE listid = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $rescount = sqlsrv_num_rows($stmt);

    while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
        // Get user
        $tsql = "SELECT * FROM users WHERE id = ?";
        $stmt2 = sqlsrv_query($conn, $tsql, array($row['userid']), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        if ($stmt2 === false){
            $errorMsg = sqlsrv_errors()[0]['message'];
            $errorCode = sqlsrv_errors()[0]['code'];
            die("Error: " . $errorCode . " - " . $errorMsg);
            return;
        }
        $rescount2 = sqlsrv_num_rows($stmt2);
        if ($rescount2 == 0){
            print_error(ErrorCodes::UserNotFoundError->value, "User doesn't exist or has been deleted");
            return;
        }
        $userrow = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC);

        $user = new User(
            $userrow['id'],
            $userrow['roleid'],
            $userrow['username'],
            $userrow['email'],
            $userrow['password'],
            $userrow['firstname'],
            $userrow['lastname'],
            $userrow['iconurl'],
            $userrow['subscriptionplan'],
            $userrow['subscriptiondate'],
            $userrow['subscriptionenddate'],
            $userrow['subscriptionstatus'],
            $userrow['isonline'],
            $userrow['isdeleted'],
            $userrow['isvalidated'],
            $userrow['validationdate'],
            $userrow['validationcode'],
            $userrow['validationcodeexpiration'],
            $userrow['lastlogin'],
            $userrow['lastupdated'],
            $userrow['creationdate']
        );
        echo(print_r($user->jsonSerialize(), true)) . "<br />";
    }

    sqlsrv_free_stmt($stmt);
?>