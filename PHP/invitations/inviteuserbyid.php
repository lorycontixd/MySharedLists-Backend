<?php
    require_once('../database.php');
    require_once('../data/user.php');
    require_once('../data/list.php');
    require_once('../errorcodes.php');

    $debugMode = false;

    if ($debugMode){
        $creatorid = 1;
        $invitedid = 0;
        $listid = 2;
    }else{
        $creatorid = $_POST['creatorid'];
        $invitedid = $_POST['invitedid'];
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
        print_error(ErrorCodes::UserNotFoundError->value, "User " . $creatorid . "does not exist or has been deleted");
        return;
    }
    $creatoruser = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    $stmt = sqlsrv_query( $conn, "select * from users where id = ?" , array($invitedid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false) {
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $count = sqlsrv_num_rows($stmt);
    if ($count == 0){
        print_error(ErrorCodes::UserNotFoundError->value, "User " . $invitedid . " does not exist or has been deleted");
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
        print_error(ErrorCodes::ListNotFoundError->value, "List " . $listid . " does not exist or has been deleted");
        return;
    }
    $list = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    // Check that the user is not already invited to the list
    $stmt = sqlsrv_query( $conn, "select * from listinvitations where invitedid = ? and listid = ?" , array($invitedid, $listid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false) {
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $count = sqlsrv_num_rows($stmt);
    if ($count > 0){
        print_error(ErrorCodes::UserAlreadyInvitedError->value, "User is already invited to the list");
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
    $stmt = sqlsrv_query( $conn, "insert into listinvitations (id, creatorid, invitedid, creatorusername, listid, listname, viewed, status, dayduration, creationdate) values (?,?,?,?,?,?,?,?,?,?)", array($invitationid, $creatorid, $invitedid, $creatoruser['username'], $listid, $list['name'], 0, 0, 7, $serverdate), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false) {
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    
    $_POST['invitationid'] = $invitationid;
    require_once('fetchsingleinvitationbyid.php');
?>