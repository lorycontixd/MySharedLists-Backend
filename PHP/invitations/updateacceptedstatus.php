<?php
    require_once('../errorcodes.php');
    require_once('../database.php');
    require_once('../data/listinvitation.php');

    $debugMode = false;

    if ($debugMode){
        $invitationid = 0;
        $newstate = 1;
    }else{
        $invitationid = $_POST['invitationid'];
        $newstate = $_POST['newstate'];
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

    // Check if invitation exists
    $stmt = sqlsrv_query( $conn, "select * from listinvitations where id = ?" , array($invitationid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false) {
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $count = sqlsrv_num_rows($stmt);
    if ($count == 0){
        print_error(ErrorCodes::InvitationNotFoundError, "Invitation does not exist or has been deleted");
        return;
    }
    $invitation = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    // Update
    $newstate = $newstate != 0 ? 1 : 0;
    $stmt = sqlsrv_query( $conn, "update listinvitations set accepted = ? where id = ?" , array($newstate, $invitationid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false) {
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }

    if ($debugMode){
        $_POST["invitationid"] = $invitationid;
    }
    sqlsrv_free_stmt($stmt);
    require_once('fetchsingleinvitationbyid.php');
?>