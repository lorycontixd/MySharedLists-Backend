<?php
    require_once('../database.php');
    require_once('../errorcodes.php');

    $debugMode = false;
    
    if ($debugMode){
        $listid = 2;
        $userid = 7;
    }else{
        $listid = $_POST['listid'];
        $userid = $_POST['userid'];
    }

    $db = new Database();
    $conn = $db->get_connection();
    if ($conn === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }

    // Check if user exists
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
        print_error(ErrorCodes::UserNotFoundError->value, "User doesn't exist or has been deleted");
        return;
    }
    $userrow = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

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
        print_error(ErrorCodes::ListNotFoundError->value, "List doesn't exist or has been deleted");
        return;
    }
    $listrow = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    // Remove user from list
    $tsql = "DELETE FROM listmembers WHERE listid = ? AND userid = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid, $userid));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }

    /*
    // Reset member ids to be in order
    $tsql = "SELECT * FROM listmembers";
    $stmt = sqlsrv_query($conn, $tsql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    
    $i = 0;
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
        $tsql = "UPDATE listmembers SET id = ? WHERE id = ?";
        $stmt2 = sqlsrv_query($conn, $tsql, array($i, $row['id']));
        if ($stmt2 === false){
            $errorMsg = sqlsrv_errors()[0]['message'];
            $errorCode = sqlsrv_errors()[0]['code'];
            die("Error: " . $errorCode . " - " . $errorMsg);
            return;
        }
        $i++;
    }*/

    // Remove user from list admins
    $tsql = "DELETE FROM listadmins WHERE listid = ? AND userid = ?";
        $stmt2 = sqlsrv_query($conn, $tsql, array($listid, $userid));
        if ($stmt2 === false){
            $errorMsg = sqlsrv_errors()[0]['message'];
            $errorCode = sqlsrv_errors()[0]['code'];
            die("Error: " . $errorCode . " - " . $errorMsg);
            return;
        }

    // Remove list invitations for user
    $tsql = "DELETE FROM listinvitations WHERE listid = ? AND invitedid = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid, $userid));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }


    if ($debugMode){
        $_POST['listid'] = $listid;
    }
    sqlsrv_free_stmt($stmt);
    require_once('fetchsinglebyid.php');
?>