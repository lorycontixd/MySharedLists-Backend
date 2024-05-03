<?php

include "config.php";

session_start();

$sql = "SELECT * FROM users where username = '$_SESSION[username]'";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    if (!isset($_SESSION["Login"]) && $_SESSION["Login"] == false) {
        header("Location: login.php");
    }
} else {
    header("Location: login.php");
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome <?php echo "$_SESSION[username]"; ?> </title>
    <style>
        .logoutBtn {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }

        .logoutBtn:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <h1>Welcome <?php echo "$_SESSION[username]"; ?></h1>
    <a class="logoutBtn" href="logout.php">Logout</a>
</body>

</html>