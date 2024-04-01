<?php
    require_once("../database.php");

    $debugMode = false;

    if ($debugMode){
        $itemid = 0;
        $listid = 0;
        $ischecked = true;
    }else{
        $itemid = $_POST["itemid"];
        $listid = $_POST["listid"];
        $ischecked = $_POST["ischecked"];
    }

    $db = new Database();
    $conn = $db->get_connection();
    if($conn === false){
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode);
    }

    $tsql = "UPDATE listitems SET ischecked = ? WHERE listid = ? AND id = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($ischecked, $listid, $itemid));
    if ($stmt === false){
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode);
        return;
    }

    $tsql = "SELECT * FROM listitems WHERE listid = ? AND id = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid, $itemid));
    if ($stmt === false){
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode);
        return;
    }

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    $item = new ListItem($row['id'], $row['name'], $row['description'], $row['quantity'], $row['listid'], $row['ischecked'], $row['creatorid']);
    echo(print_r($item->jsonSerialize(), true));
    sqlsrv_free_stmt($stmt);
?>