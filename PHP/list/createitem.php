<?php
    require_once("../data/listitem.php");
    require_once("../database.php");
    require_once("../errorcodes.php");

    $debugMode = false;

    if ($debugMode){
        $listid = 0;
        $itemname = "Test Item";
        $itemdescription = "This is a test item";
        $itemquantity = 1;
        $itemprice = 0.0;
        $itembrand = "Test Item brand";
        $creatorid = 0;
    }else{
        $listid = $_POST["listid"];
        $itemname = $_POST["name"];
        $itemdescription = $_POST["description"];
        $itemquantity = $_POST["quantity"];
        $itemprice = $_POST["price"];
        $itembrand = $_POST["brand"];
        $creatorid = $_POST["creatorid"];
    }
    $itemprice = floatval($itemprice);

    $db = new Database();
    $conn = $db->get_connection();
    if($conn === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $serverdate = $db->get_server_date();

    // Check if list exists
    $tsql = "SELECT * FROM lists WHERE id = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($listid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET )); 
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $rescount = sqlsrv_num_rows($stmt);
    if ($rescount == 0){
        print_error(ErrorCodes::ListNotFoundError->value, "List doesn't exist or has been deleted");
        return;
    }

    // Row number as id
    $tsql = "select * from [dbo].[listitems]";
    $stmt = sqlsrv_query( $conn, "select * from listitems" , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET )); 
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $row_count = sqlsrv_num_rows($stmt);
    $itemid = $row_count;

    // List order value is last item's order + 1
    $tsql = "SELECT TOP 1 * FROM listitems WHERE listid = ? ORDER BY listorder DESC";
    $stmt = sqlsrv_query($conn, $tsql, array($listid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $rescount = sqlsrv_num_rows($stmt);
    if ($rescount == 0){
        $itemorder = 0;
    }else{
        $item = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        $itemorder = $item['listorder'] + 1;
    }

    // Insert
    $tsql = "INSERT INTO listitems (
        id,
        listorder,
        name,
        description,
        quantity,
        price,
        brand,
        listid,
        ischecked,
        creatorid,
        creationdate
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = sqlsrv_query($conn, $tsql, array(
        $itemid,
        $itemorder,
        $itemname,
        $itemdescription,
        $itemquantity,
        $itemprice,
        $itembrand,
        $listid,
        0,
        $creatorid,
        $serverdate
    ));

    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }

    // Return list
    if ($debugMode){
        $_POST['listid'] = $listid;
    }
    require_once("fetchsinglebyid.php");
?>