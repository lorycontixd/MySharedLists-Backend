<?php
    require_once('../database.php');
    require_once('../errorcodes.php');

    $debugMode = false;

    if ($debugMode){
        $listid = 0;
        $itemid = 0;
    }else{
        $listid = $_POST['listid'];
        $itemid = $_POST['itemid'];
    }

    $db = new Database();
    $conn = $db->get_connection();
    if ($conn === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }

    // Check if list exists, fetch if it does
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
    $listrow = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    // Check if item exists, fetch if it does
    $tsql = "SELECT * FROM listitems WHERE id = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($itemid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $rescount = sqlsrv_num_rows($stmt);
    if ($rescount == 0){
        print_error(ErrorCodes::ItemNotFoundError, "Item doesn't exist or has been deleted");
        return;
    }
    $itemrow = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    // Check if item is in the list
    $tsql = "SELECT * FROM listitems WHERE listid = ? AND id = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid, $itemid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $rescount = sqlsrv_num_rows($stmt);
    if ($rescount == 0){
        print_error(ErrorCodes::ItemNotFoundError, "Item doesn't exist in the list");
        return;
    }

    // Remove the item
    $tsql = "DELETE FROM listitems WHERE id = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($itemid));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }

    // Return list
    sqlsrv_free_stmt($stmt);
    if ($debugMode){
        $_POST['listid'] = $listid;
    }
    require_once('fetchsinglebyid.php');

?>