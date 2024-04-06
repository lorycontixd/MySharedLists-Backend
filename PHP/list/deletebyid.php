<?php
    require_once('../database.php');
    require_once('../errorcodes.php');

    $debugMode = false;

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

    // Reset all list ids to be in order
    $tsql = "SELECT * FROM lists ORDER BY id";
    $stmt = sqlsrv_query($conn, $tsql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    $rescount = sqlsrv_num_rows($stmt);

    $i = 0;
    while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
        $tsql = "UPDATE lists SET id = ? WHERE id = ?";
        $stmt = sqlsrv_query($conn, $tsql, array($i, $row['id']));
        if ($stmt === false){
            $errorMsg = sqlsrv_errors()[0]['message'];
            $errorCode = sqlsrv_errors()[0]['code'];
            die("Error: " . $errorCode . " -" . $errorMsg);
            return;
        }
        $i++;
    }
    
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
    echo "Success";
?>