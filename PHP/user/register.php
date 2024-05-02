<?php  
    include "../database.php";
    include "../data/user.php";
    #include "../errorcodes.php";

    $debugMode = false;

    if($debugMode){
        $userName = "lorenzo.conti";
        $firstName = "Lorenzo";
        $lastName = "Conti";
        $emailAddress = "testemail@email.com";
        $password = "testpassword";
        
    }else{
        $userName = $_POST["username"];
        $firstName = $_POST["firstname"];
        $lastName = $_POST["lastname"];
        $emailAddress = $_POST["email"];
        $password = $_POST["password"];
    }
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

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

    $verification_code = $database->generate_code('alpha', 8);
    $verification_code_expiration = $database->get_server_date();
    $verification_code_expiration->date_modify('+7 day');

    // Insert the new user
    $tsql= "INSERT INTO users (
        id,
        roleid,
        username,
        email,
        password,
        firstname,
        lastname,
        iconurl,
        subscriptionplan,
        subscriptiondate,
        subscriptionenddate,
        subscriptionstatus,
        isonline,
        isdeleted,
        isvalidated,
        validationdate,
        validationcode,
        validationcodeexpiration,
        lastlogin,
        lastupdated,
        creationdate
        )
        VALUES (
            ?,
            0,
            ?,
            ?,
            ?,
            ?,
            ?,
            '',
            0,
            ?, 
            ?, 
            1, 
            0, 
            0, 
            0, 
            ?, 
            ?,
            ?,
            ?, 
            ?, 
            ?
            )";
    $var = array(
        $row_count,
        $userName,
        $emailAddress,
        $hashed_password,
        $firstName,
        $lastName,
        $serverdate, // subscriptiondate
        $serverdate, // subscriptionenddate
        $serverdate, // validationdate
        $validation_code,
        $verification_code_expiration, // validationcodeexpiration
        $serverdate, // lastlogin
        $serverdate, // lastupdated
        $serverdate  // creationdate
    );
    $stmt = sqlsrv_query($conn, $tsql, $var);
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }

    // Finalize
    $stmt = sqlsrv_query( $conn, "select * from users where id = ?" , array($row_count), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    $userrow = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    // Check if the user was inserted
    if ($userrow == null){
        echo print_error(ErrorCodes::UserNotFoundError->value, "User not found after creation. Please contact the administrator.");
    }

    if($userrow['id'] == $row_count){
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
        $response = "User " . strval($row_count) . " not found";
        echo print_error(ErrorCodes::UserNotFoundError->value, $response);
        return;
    }
    sqlsrv_free_stmt($stmt);

?>