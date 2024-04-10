<?php
    require_once('../errorcodes.php');
    require_once('../database.php');
    require_once('../data/listinvitation.php');

    $debugMode = false;

    if ($debugMode){
        $id = 0;
    }else{
        $id = $_POST['id'];
    }

    $db = new Database();
    $conn = $db->get_connection();
    if($conn === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }

    $tsql = "delete from listinvitations where id = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($id));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }

    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
    echo("Success");
?>