<?php
    require_once "../database.php";
    require_once "../data/list.php";
    require_once "../errorcodes.php";

    $debugMode = false;
    
    if ($debugMode){
        $listName = "Test List";
        $listDescription = "This is a test list";
        $listOwner = 0;
        $colorCode = 0;
        $iconId = 0;
        $currencyId = 0;
    }else{
        $listName = $_POST["name"];
        $listDescription = $_POST["description"];
        $listOwner = $_POST["creator"];
        $colorCode = $_POST["colorcode"];
        $iconId = $_POST["iconid"];
        $currencyId = $_POST["currencyid"];
    }
    if ($iconId == null || $iconId < 0){
        $iconId = 0;
    }

    $db = new Database();
    $conn = $db->get_connection();
    if($conn === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $serverdate = $db->get_server_date();

    //// Count the number of rows in the table
    $stmt = sqlsrv_query( $conn, "select * from lists" , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET )); 
    if ($stmt === false) {
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $row_count = sqlsrv_num_rows( $stmt );  
    $listidtable = $row_count;

    // Check if user exists
    $stmt = sqlsrv_query( $conn, "select * from users where id = ?" , array($listOwner), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false) {
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $row_count = sqlsrv_num_rows( $stmt );
    if ($row_count == 0){
        print_error(ErrorCodes::UserNotFoundError->value, "User doesn't exist or has been deleted");
        return;
    }

    //// Insert the new list
    $tsql= "INSERT INTO lists (
        id,
        name,
        description,
        creatorid,
        color,
        iconid,
        currencyid,
        code,
        lastupdated,
        creationdate
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $code = $db->generate_code('alnum', 8); 
    $var = array($listidtable, $listName, $listDescription, $listOwner, $colorCode, $iconId, $currencyId, $code, $serverdate, $serverdate);
    $stmt = sqlsrv_query($conn, $tsql, $var, array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        echo "y";
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }

    //// Insert new list member
    // Get record id
    $stmt = sqlsrv_query( $conn, "select * from listmembers" , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false) {
        echo "x";
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $row_count = sqlsrv_num_rows( $stmt );
    $listmemberidtable = $row_count;

    // Insert new list member
    $tsql= "INSERT INTO listmembers (
        id,
        userid,
        listid,
        creationdate
        ) 
        VALUES
        (?, ?, ?, ?)";
    $var = array($listmemberidtable, $listOwner, $listidtable, $serverdate);
    $stmt = sqlsrv_query($conn, $tsql, $var);
    if ($stmt === false){
        echo "a";
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }

    //// Insert new list admin
    // Get record id
    $stmt = sqlsrv_query( $conn, "select * from listadmins" , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false) {
        echo "b";
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $row_count = sqlsrv_num_rows( $stmt );
    $listadminidtable = $row_count;

    // Insert new list admin
    $tsql= "INSERT INTO listadmins (
        id,
        userid,
        listid,
        creationdate
        ) 
        VALUES
        (?, ?, ?, ?)";
    $var = array($listadminidtable, $listOwner, $listidtable, $serverdate);
    $stmt = sqlsrv_query($conn, $tsql, $var);
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }

    // Return list
    $_POST['listid'] = $listidtable;
    sqlsrv_free_stmt($stmt);
    require_once("fetchsinglebyid.php");
?>