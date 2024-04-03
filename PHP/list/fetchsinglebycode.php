<?php
    require_once('../database.php');
    require_once('../data/list.php');

    $debugMode = false;
    if ($debugMode){
        $listCode = "0";
    }else{
        $listCode = $_POST['code'];
    }

    $db = new Database();
    $conn = $db->get_connection();
    if($conn === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }

    $stmt = sqlsrv_query( $conn, "select * from lists where code = ?" , array($listCode), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false) {
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $count = sqlsrv_num_rows($stmt);
    if ($count == 0){
        die("Error: List " . $listCode . " does not exist");
        return;
    }
    $listrow = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    // Fetch member ids
    $stmt = sqlsrv_query( $conn, "select * from listmembers where listid = ?" , array($listrow['id']), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false) {
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $memberids = array();
    while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
        array_push($memberids, $row['userid']);
    }

    // Fetch admin ids
    $stmt = sqlsrv_query( $conn, "select * from listadmins where listid = ?" , array($listrow['id']), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false) {
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $adminids = array();
    while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
        array_push($adminids, $row['userid']);
    }

    $list = new MyList(
        $listrow['id'],
        $listrow['name'],
        $listrow['description'],
        $listrow['creatorid'],
        $listrow['color'],
        $listrow['iconid'],
        $listrow['code'],
        $memberids,
        $adminids,
        $listrow['lastupdated'],
        $listrow['creationdate'],
    );
    sqlsrv_free_stmt($stmt);
    echo(print_r($list->jsonSerialize(), true));
?>