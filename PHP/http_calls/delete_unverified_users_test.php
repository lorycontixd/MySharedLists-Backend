<?php
    require_once('../database.php');
    require_once('../errorcodes.php');

    $errorCode = 0;
    $errorMsg = "";

    $db = new Database();
    $conn = $db->get_connection();
    if($conn === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
    }
    $serverdate = $db->get_server_date_obj();

    $deleted_users = array();
    $tsql = "select * from users where isvalidated = 0";
    $stmt = sqlsrv_query($conn, $tsql);
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
    }
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
        $deleted_users[] = $row;
    }
    $count = count($deleted_users);
    
    if ($count > 0){
        $tsql = "delete from users where isvalidated = 0";
        $stmt = sqlsrv_query($conn, $tsql, array($serverdate));
        if ($stmt === false){
            $errorMsg = sqlsrv_errors()[0]['message'];
            $errorCode = sqlsrv_errors()[0]['code'];
        }
        $del_count = sqlsrv_rows_affected($stmt);
        if ($del_count != $count){
            $errorMsg = "Mismatch between deleted users and affected rows";
            $errorCode  = -1;
        }
    }
    
    
    $jsonArray = array();
    $jsonArray["affected_rows"] = $count;
    $jsonArray["error_code"] = $errorCode;
    $jsonArray["error_message"] = $errorMsg;
    $jsonArray["date"] = $serverdate->format('Y-m-d H:i:s');
    $jsonArray["deleted_users"] = $deleted_users;
    
    echo json_encode($jsonArray, JSON_PRETTY_PRINT);
?>