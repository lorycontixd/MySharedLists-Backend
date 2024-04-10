<?php
    require_once("../data/list.php");
    require_once("../database.php");
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
        die("Error: " . $errorMsg . " (" . $errorCode . ")");
        return;
    }
    $serverdate = $db->get_server_date();

    // Check if list exists, fetch if it does
    $tsql = "select * from lists where id = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorMsg . " (" . $errorCode . ")");
        return;
    }
    $rescount = sqlsrv_num_rows($stmt);
    if ($rescount == 0){
        print_error(ErrorCodes::ListNotFoundError->value, "List doesn't exist or has been deleted");
        return;
    }
    $listrow = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    // Check if user exists, fetch if it does
    $tsql = "select * from users where id = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($userid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorMsg . " (" . $errorCode . ")");
        return;
    }
    $rescount = sqlsrv_num_rows($stmt);
    if ($rescount == 0){
        print_error(ErrorCodes::UserNotFoundError->value, "User doesn't exist or has been deleted");
        return;
    }
    $userrow = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    // Check if user is already an admin
    $tsql = "SELECT * FROM listadmins WHERE listid = ? AND userid = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid, $userid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorMsg . " (" . $errorCode . ")");
        return;
    }
    $rescount = sqlsrv_num_rows($stmt);
    if ($rescount > 0){
        print_error(ErrorCodes::UserAlreadyAdminError->value, "User is already an admin of this list");
        return;
    }

    // Find id of new record
    $tsql = "SELECT * FROM listadmins";
    $stmt = sqlsrv_query($conn, $tsql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorMsg . " (" . $errorCode . ")");
        return;
    }
    $row_count = sqlsrv_num_rows($stmt);
    $newid = $row_count;

    // Insert new admin
    $tsql = "INSERT INTO listadmins (
        id,
        userid,
        listid,
        creationdate
        ) 
        VALUES
        (?, ?, ?, ?)";
    $var = array($newid, $userid, $listid, $serverdate);
    $stmt = sqlsrv_query($conn, $tsql, $var, array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorMsg . " (" . $errorCode . ")");
        return;
    }

    // Return list by calling fetchsinglebyid.php
    if ($debugMode){
        $_POST["listid"] = $listid;
    }
    require_once("fetchsinglebyid.php");
?>