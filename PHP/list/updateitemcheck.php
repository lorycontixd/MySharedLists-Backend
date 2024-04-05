<?php
    require_once("../database.php");
    require_once("../data/listitem.php");
    require_once("../errorcodes.php");

    $debugMode = false;

    if ($debugMode){
        $itemid = 0;
        $listid = 1;
        $ischecked = true;
    }else{
        $itemid = $_POST["itemid"];
        $listid = $_POST["listid"];
        $ischecked = $_POST["ischecked"];
    }

    $db = new Database();
    $conn = $db->get_connection();
    if($conn === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }

    $tsql = "UPDATE listitems SET ischecked = ? WHERE listid = ? AND id = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($ischecked, $listid, $itemid));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }

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
        print_error(ErrorCodes::ItemNotFoundError, "Item doesn't exist or has been deleted");
        return;
    }
    $itemrow = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    sqlsrv_free_stmt($stmt);
    // Return list
    if ($debugMode){
        $_POST['listid'] = $listid;
    }
    require_once("fetchsinglebyid.php");
?>