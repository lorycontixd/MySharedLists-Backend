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
    $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);
    // check if exists
    if ($row == null){
        echo print_error(ErrorCodes::UserNotFoundError->value, "User not found");
        return;
    }

    // verify user
    if ($row["username"] == $userName && password_verify($password, $row["password"]))
    {
        $user = new User($row["id"], $row["username"], $row["firstname"], $row["lastname"], $row["password"], $row["creationdate"], $row["lastupdated"]);
        $json = $user->jsonSerialize();
        echo(print_r($json, true));
    }else{
        if (sqlsrv_errors() != null){
            $errorMsg = sqlsrv_errors()[0]['message'];
            $errorCode = sqlsrv_errors()[0]['code'];
            die("Error: " . $errorCode . " - " . $errorMsg);
            return;
        }else{
            echo print_error(ErrorCodes::LoginFailedCredentials->value, "Login failed");
            return;
        }
    }
?>