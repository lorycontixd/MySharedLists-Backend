<?php
    include "../data/list.php";
    include "../data/user.php";
    include "../database.php";

    //$userid = 0;
    $userid = $_POST["userid"];

    function fetch_lists_by_user(int $userid){
        $db = new Database();
        $conn = $db->get_connection();
        if($conn === false){
            $errorCode = sqlsrv_errors()[0]['code'];
            die("Error: " . $errorCode);
        }

        $tsql = "select * from [dbo].[lists] where id in (select distinct listid from [dbo].[listmembers] where userid = ?)";
        $stmt = sqlsrv_query($conn, $tsql, array($userid));
        if ($stmt === false){
            $errorCode = sqlsrv_errors()[0]['code'];
            die("Error: " . $errorCode);
            return;
        }

        $lists = array();
        while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
            $l = new MyList(
                $row['id'],
                $row['name'],
                $row['description'],
                $row['creatorid'],
                $row['color'],
                $row['code'],
                $row['creationdate'],
                $row['lastupdated']
            );
            $json = $l->jsonSerialize();
            echo(print_r($json, true)) . "<br />"; 
        }
    }

    $lists = fetch_lists_by_user($userid);
    echo json_encode($lists);
?>