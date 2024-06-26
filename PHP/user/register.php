<?php  
    include "../database.php";
    include "../data/user.php";
    include "../errorcodes.php";

    $debugMode = false;

    if($debugMode){
        $user_code = 2;
        $userName = "lorenzo.conti$user_code";
        $firstName = "Lorenzo";
        $lastName = "Conti";
        $emailAddress = "testemail$user_code@email.com";
        $password = "Testpassword";
        
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

    // Add register constraints at server level (?)

    // Check if the user already exists
    // Check username first
    $tsql = "SELECT * FROM users WHERE username = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($userName), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $rescount = sqlsrv_num_rows($stmt);
    if ($rescount > 0){
        echo print_error(ErrorCodes::UserAlreadyExistsError->value, "A user with the same username already exists");
        return;
    }

    // Check email
    $tsql = "SELECT * FROM users WHERE email = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($emailAddress), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $rescount = sqlsrv_num_rows($stmt);
    if ($rescount > 0){
        echo print_error(ErrorCodes::UserAlreadyExistsError->value, "A user with the same email already exists");
        return;
    }

    // Get highest user id
    $stmt = sqlsrv_query( $conn, "SELECT MAX(id) as maxid FROM users" );
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    $useridtable = $row['maxid'] + 1;
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $verification_code = $database->generate_code('alpha', 8);
    $verification_code_expiration = $database->get_server_date_obj();
    $verification_code_expiration->modify('+7 day');
    $verification_code_expiration = $verification_code_expiration->format('Y-m-d H:i:s');

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
        $useridtable,
        $userName,
        $emailAddress,
        $hashed_password,
        $firstName,
        $lastName,
        $serverdate, // subscriptiondate
        $serverdate, // subscriptionenddate
        $serverdate, // validationdate
        $verification_code,
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
    $stmt = sqlsrv_query( $conn, "select * from users where id = ?" , array($useridtable), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
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