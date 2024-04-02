<?php
    require_once("../database.php");
    require_once("../data/list.php");

    $debugMode = false;

    if ($debugMode){
        $listid = 0;
        $userid = 1;
    }else{
        $listid = $_POST["listid"];
        $userid = $_POST["userid"];
    }

    $db = new Database();
    $conn = $db->get_connection();
    if($conn === false){
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode);
    }
    $serverdate = $db->get_server_date();

    // Get list and check if it exists
    $tsql = "SELECT * FROM lists WHERE id = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode);
        return;
    }
    $rescount = sqlsrv_num_rows($stmt);
    if ($rescount == 0){
        die("Error: List doesn't exist or has been deleted");
        return;
    }
    $list = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    // Check if user is already a member of the list
    $tsql = "SELECT * FROM listmembers WHERE listid = ? AND userid = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid, $userid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode);
        return;
    }
    $rescount = sqlsrv_num_rows($stmt);
    if ($rescount > 0){
        die("Error: User is already a member of the list");
        return;
    }

    // Get id of new list member
    $tsql = "SELECT * FROM listmembers";
    $stmt = sqlsrv_query($conn, $tsql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode);
        return;
    }
    $row_count = sqlsrv_num_rows($stmt);
    $newuserid = $row_count;

    // Insert new list member
    $tsql = "INSERT INTO listmembers (id, userid, listid, creationdate) VALUES (?, ?, ?, ?)";
    $var = array($newuserid, $userid, $listid, $serverdate);
    $stmt = sqlsrv_query($conn, $tsql, $var);
    if ($stmt === false){
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode);
        return;
    }

    // Return list calling fetchsingle.php
    
    // In editData.php
    $data = array('listid' => $listid);
    $url = 'https://my-groceries.azurewebsites.net/PHP/list/fetchsingle.php';

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    echo $response;
    curl_close($ch);

    // In submitData.php
    $receivedData = $_POST;
?>