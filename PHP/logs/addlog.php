<?php
    require_once('../errorcodes.php');
    require_once('../database.php'); 
    require_once('../data/log.php');

    $debugmode = false;

    if ($debugmode){
        $code = 0;
        $level = 0;
        $title = "Test log";
        $description = "This is a test log";
        $userid = 0;
        $source = "Test Source";
        $stacktrace = "Test Stacktrace";
        $devicename = "Test Device";
        $devicemodel = "Test Model";
        $devicetype = "Test Type";
        $deviceos = "Test OS";
    }else{
        $code = $_POST['code'];
        $level = $_POST['level'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $userid = $_POST['userid'];
        $source = $_POST['source'];
        $stacktrace = $_POST['stacktrace'];
        $devicename = $_POST['devicename'];
        $devicemodel = $_POST['devicemodel'];
        $devicetype = $_POST['devicetype'];
        $deviceos = $_POST['deviceos'];
    }

    $db = new Database();
    $conn = $db->get_connection();
    if ($conn === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $serverdate = $db->get_server_date();

    // Check if user exists
    $stmt = sqlsrv_query($conn, "select * from users where id = ?", array($userid), array("Scrollable" => SQLSRV_CURSOR_KEYSET));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $row_count = sqlsrv_num_rows($stmt);
    if ($row_count == 0){
        print_error(ErrorCodes::UserNotFoundError->value, "User doesn't exist or has been deleted");
        return;
    }

    // Count log id
    $stmt = sqlsrv_query($conn, "select * from logs", array(), array("Scrollable" => SQLSRV_CURSOR_KEYSET));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $row_count = sqlsrv_num_rows($stmt);
    $logidtable = $row_count;

    // Insert the new log
    $tsql = "INSERT INTO logs (
        id,
        code,
        level,
        title,
        description,
        userid,
        source,
        stacktrace,
        devicename,
        devicemodel,
        devicetype,
        deviceos,
        createddate
    ) VALUES (
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?
    )";
    $var = array($logidtable, $code, $level, $title, $description, $userid, $source, $stacktrace, $devicename, $devicemodel, $devicetype, $deviceos, $serverdate);
    $stmt = sqlsrv_query($conn, $tsql, $var, array("Scrollable" => SQLSRV_CURSOR_KEYSET));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }

    $log = new Log(
        $logidtable,
        $code,
        $level,
        $title,
        $description,
        $userid,
        $source,
        $stacktrace,
        $devicename,
        $devicemodel,
        $devicetype,
        $deviceos,
        $serverdate
    );
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);

    echo(print_r($log->newJsonSerialize(), true));
?>