<?php
    require_once('../database.php');
    require_once('../errorcodes.php');

    $debugMode = true;

    if ($debugMode){
        $listid = 8;
    }else{
        $listid = $_POST["listid"];
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

    // Delete list items
    $tsql = "DELETE FROM listitems WHERE listid = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " -" . $errorMsg);
        return;
    }

    // Delete list members
    $tsql = "DELETE FROM listmembers WHERE listid = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " -" . $errorMsg);
        return;
    }

    // Delete list admins
    $tsql = "DELETE FROM listadmins WHERE listid = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " -" . $errorMsg);
        return;
    }

    // Delete list
    $tsql = "DELETE FROM lists WHERE id = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " -" . $errorMsg);
        return;
    }

    // Check that no items, members or admins are left
    $tsql = "SELECT * FROM listitems WHERE listid = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid));
    $rescount = sqlsrv_num_rows($stmt);
    if ($rescount > 0){
        print_error(ErrorCodes::DeleteError->value, "List items not deleted");
        return;
    }

    $tsql = "SELECT * FROM listmembers WHERE listid = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid));
    $rescount = sqlsrv_num_rows($stmt);
    if ($rescount > 0){
        print_error(ErrorCodes::DeleteError->value, "List members not deleted");
        return;
    }
    
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
    echo "Success";
?>