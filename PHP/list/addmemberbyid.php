<?php
    require_once("../database.php");
    require_once("../data/list.php");
    require_once("../errorcodes.php");

    $debugMode = false;

    if ($debugMode){
        $listid = 0;
        $userid = 0;
    }else{
        $listid = $_POST["listid"];
        $userid = $_POST["userid"];
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
        print_error(ErrorCodes::ListNotFoundError, "List doesn't exist or has been deleted");
        return;
    }
    $list = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    // Get user and check if it exists
    $tsql = "SELECT * FROM users WHERE id = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($userid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $rescount = sqlsrv_num_rows($stmt);
    if ($rescount == 0){
        print_error(ErrorCodes::UserNotFoundError, "User doesn't exist or has been deleted");
        return;
    }
    $userrow = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    // Check if user is already a member of the list
    $tsql = "SELECT * FROM listmembers WHERE listid = ? AND userid = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid, $userid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $rescount = sqlsrv_num_rows($stmt);
    if ($rescount > 0){
        print_error(ErrorCodes::UserAlreadyMemberError, "User is already a member of the list");
        return;
    }

    // Get id of new list member
    $tsql = "SELECT * FROM listmembers";
    $stmt = sqlsrv_query($conn, $tsql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $row_count = sqlsrv_num_rows($stmt);
    $newuserid = $row_count;

    // Insert new list member
    $tsql = "INSERT INTO listmembers (id, userid, listid, creationdate) VALUES (?, ?, ?, ?)";
    $var = array($newuserid, $userid, $listid, $serverdate);
    $stmt = sqlsrv_query($conn, $tsql, $var);
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }

    // Return list
    if ($debugMode){
        $_POST['listid'] = $listid;
    }
    require_once("fetchsinglebyid.php");
?>