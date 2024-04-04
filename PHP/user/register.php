<?php  
    include "../database.php";
    include "../data/user.php";

    $debugMode = false;

    if($debugMode){
        $userName = "testusername";
        $firstName = "testfirstname";
        $lastName = "testlastname";
        $emailAddress = "testemail";
        $password = "testpassword";
    }else{
        $userName = $_POST["username"];
        $firstName = $_POST["firstname"];
        $lastName = $_POST["lastname"];
        $emailAddress = $_POST["email"];
        $password = $_POST["password"];
    }
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    function user_register(
        $userName,
        $firstName,
        $lastName,
        $emailAddress,
        $password){

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

        // Count the number of rows in the table
        $stmt = sqlsrv_query( $conn, "select * from users" , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET )); 
        if ($stmt === false) {
            $errorMsg = sqlsrv_errors()[0]['message'];
            $errorCode = sqlsrv_errors()[0]['code'];
            die("Error: " . $errorCode . " - " . $errorMsg);
            return;
        }
        $row_count = sqlsrv_num_rows( $stmt );  
        $useridtable = $row_count;
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user
        $tsql= "INSERT INTO users (
            id,
            username,
            firstname,
            lastname,
            password,
            creationdate,
            lastupdated
            ) 
            VALUES
            (?, ?, ?, ?, ?, ?, ?)";
        $var = array($useridtable, $userName, $firstName, $lastName, $hashed_password, $serverdate, $serverdate);
        $stmt = sqlsrv_query($conn, $tsql, $var);
        if ($stmt === false){
            $errorMsg = sqlsrv_errors()[0]['message'];
            $errorCode = sqlsrv_errors()[0]['code'];
            die("Error: " . $errorCode . " - " . $errorMsg);
            return;
        }

        // Finalize
        $stmt = sqlsrv_query( $conn, "select * from users where id = ?" , array($row_count), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        if($row['id'] == $row_count){
            $user = new User(
                $row['id'],
                $row['username'],
                $row['firstname'],
                $row['lastname'],
                $row['password'],
                $row['creationdate'],
                $row['lastupdated']
            );
            $json = $user->jsonSerialize();
            echo(print_r($json, true));
        }else{
            $response = "User(" . strval($row_count) . ") not found";
            die("Error: " . $response);
        }
    }

    user_register($userName, $firstName, $lastName, $emailAddress, $password);
?>