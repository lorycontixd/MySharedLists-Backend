<?php
    require_once('../database.php');
    require_once('../data/user.php');
    require_once('../data/list.php');

    $debugMode = false;

    if ($debugMode){
        $creatorid = 0;
        $invitedid = 1;
        $listid = 0;
    }else{
        $creatorid = $_POST['creatorid'];
        $invitedid = $_POST['invitedid'];
        $listid = $_POST['listid'];
    }
    
    $db = new Database();
    $conn = $db->get_connection();
    if($conn === false){
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode);
        return;
    }
    $serverdate = $db->get_server_date();

    // Check if users exists
    $stmt = sqlsrv_query( $conn, "select * from users where id = ?" , array($creatorid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false) {
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode);
        return;
    }
    $count = sqlsrv_num_rows($stmt);
    if ($count == 0){
        die("Error: User " . $creatorid . " does not exist");
        return;
    }

    $stmt = sqlsrv_query( $conn, "select * from users where id = ?" , array($invitedid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false) {
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode);
        return;
    }
    $count = sqlsrv_num_rows($stmt);
    if ($count == 0){
        die("Error: User " . $invitedid . " does not exist");
        return;
    }
    $inviteduser = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    // Check if list exists
    $stmt = sqlsrv_query( $conn, "select * from lists where id = ?" , array($listid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false) {
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode);
        return;
    }
    $count = sqlsrv_num_rows($stmt);
    if ($count == 0){
        die("Error: List " . $listid . " does not exist");
        return;
    }
    
    // Check that the user is not already invited to the list
    $stmt = sqlsrv_query( $conn, "select * from listinvitations where invitedid = ? and listid = ?" , array($invitedid, $listid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false) {
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode);
        return;
    }
    $count = sqlsrv_num_rows($stmt);
    if ($count > 0){
        die("Error: User " . $inviteduser['username'] . " is already invited to list ");
        return;
    }

    // Fetch invitation id
    $stmt = sqlsrv_query( $conn, "select * from listinvitations", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false) {
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode);
        return;
    }
    $count = sqlsrv_num_rows($stmt);
    $invitationid = $count;

    // Insert list invitation
    $stmt = sqlsrv_query( $conn, "insert into listinvitations (id, creatorid, invitedid, listid, wasviewed, dayduration, creationdate) values (?,?,?,?,?,?,?)" , array($invitationid, $creatorid, $invitedid, $listid, 0, 7, $serverdate));
    if ($stmt === false) {
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode);
        return;
    }
    
    $_POST['invitationid'] = $invitationid;
    require_once('fetchsingleinvitationbyid.php');
?>