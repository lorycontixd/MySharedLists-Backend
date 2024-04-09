<?php
    require_once('../data/listinvitation.php');
    require_once('../database.php');
    require_once('../errorcodes.php');

    $debugMode = false;

    if ($debugMode){
        $id = 0;
    }else{
        $id = $_POST['invitationid'];
    }

    $db = new Database();
    $conn = $db->get_connection();
    if($conn === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }

    $tsql = "select * from listinvitations where id = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($id), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $count = sqlsrv_num_rows($stmt);
    if ($count == 0){
        print_error(ErrorCodes::InvitationNotFoundError->value, "Invitation doesn't exist or has been deleted");
        return;
    }

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    $invitation = new ListInvitation(
        $row['id'],
        $row['creatorid'],
        $row['invitedid'],
        $row['listid'],
        $row['wasviewed'],
        $row['dayduration'],
        $row['creationdate']
    );

    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
    echo(print_r($invitation->newJsonSerialize(), true));
?>