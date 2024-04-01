<?php
    include("../data/listitem.php");
    require_once("../database.php");

    $debugMode = false;

    if ($debugMode){
        $listid = 0;
        $itemname = "Test Item";
        $itemdescription = "This is a test item";
        $itemquantity = 1;
        $creatorid = 0;
    }else{
        $listid = $_POST["listid"];
        $itemname = $_POST["name"];
        $itemdescription = $_POST["description"];
        $itemquantity = $_POST["quantity"];
        $creatorid = $_POST["creatorid"];
    }

    $db = new Database();
    $conn = $db->get_connection();
    if($conn === false){
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode);
    }
    $serverdate = $db->get_server_date();

    // Row number as id
    $tsql = "select * from [dbo].[listitems]";
    $stmt = sqlsrv_query( $conn, "select * from listitems" , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET )); 
    if ($stmt === false){
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode);
        return;
    }
    $row_count = sqlsrv_num_rows($stmt);
    $itemid = $row_count;

    // Insert
    $tsql = "INSERT INTO listitems (
        id,
        name,
        description,
        quantity,
        listid,
        ischecked,
        creatorid,
        creationdate
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = sqlsrv_query($conn, $tsql, array(
        $itemid,
        $itemname,
        $itemdescription,
        $itemquantity,
        $listid,
        0,
        $creatorid,
        $serverdate
    ));

    if ($stmt === false){
        echo "a1";
        
        for ($errors = sqlsrv_errors(), $i = 0, $n = count($errors); $i < $n; $i++) {
            echo "SQLSTATE: ".$errors[$i]['SQLSTATE']."<br />";
            echo "code: ".$errors[$i]['code']."<br />";
            echo "message: ".$errors[$i]['message']."<br />";
        }
    }

    $item = new ListItem(
        $itemid,
        $itemname,
        $itemdescription,
        $itemquantity,
        $listid,
        0,
        $creatorid
    );
    sqlsrv_free_stmt($stmt);
    echo(print_r($item->jsonSerialize(), true));
?>