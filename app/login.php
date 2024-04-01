<?php

include 'config.php';

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM userdetails where UserName = '$email' AND Password = '$password'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        session_start();
        $_SESSION['Login'] = true;
        $_SESSION['username'] = $row['Name'];
        header("Location: index.php");
    } else {
        echo "<script>alert(' Email or Password is wrong. ')</script>";
    }
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
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input name="submit" type="Submit" value="Login">
            <div class="signup_link">
                Not a Member ? <a href="signup.php">Signup</a>
            </div>
        </form>
    </div>
</body>

</html>