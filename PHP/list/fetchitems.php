<?php
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

    $tsql = "select * from [dbo].[listitems] where listid = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    
    $items = array();
    while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
        $item = new ListItem($row['id'], $row['name'], $row['description'], $row['quantity'], $row['listid'], $row['ischecked'], $row['creatorid'], $row['creationdate']);
        if ($item != null){
            echo print_r($item->newJsonSerialize(), true) . "<br />";
        }
    }

    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
?>