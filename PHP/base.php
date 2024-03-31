<?php
    define('ROOT_PATH', dirname(__DIR__) . '/PHP/');

    include ROOT_PATH . "/database.php";
    include ROOT_PATH . "/user/register.php";

    $userName = "lorenzo.conti";
    $firstName = "Lorenzo";
    $lastName = "Conti";
    $emailAddress = "loryconti1@gmail.com";
    $password = "Lori";

    $db = new Database();
    user_register($db, $userName, $firstName, $lastName, $emailAddress, $password);
?>