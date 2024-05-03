<?php
    require_once ('../errorcodes.php');
    require_once('../database.php');

    $debugMode = false;

    if($debugMode){
        $userid = 7;
    }
    else{
        $userid = $_POST["userid"];
    }

    // Database connection
    $database = new Database();
    $conn = $database->get_connection();
    if ($conn === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $serverdate = $database->get_server_date();

    // Get user
    $tsql = "SELECT * FROM users WHERE id = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($userid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $rescount = sqlsrv_num_rows($stmt);
    if ($rescount == 0){
        print_error(ErrorCodes::UserNotFoundError->value, "User doesn't exist or has been deleted");
        return;
    }
    $userrow = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    // Generate new validation code
    $verification_code = $database->generate_code('alnum', 8);
    $verification_code_expiration = $database->get_server_date_obj();
    $verification_code_expiration->modify('+7 day');
    $verification_code_expiration = $verification_code_expiration->format('Y-m-d H:i:s');

    // Update user
    $tsql = "UPDATE users SET validationcode = ?, validationcodeexpiration = ? WHERE id = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($verification_code, $verification_code_expiration, $userid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }

    // Return user
    if ($debugMode){
        $_POST["userid"] = $userid;
    }
    require_once('fetchsinglebyid.php');

?>