<?php
    require_once("../database.php");
    require_once("../data/list.php");

    $debugMode = false;

    if ($debugMode){
        $listcode = 0;
        $userid = 0;
    }else{
        $listcode = $_POST["code"];
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
    $tsql = "SELECT * FROM lists WHERE code = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listcode), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
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
    $listid = $list['id'];

    // Get user and check if it exists
    $tsql = "SELECT * FROM users WHERE id = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($userid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode);
        return;
    }
    $rescount = sqlsrv_num_rows($stmt);
    if ($rescount == 0){
        die("Error: User doesn't exist or has been deleted");
        return;
    }

    // Check if user is already a member of the list
    $tsql = "SELECT * FROM listmembers WHERE code = ? AND userid = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listcode, $userid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
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

    // Return list

    // Fetch members
    $tsql = "SELECT * FROM listmembers WHERE listid = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode);
        return;
    }
    $members = array();
    while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
        array_push($members, $row['userid']);
    }

    // Fetch admins
    $tsql = "SELECT * FROM listadmins WHERE listid= ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode);
        return;
    }
    $admins = array();
    while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
        array_push($admins, $row['userid']);
    }

    // Return list
    $list = new MyList(
        $list['id'],
        $list['name'],
        $list['description'],
        $list['creatorid'],
        $list['color'],
        $list['iconid'],
        $list['code'],
        $members,
        $admins,
        $list['lastupdated'],
        $list['creationdate']
    );
    sqlsrv_free_stmt($stmt);
    echo(print_r($list->jsonSerialize(), true));
?>