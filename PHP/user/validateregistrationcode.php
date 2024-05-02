<?php
    include "../database.php";
    include "../data/user.php";
    include "../errorcodes.php";

    $debugMode = false;

    if($debugMode){
        $userid = 0;
        $code = "123456";
    }else{
        $userid = $_POST["userid"];
        $code = $_POST["code"];
    }

    // Database connection
    $database = new Database();
    $conn = $database->get_connection();
    if($conn === false){
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
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    $uservalidationcode = $row["validationcode"];
    $validationcodeexpiration = $row["validationcodeexpiration"];

    if ($serverdate > $validationcodeexpiration){
        print_error(ErrorCodes::ValidationCodeExpired->value, "Validation code has expired");
        return;
    }

    if ($uservalidationcode != $code){
        print_error(ErrorCodes::InvalidValidationCode->value, "Invalid validation code");
        return;
    }

    // Update user
    $tsql = "UPDATE users SET validationdate = ?, isvalidated = 1 WHERE id = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($serverdate, $userid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }

    // Return user
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
        print_error(ErrorCodes::UserNotFoundError->value, "User not found after validation");
        return;
    }
    $userrow = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    $user = new User(
        $userrow['id'],
        $userrow['roleid'],
        $userrow['username'],
        $userrow['email'],
        $userrow['password'],
        $userrow['firstname'],
        $userrow['lastname'],
        $userrow['iconurl'],
        $userrow['subscriptionplan'],
        $userrow['subscriptiondate'],
        $userrow['subscriptionenddate'],
        $userrow['subscriptionstatus'],
        $userrow['isonline'],
        $userrow['isdeleted'],
        $userrow['isvalidated'],
        $userrow['validationdate'],
        $userrow['validationcode'],
        $userrow['validationcodeexpiration'],
        $userrow['lastlogin'],
        $userrow['lastupdated'],
        $userrow['creationdate']
    );
    $json = $user->jsonSerialize();
    echo(print_r($json, true));
?>