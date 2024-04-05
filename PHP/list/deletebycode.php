<?php
    require_once('../database.php');
    require_once('../errorcodes.php');

    $debugMode = false;

    if ($debugMode){
        $listcode = 0;
    }else{
        $listcode = $_POST["code"];
    }

    $db = new Database();
    $conn = $db->get_connection();
    if($conn === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " -" . $errorMsg);
        return;
    }

    // Get list id
    $tsql = "SELECT id FROM lists WHERE code = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listcode));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " -" . $errorMsg);
        return;
    }
    $count = sqlsrv_num_rows($stmt);
    if ($count == 0){
        print_error(ErrorCodes::ListNotFoundError, "List doesn't exist or has been deleted");
        return;
    }
    $listid = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)['id'];
    
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

    // Check that no items, members or admins are left
    $tsql = "SELECT * FROM listitems WHERE listid = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid));
    $count = sqlsrv_num_rows($stmt);
    if ($count > 0){
        print_error(ErrorCodes::DeleteError, "List items were not deleted");
        return;
    }

    $tsql = "SELECT * FROM listmembers WHERE listid = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid));
    $count = sqlsrv_num_rows($stmt);
    if ($count > 0){
        print_error(ErrorCodes::DeleteError, "List members were not deleted");
        return;
    }

    $tsql = "SELECT * FROM listadmins WHERE listid = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid));
    $count = sqlsrv_num_rows($stmt);
    if ($count > 0){
        print_error(ErrorCodes::DeleteError, "List admins were not deleted");
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
    
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
    echo "Success";
?>