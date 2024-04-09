<?php
    require_once('../database.php');
    require_once('../data/user.php');
    require_once('../data/list.php');
    require_once('../errorcodes.php');

    $debugMode = false;

    if ($debugMode){
        $creatorid = 1;
        $invitedusername = "lc";
        $listid = 0;
    }else{
        $creatorid = $_POST['creatorid'];
        $invitedusername = $_POST['invitedusername'];
        $listid = $_POST['listid'];
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

    // Check if users exists
    $stmt = sqlsrv_query( $conn, "select * from users where id = ?" , array($creatorid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false) {
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $count = sqlsrv_num_rows($stmt);
    if ($count == 0){
        print_error(ErrorCodes::UserNotFoundError, "User does not exist or has been deleted");
        return;
    }

    $stmt = sqlsrv_query( $conn, "select * from users where username = ?" , array($invitedusername), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false) {
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $count = sqlsrv_num_rows($stmt);
    if ($count == 0){
        print_error(ErrorCodes::UserNotFoundError, "User does not exist or has been deleted");
        return;
    }
    if ($count > 1){
        print_error(ErrorCodes::ConflictError, "Multiple users with the same username");
        return;
    }
    $inviteduser = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    // Check if list exists
    $stmt = sqlsrv_query( $conn, "select * from lists where id = ?" , array($listid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false) {
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $count = sqlsrv_num_rows($stmt);
    if ($count == 0){
        print_error(ErrorCodes::ListNotFoundError, "List does not exist or has been deleted");
        return;
    }

    // Check that the user is not already invited to the list
    $stmt = sqlsrv_query( $conn, "select * from listinvitations where invitedid = ? and listid = ?" , array($inviteduser['id'], $listid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false) {
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $count = sqlsrv_num_rows($stmt);
    if ($count > 0){
        print_error(ErrorCodes::UserAlreadyInvitedError, "User " . $inviteduser['username'] . " is already invited to list " . $listid);
        return;
    }

    // Fetch invitation id
    $stmt = sqlsrv_query( $conn, "select * from listinvitations", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false) {
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $count = sqlsrv_num_rows($stmt);
    $invitationid = $count;

    // Insert list invitation
    $stmt = sqlsrv_query( $conn, "insert into listinvitations (id, creatorid, invitedid, listid, wasviewed, dayduration, creationdate) values (?,?,?,?,?,?,?)" , array($invitationid, $creatorid, $inviteduser['id'], $listid, 0, 7, $serverdate));
    if ($stmt === false) {
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    
    $_POST['invitationid'] = $invitationid;
    require_once('fetchsingleinvitationbyid.php');
?>