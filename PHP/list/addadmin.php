<?php
    include("../data/list.php");
    require_once("../database.php");

    $debugMode = false;

    if ($debugMode){
        $listid = 0;
        $userid = 0;
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

    $tsql = "select * from lists where id = ?";
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
    $listrow = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    $tsql = "select * from users where id = ?";
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

    // Check if user is already an admin
    $tsql = "SELECT * FROM listadmins WHERE listid = ? AND userid = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid, $userid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode);
        return;
    }
    $rescount = sqlsrv_num_rows($stmt);
    if ($rescount > 0){
        die("Error: User is already an admin of the list");
        return;
    }

    // Find id of new record
    $tsql = "SELECT * FROM listadmins";
    $stmt = sqlsrv_query($conn, $tsql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode);
        return;
    }
    $row_count = sqlsrv_num_rows($stmt);
    $newid = $row_count;

    // Insert new admin
    $tsql = "INSERT INTO listadmins (
        id,
        userid,
        listid,
        creationdate
        ) 
        VALUES
        (?, ?, ?, ?)";
    $var = array($newid, $userid, $listid, $serverdate);
    $stmt = sqlsrv_query($conn, $tsql, $var, array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
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
    $tsql = "SELECT * FROM listadmins WHERE listid = ?";
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
        $listrow['id'],
        $listrow['name'],
        $listrow['description'],
        $listrow['creatorid'],
        $listrow['color'],
        $listrow['iconid'],
        $listrow['code'],
        $members,
        $admins,
        $listrow['lastupdated'],
        $listrow['creationdate']
    );
    sqlsrv_free_stmt($stmt);
    echo(print_r($list->jsonSerialize(), true));
?>