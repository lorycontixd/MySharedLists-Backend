<?php  
    include "../database.php";
    include "../data/user.php";
    
    #$userName = "lorenzo.conti";
    #$firstName = "Lorenzo";
    #$lastName = "Conti";
    #$emailAddress = "loryconti1@gmail.com";
    #$password = "Loriemichi19!";
    #$hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $userName = $_POST["username"];
    $firstName = $_POST["firstname"];
    $lastName = $_POST["lastname"];
    $emailAddress = $_POST["email"];
    $password = $_POST["password"];
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
            for ($errors = sqlsrv_errors(), $i = 0, $n = count($errors); $i < $n; $i++) {
                echo "SQLSTATE: ".$errors[$i]['SQLSTATE']."<br />";
                echo "code: ".$errors[$i]['code']."<br />";
                echo "message: ".$errors[$i]['message']."<br />";

                // terminate
                return;
            }
        }
        $serverdate = $database->get_server_date();

        // Count the number of rows in the table
        $stmt = sqlsrv_query( $conn, "select * from users" , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET )); 
        if ($stmt === false) {
            die("Error1: Failed counting user rows");
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
            echo "";
            for ($errors = sqlsrv_errors(), $i = 0, $n = count($errors); $i < $n; $i++) {
                echo "SQLSTATE: ".$errors[$i]['SQLSTATE']."<br />";
                echo "code: ".$errors[$i]['code']."<br />";
                echo "message: ".$errors[$i]['message']."<br />";
            }
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
            die("Error3: " . $response);
        }
    }

    user_register($userName, $firstName, $lastName, $emailAddress, $password);
?>