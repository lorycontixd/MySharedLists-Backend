<?php
    require_once('../database.php');

    $debugMode = false;

    if ($debugMode){
        $userid = 0;
        $modeid = 0;
        if ($modeid == 0){
            $mode = "bug";
        }else{
            $mode = "suggestion";
        }
        $title = "Test Title";
        $description = "Test Description";
    }else{
        $userid = $_POST["userid"];
        $modeid = $_POST["modeid"];
        if ($modeid == 0){
            $mode = "bug";
        }else{
            $mode = "suggestion";
        }
        $title = $_POST["title"];
        $description = $_POST["description"];
    }

    $db = new Database();
    $conn = $db->get_connection();
    if($conn === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorMsg . " (" . $errorCode . ")");
        return;
    }
    $serverdate = $db->get_server_date();

    // Check user exist
    $tsql = "select * from users where id = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($userid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorMsg . " (" . $errorCode . ")");
        return;
    }
    $rescount = sqlsrv_num_rows($stmt);
    if ($rescount == 0){
        die("Error: User doesn't exist or has been deleted");
        return;
    }
    $userrow = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    // Get suggestion id
    $tsql = "SELECT * FROM suggestions";
    $stmt = sqlsrv_query($conn, $tsql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorMsg . " (" . $errorCode . ")");
        return;
    }
    $suggestionid = sqlsrv_num_rows($stmt);


    // Insert suggestion
    $tsql = "INSERT into suggestions (id, userid, modeid, mode, title, description, creationdate) values (?, ?, ?, ?, ?, ?, ?)";
    $var = array($suggestionid, $userid, $modeid, $mode, $title, $description, $serverdate);
    $stmt = sqlsrv_query($conn, $tsql, $var);
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorMsg . " (" . $errorCode . ")");
        return;
    }

    echo "Success";
?>