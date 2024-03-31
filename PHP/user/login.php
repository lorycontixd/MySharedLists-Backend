<?php
    include '../database.php';
    include '../errorcodes.php';
    include '../data/user.php';

    #$userName = "lorenzo.conti";
    #$password = "testpassword";

    $userName = $_POST['username'];
    $password = $_POST['password'];
    $hashedpass = password_hash($password, PASSWORD_DEFAULT);
    
    $database = new Database();
    $conn = $database->get_connection();
    echo "hi";
    if($conn === false){
        for ($errors = sqlsrv_errors(), $i = 0, $n = count($errors); $i < $n; $i++) {
            echo "SQLSTATE: ".$errors[$i]['SQLSTATE']."<br />";
            echo "code: ".$errors[$i]['code']."<br />";
            echo "message: ".$errors[$i]['message']."<br />";

            // terminate
            return;
        }
    }

    $tsql = "SELECT * FROM users WHERE username = ?";
    $var = array($userName);

    $stmt = sqlsrv_query($conn, $tsql, $var);
    if ($stmt === false){
        if (sqlsrv_errors() != null){
            for ($errors = sqlsrv_errors(), $i = 0, $n = count($errors); $i < $n; $i++) {
                echo "SQLSTATE: ".$errors[$i]['SQLSTATE']."<br />";
                echo "code: ".$errors[$i]['code']."<br />";
                echo "message: ".$errors[$i]['message']."<br />";
            }
        }else{
            echo "Error: Code " . ErrorCodes::UserNotFoundError->value;
        }
    }

    $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);
    if ($row["username"] == $userName && password_verify($password, $row["password"]))
    {
        $user = new User($row["id"], $row["username"], $row["firstname"], $row["lastname"], $row["password"], $row["creationdate"], $row["lastupdated"]);
        $json = $user->jsonSerialize();
        echo(print_r($json, true));
    }else{
        if (sqlsrv_errors() != null){
            for ($errors = sqlsrv_errors(), $i = 0, $n = count($errors); $i < $n; $i++) {
                echo "SQLSTATE: ".$errors[$i]['SQLSTATE']."<br />";
                echo "code: ".$errors[$i]['code']."<br />";
                echo "message: ".$errors[$i]['message']."<br />";
            }
        }else{
            echo "Error: Code " . ErrorCodes::PasswordMismatchError->value;
        }
    }
?>