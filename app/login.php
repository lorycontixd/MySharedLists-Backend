<?php

require_once 'config.php';

$url = 'https://mysharedlists.azurewebsites.net/PHP/user/login.php';

if (isset($_POST['submit'])) {
    // post to url my-groceries.azurewebsites.net/PHP/user/login.php
    $username = $_POST['username'];
    $password = $_POST['password'];

    $fields = array(
        'username' => $username,
        'password' => $password,
     );

    $postvars = http_build_query($fields);
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, count($fields));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
    // execute post
    $result = curl_exec($ch);
    echo $result;

    // close connection
    curl_close($ch);

}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        .center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        input {
            width: 80%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type='Submit'] {
            cursor: pointer;
            width: 50%;
            text-align: center;
        }
    </style>
    <title>Login Form</title>
</head>

<body>
    <div class="center">
        <h1>Login</h1>
        <form action="" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input name="submit" type="Submit" value="Login">
            <div class="signup_link">
                Not a Member ? <a href="signup.php">Signup</a>
            </div>
        </form>
    </div>
</body>

</html>