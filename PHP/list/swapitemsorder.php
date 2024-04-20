<?php
    require_once('../database.php');
    require_once('../errorcodes.php');

    $debugMode = false;

    if ($debugMode){
        $listid = 0;
        $itemid1 = 0;
        $itemid2 = 0;
    }else{
        $listid = $_POST['listid'];
        $itemid1 = $_POST['itemid1'];
        $itemid2 = $_POST['itemid2'];
    }

    $db = new Database();
    $conn = $db->get_connection();
    if($conn === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " -" . $errorMsg);
        return;
    }

    // Check if list exists, fetch if it does
    $tsql = "SELECT * FROM lists WHERE id = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " -" . $errorMsg);
        return;
    }
    $rescount = sqlsrv_num_rows($stmt);
    if ($rescount == 0){
        print_error(ErrorCodes::ListNotFoundError->value, "List doesn't exist or has been deleted");
        return;
    }
    $list = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    // Check if list item 1 exists, fetch if it does
    $tsql = "SELECT * FROM listitems WHERE id = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($itemid1), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " -" . $errorMsg);
        return;
    }
    $rescount = sqlsrv_num_rows($stmt);
    if ($rescount == 0){
        print_error(ErrorCodes::ItemNotFoundError->value, "List item 1 doesn't exist or has been deleted");
        return;
    }
    $item1 = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    // Check if list item 2 exists, fetch if it does
    $tsql = "SELECT * FROM listitems WHERE id = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($itemid2), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " -" . $errorMsg);
        return;
    }
    $rescount = sqlsrv_num_rows($stmt);
    if ($rescount == 0){
        print_error(ErrorCodes::ItemNotFoundError->value, "List item 2 doesn't exist or has been deleted");
        return;
    }
    $item2 = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    // Check if list items belongs to list
    if ($item1['listid'] != $listid){
        print_error(ErrorCodes::ItemNotFoundError->value, "List item 1 doesn't belong to list");
        return;
    }
    if ($item2['listid'] != $listid){
        print_error(ErrorCodes::ItemNotFoundError->value, "List item 2 doesn't belong to list");
        return;
    }

    // Swap list order
    $item1_neworder = $item2['listorder'];
    $item2_neworder = $item1['listorder'];
    $tsql = "UPDATE listitems SET listorder = ? WHERE id = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($item1_neworder, $itemid1), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " -" . $errorMsg);
        return;
    }
    $stmt = sqlsrv_query($conn, $tsql, array($item2_neworder, $itemid2), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " -" . $errorMsg);
        return;
    }

    // Return list
    sqlsrv_free_stmt($stmt);
    if ($debugMode){
        $_POST['listid'] = $listid;
    }
    require_once("fetchsinglebyid.php");
?>