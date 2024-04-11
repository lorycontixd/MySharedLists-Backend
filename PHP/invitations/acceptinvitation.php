<?php
    require_once('../errorcodes.php');
    require_once('../database.php');
    require_once('../data/listinvitation.php');

    $debugMode = false;

    if ($debugMode){
        $invitationid = 0;
    }else{
        $invitationid = $_POST['invitationid'];
    }

    $db = new Database();
    $conn = $db->get_connection();
    if($conn === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }

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
        print_error(ErrorCodes::InvitationNotFoundError->value, "Invitation does not exist or has been deleted");
        return;
    }
    $invitation = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    $listid = $invitation['listid'];
    $userid = $invitation['invitedid'];

    $tsql = "update listinvitations set status = 1 where id = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($invitationid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }

    $tsql = "update listinvitations set viewed = 1 where id = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($invitationid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }

    if ($debugMode){
        $_POST["invitationid"] = $invitationid;
    }
    $_POST["listid"] = $listid;
    $_POST["userid"] = $userid;

    require_once('../list/addmemberbyid.php');

?>