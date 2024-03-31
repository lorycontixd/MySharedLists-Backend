<?php
    include "../data/list.php";
    include "../data/user.php";
    include "../database.php";

    $userid = 0;
    //$userid = $_POST["userid"];

    function fetch_lists_by_user(int $userid){
        $db = new Database();
        $conn = $db->get_connection();
        if($conn === false){
            echo "a-1";
            $errorCode = sqlsrv_errors()[0]['code'];
            die("Error: " . $errorCode);
        }

        $tsql = "select * from [dbo].[lists] where id in (select distinct listid from [dbo].[listmembers] where userid = ?)";
        $stmt = sqlsrv_query($conn, $tsql, array($userid));
        if ($stmt === false){
            echo "a0";
            $errorCode = sqlsrv_errors()[0]['code'];
            die("Error: " . $errorCode);
            return;
        }

        
        
        
        // fetch
        while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){            
            // fetch members
            $tsql = "select userid from [dbo].[listmembers] where listid = ?";
            $stmt2 = sqlsrv_query($conn, $tsql, array($row['id']));
            if ($stmt2 === false){
                $errorCode = sqlsrv_errors()[0]['code'];
                die("Error: " . $errorCode);
                return;
            }
            $members = array();
            while($row2 = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)){
                array_push($members, $row2['userid']);
            }

            // fetch admins
            $tsql = "select userid from [dbo].[listadmins] where listid = ?";
            $stmt2 = sqlsrv_query($conn, $tsql, array($row['id']));
            if ($stmt2 === false){
                $errorCode = sqlsrv_errors()[0]['code'];
                die("Error: " . $errorCode);
                return;
            }
            $admins = array();
            while($row2 = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)){
                array_push($admins, $row2['userid']);
            }

            // free statements
            sqlsrv_free_stmt($stmt2);

            $l = new MyList(
                $row['id'],
                $row['name'],
                $row['description'],
                $row['creatorid'],
                $row['color'],
                $row['code'],
                $members,
                $admins,
                $row['creationdate'],
                $row['lastupdated']
            );
            if ($l != null){
                $json = $l->jsonSerialize();
                echo(print_r($json, true)) . "<br />";
            }
        }
    }

    $lists = fetch_lists_by_user($userid);
    echo json_encode($lists);
?>