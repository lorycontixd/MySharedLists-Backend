<?php
    require_once('../database.php');
    require_once('../data/listinvitation.php');

    $debugMode = false;

    if ($debugMode){
        $userid = 0;
    }else{
        $userid = $_POST['userid'];
    }

    $db = new Database();
    $conn = $db->get_connection();
    if($conn === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }

    $stmt = sqlsrv_query( $conn, "select * from listinvitations where invitedid = ? and isviewed = 0" , array($userid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false) {
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }

    $invitations = array();
    while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
        $invitation = new ListInvitation(
            $row['id'],
            $row['creatorid'],
            $row['invitedid'],
            $row['listid'],
            $row['wasviewed'],
            $row['dayduration'],
            $row['creationdate']
        );
        $json = $invitation->newJsonSerialize();
        echo(print_r($json, true) . "<br />"); 
    }

    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
?>