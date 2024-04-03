<?php
    require_once('../database.php');

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
        die("Error: " . $errorMsg . " (" . $errorCode . ")");
        return;
    }
    
    // Delete list items
    $tsql = "DELETE FROM listitems WHERE listid = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorMsg . " (" . $errorCode . ")");
        return;
    }

    // Delete list members
    $tsql = "DELETE FROM listmembers WHERE listid = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorMsg . " (" . $errorCode . ")");
        return;
    }

    // Delete list admins
    $tsql = "DELETE FROM listadmins WHERE listid = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorMsg . " (" . $errorCode . ")");
        return;
    }

    // Delete list
    $tsql = "DELETE FROM lists WHERE id = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorMsg . " (" . $errorCode . ")");
        return;
    }
    
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
    echo "Success";
?>