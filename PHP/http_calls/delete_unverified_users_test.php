<?php
    require_once('../database.php');
    require_once('../errorcodes.php');

    $db = new Database();
    $conn = $db->get_connection();
    if($conn === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $serverdate = $db->get_server_date_obj();
    $tsql = "delete from users where isvalidated = 0";
    $stmt = sqlsrv_query($conn, $tsql, array($serverdate));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $count = sqlsrv_rows_affected($stmt);
    
    $jsonArray = array();
    $jsonArray["affected_rows"] = $count;
    $jsonArray["error_code"] = 0;
    $jsonArray["date"] = $serverdate->format('Y-m-d H:i:s');
    echo json_encode($jsonArray, JSON_PRETTY_PRINT);
?>