<?php
    require_once "../data/list.php";
    require_once "../data/user.php";
    require_once "../data/listitem.php";
    require_once "../database.php";

    $debugMode = false;

    if ($debugMode){
        $userid = 0;
    }else{
        $userid = $_POST["userid"];
    }

    $db = new Database();
    $conn = $db->get_connection();
    if($conn === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }

    $tsql = "select * from [dbo].[lists] where id in (select distinct listid from [dbo].[listmembers] where userid = ?)";
    $stmt = sqlsrv_query($conn, $tsql, array($userid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $listcount = sqlsrv_num_rows($stmt);

    
    // Fetch
    while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){            
        $listid = $row['id'];
        //echo "List ID: " . $listid . "<br />";
        // fetch members
        $tsql = "select userid from [dbo].[listmembers] where listid = ?";
        $stmt2 = sqlsrv_query($conn, $tsql, array($row['id']));
        if ($stmt2 === false){
            $errorMsg = sqlsrv_errors()[0]['message'];
            $errorCode = sqlsrv_errors()[0]['code'];
            die("Error: " . $errorCode . " - " . $errorMsg);
            return;
        }
        $members = array();
        while($row2 = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)){
            array_push($members, $row2['userid']);
        }

        // fetch admins
        $tsql = "select userid from [dbo].[listadmins] where listid = ?";
        $stmt2 = sqlsrv_query($conn, $tsql, array($row['id']));
        if ($stmt2 === false){
            $errorMsg = sqlsrv_errors()[0]['message'];
            $errorCode = sqlsrv_errors()[0]['code'];
            die("Error: " . $errorCode . " - " . $errorMsg);
            return;
        }
        $admins = array();
        while($row2 = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)){
            array_push($admins, $row2['userid']);
        }

        // fetch items
        $tsql = "select * from listitems where listid = ?";
        $stmt2 = sqlsrv_query($conn, $tsql, array($row['id']), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        if ($stmt2 === false){
            $errorMsg = sqlsrv_errors()[0]['message'];
            $errorCode = sqlsrv_errors()[0]['code'];
            die("Error: " . $errorCode . " - " . $errorMsg);
            return;
        }
        $listitems = array();
        while($itemrow = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)){
            $item = new ListItem(
                $itemrow['id'],
                $itemrow['listid'],
                $itemrow['name'],
                $itemrow['description'],
                $itemrow['quantity'],
                $itemrow['price'],
                $itemrow['brand'],
                $itemrow['listid'],
                $itemrow['ischecked'],
                $itemrow['creatorid'],
                $itemrow['creationdate']
            );
            array_push($listitems, $item);
        }

        // free statements
        sqlsrv_free_stmt($stmt2);

        $l = new MyList(
            $row['id'],
            $row['name'],
            $row['description'],
            $row['creatorid'],
            $row['color'],
            $row['iconid'],
            $row['currencyid'],
            $row['code'],
            $members,
            $admins,
            $row['lastupdated'],
            $row['creationdate'],
            $listitems
        );
        if ($l != null){
            $json = $l->jsonSerialize();
            echo(print_r($json, true)) . "<br />";
        }
    }
?>