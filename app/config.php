<?php

    $database = "mysharedlists";
    $uid = "lorenzo.conti";
    $password = "Loriemichi19!";
    $server = "tcp:mysharedlists.database.windows.net,1433";
    $connectionInfo = array("UID" => $uid, "pwd" => $password, "Database" => $database, "LoginTimeout" => 30, "Encrypt" => 1, "TrustServerCertificate" => 0);

    $conn = sqlsrv_connect($server, $connectionInfo);
    if ($conn) {
        echo "Connection established.\n";
    } else {
        echo "Connection could not be established.\n";
        die(print_r(sqlsrv_errors(), true));
    }
?>