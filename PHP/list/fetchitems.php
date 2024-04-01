<?php
    include("../data/listitem.php");
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
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode);
    }

    $tsql = "select * from [dbo].[listitems] where listid = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid));
    if ($stmt === false){
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode);
        return;
    }
    
    $items = array();
    while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
        $item = new ListItem($row['id'], $row['name'], $row['description'], $row['quantity'], $row['listid'], $row['ischecked'], $row['creatorid']);
        if ($item != null){
            echo print_r($item->jsonSerialize(), true) . "<br />";
        }
    }

    sqlsrv_free_stmt($stmt);
?>