<?php
    require_once('../database.php');

    $debugMode = false;
    if ($debugMode){
        $listcode = "AAAA";
        $userid = 0;
    }else{
        $listcode = $_POST['code'];
        $userid = $_POST['userid'];
    }

    $db = new Database();
    $conn = $db->get_connection();
    if ($conn === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }

    // Check if user exists
    $tsql = "SELECT * FROM users WHERE id = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($userid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $rescount = sqlsrv_num_rows($stmt);
    if ($rescount == 0){
        die("Error: User doesn't exist or has been deleted");
        return;
    }
    $userrow = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    // Check if list exists, fetch if it does
    $tsql = "SELECT * FROM lists WHERE code = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listcode), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $rescount = sqlsrv_num_rows($stmt);
    if ($rescount == 0){
        die("Error: List doesn't exist or has been deleted");
        return;
    }
    $listrow = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    // Check if user is a member of the list
    $tsql = "SELECT * FROM listmembers WHERE listid = ? AND userid = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid, $userid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $rescount = sqlsrv_num_rows($stmt);
    if ($rescount == 0){
        die("Error: User is not a member of the list");
        return;
    }

    // Remove user from list
    $tsql = "DELETE FROM listmembers WHERE listid = ? AND userid = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid, $userid));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }

    echo $listrow['id'];
?>