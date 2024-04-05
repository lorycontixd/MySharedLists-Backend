<?php
    require_once('../database.php');
    require_once('../data/list.php');
    require_once('../errorcodes.php');

    $debugMode = false;

    if ($debugMode){
        $listcode = "iZWBClRQ";
    }else{
        $listcode = $_POST['code'];
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
    $tsql = "SELECT * FROM lists WHERE code = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listcode), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
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
    $listid = $list['id'];

    // Delete all items
    $tsql = "DELETE FROM listitems WHERE listid = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }

    // Return list
    if ($debugMode){
        $_POST['code'] = $listcode;
    }
    require_once("fetchsinglebycode.php");
?>