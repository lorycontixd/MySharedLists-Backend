<?php
    include '../database.php';
    include '../errorcodes.php';
    include '../data/user.php';

    #$userName = "lorenzo.conti";
    #$password = "testpassword";

    $debugmode = false;

    if ($debugmode){
        $userName = "lorenzo.conti";
        $password = "testpassword";
    }else{    
        $userName = $_POST['username'];
        $password = $_POST['password'];
    }
    $hashedpass = password_hash($password, PASSWORD_DEFAULT);

    $database = new Database();
    $conn = $database->get_connection();
    if($conn === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }

    $tsql = "SELECT * FROM users WHERE username = ?";
    $var = array($userName);

    $stmt = sqlsrv_query($conn, $tsql, $var);
    if ($stmt === false){
        if (sqlsrv_errors() != null){
            $errorMsg = sqlsrv_errors()[0]['message'];
            $errorCode = sqlsrv_errors()[0]['code'];
            die("Error: " . $errorCode . " - " . $errorMsg);
            return;
        }else{
            echo print_error(ErrorCodes::UserNotFoundError->value, "User not found");
            return;
        }
    }
    $userrow = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);
    // check if exists
    if ($userrow == null){
        echo print_error(ErrorCodes::UserNotFoundError->value, "User not found");
        return;
    }

    // verify user
    if ($userrow["username"] == $userName && password_verify($password, $userrow["password"]))
    {
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
    }else{
        if (sqlsrv_errors() != null){
            $errorMsg = sqlsrv_errors()[0]['message'];
            $errorCode = sqlsrv_errors()[0]['code'];
            die("Error: " . $errorCode . " - " . $errorMsg);
            return;
        }else{
            echo print_error(ErrorCodes::LoginFailedCredentials->value, "Username or password is incorrect. Please try again.");
            return;
        }
    }
?>