<?php
    include "../database.php";
    include "../data/list.php";

    $debugMode = false;
    
    if ($debugMode){
        $listName = "Test List";
        $listDescription = "This is a test list";
        $listOwner = 0;
        $colorCode = 0;
        $iconId = 0;
    }else{
        $listName = $_POST["name"];
        $listDescription = $_POST["description"];
        $listOwner = $_POST["creator"];
        $colorCode = $_POST["colorCode"];
        $iconId = $_POST["iconId"];
    }
    
    if ($iconId < 0){
        $iconId = 0;
    }
    
    //// Check if the color code is valid, if not set it to 0
    if ($colorCode == null){
        $colorCode = 0;
    }else{
        if (!is_int($colorCode)){
            $colorCode = 0;
        }else{
            if ($colorCode < 0 || $colorCode > 16777215){
                $colorCode = 0;
            }
        }
    }

    function create_list($listName, $listDescription, int $listOwner, int $colorCode = 0, int $iconId = 0){
        $db = new Database();
        $conn = $db->get_connection();
        if($conn === false){
            $errorCode = sqlsrv_errors()[0]['code'];
            die("Error: " . $errorCode);
        }
        $serverdate = $db->get_server_date();

        //// Count the number of rows in the table
        $stmt = sqlsrv_query( $conn, "select * from lists" , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET )); 
        if ($stmt === false) {
            $errorCode = sqlsrv_errors()[0]['code'];
            die("Error: " . $errorCode);
        }
        $row_count = sqlsrv_num_rows( $stmt );  
        $listidtable = $row_count;

        // Check if user exists
        $stmt = sqlsrv_query( $conn, "select * from users where id = ?" , array($listOwner), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        if ($stmt === false) {
            $errorCode = sqlsrv_errors()[0]['code'];
            die("Error: " . $errorCode);
            return;
        }
        $row_count = sqlsrv_num_rows( $stmt );
        if ($row_count == 0){
            die("Error: User " . $listOwner . " doesn't exist or has been deleted");
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
            code,
            lastupdated,
            creationdate
            ) 
            VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $code = $db->generate_code('alnum', 8);
        $var = array($listidtable, $listName, $listDescription, $listOwner, $colorCode, $iconId, $code, $serverdate, $serverdate);
        $stmt = sqlsrv_query($conn, $tsql, $var);
        if ($stmt === false){
            $errorCode = sqlsrv_errors()[0]['code'];
            die("Error: " . $errorCode);
            return;
        }

        //// Insert new list member
        // Get record id
        $stmt = sqlsrv_query( $conn, "select * from listmembers" , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        if ($stmt === false) {
            $errorCode = sqlsrv_errors()[0]['code'];
            die("Error: " . $errorCode);
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
            $errorCode = sqlsrv_errors()[0]['code'];
            die("Error: " . $errorCode);
            return;
        }

        //// Insert new list admin
        // Get record id
        $stmt = sqlsrv_query( $conn, "select * from listadmins" , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        if ($stmt === false) {
            $errorCode = sqlsrv_errors()[0]['code'];
            die("Error: " . $errorCode);
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
            $errorCode = sqlsrv_errors()[0]['code'];
            die("Error: " . $errorCode);
            return;
        }

        //// Finalize
        $stmt = sqlsrv_query( $conn, "select * from lists where id = ?" , array($row_count), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        if($row['id'] == $row_count){
            $l = new MyList(
                $row['id'],
                $row['name'],
                $row['description'],
                $row['creatorid'],
                $row['color'],
                $row['iconid'],
                $row['code'],
                array($listOwner),
                array($listOwner),
                $row['creationdate'],
                $row['lastupdated']
            );
            $json = $l->jsonSerialize();
            echo(print_r($json, true));
        }else{
            $response = "List(" . strval($row_count) . ") not found";
            die("Error: " . $response);
        }
    }
    
    create_list($listName, $listDescription, $listOwner, $colorCode, $iconId);
?>