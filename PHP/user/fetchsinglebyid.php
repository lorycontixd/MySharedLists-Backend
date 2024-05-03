<?php
    require_once('../errorcodes.php');
    require_once('../database.php');
    require_once('../data/user.php');

    $debugMode = false;

    if ($debugMode){
        $userid = 0;
    }else{
        $userid = $_POST["userid"];
    }

    $db = new Database();
    $conn = $db->get_connection();
    if($conn === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }

    $tsql = "select * from users where id = ?";
    $stmt = sqlsrv_query($conn, $tsql, array($userid), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    if ($stmt === false){
        $errorMsg = sqlsrv_errors()[0]['message'];
        $errorCode = sqlsrv_errors()[0]['code'];
        die("Error: " . $errorCode . " - " . $errorMsg);
        return;
    }
    $count = sqlsrv_num_rows($stmt);
    if ($count == 0){
        print_error(ErrorCodes::UserNotFoundError->value, "User doesn't exist or has been deleted");
        return;
    }
    $userrow = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    // Fetch user
    $user = new User(
        $userrow["id"],
        $userrow["roleid"],
        $userrow["username"],
        $userrow["email"],
        $userrow["password"],
        $userrow["firstname"],
        $userrow["lastname"],
        $userrow["iconurl"],
        $userrow["subscriptionplan"],
        $userrow["subscriptiondate"],
        $userrow["subscriptionenddate"],
        $userrow["subscriptionstatus"],
        $userrow["isonline"],
        $userrow["isdeleted"],
        $userrow["isvalidated"],
        $userrow["validationdate"],
        $userrow["validationcode"],
        $userrow["validationcodeexpiration"],
        $userrow["lastlogin"],
        $userrow["creationdate"],
        $userrow["lastupdated"]
    );

    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
    echo(print_r($user->jsonSerialize(), true));

?>