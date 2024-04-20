<?php
    require_once("../data/list.php");
    require_once("../data/listitem.php");
    require_once("../database.php");

    $debugMode = false;

    if ($debugMode){
        $listid = 0;
    }else{
        $listid = $_POST["listid"];
    }

    $db = new Database();
    $conn = $db->get_connection();
    if($conn === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }

    $tsql = "select * from lists where id = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $count = sqlsrv_num_rows($stmt);
    if ($count == 0){
        print_error(ErrorCodes::ListNotFoundError->value, "List doesn't exist or has been deleted");
        return;
    }
    $listrow = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    
    // Fetch members
    $tsql = "SELECT * FROM listmembers WHERE listid = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $members = array();
    while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
        array_push($members, $row['userid']);
    }

    // Fetch admins
    $tsql = "SELECT * FROM listadmins WHERE listid = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $admins = array();
    while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
        array_push($admins, $row['userid']);
    }

    // Fetch items
    $tsql = "SELECT * FROM listitems WHERE listid = ? order by listorder";
    $stmt = sqlsrv_query($conn, $tsql, array($listid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ( $stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $listitems = array();
    while($itemrow = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
        $listitem = new ListItem(
            $itemrow['id'],
            $itemrow['listorder'],
            $itemrow['name'],
            $itemrow['description'],
            $itemrow['quantity'],
            $itemrow['price'],
            $itemrow['brand'],
            $itemrow['listid'],
            $itemrow['ischecked'],
            $itemrow['creatorid'],
            $itemrow['creationdate']
        );
        array_push($listitems, $listitem);
    }

    // Return list
    $list = new MyList(
        $listrow['id'],
        $listrow['name'],
        $listrow['description'],
        $listrow['creatorid'],
        $listrow['color'],
        $listrow['iconid'],
        $listrow['currencyid'],
        $listrow['code'],
        $members,
        $admins,
        $listrow['lastupdated'],
        $listrow['creationdate'],
        $listitems
    );
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
    echo(print_r($list->jsonSerialize(), true));
?>