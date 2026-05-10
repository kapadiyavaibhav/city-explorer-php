<?php
session_start();
include "db_connect.php";
//include "header.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM user WHERE email='$email'");
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        header("Location: redirect.php");
        exit();
    } else {
        echo "<p class='error'>Invalid email or password!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #6a11cb, #fbfbfcff);
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-box {
            background: #fff;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0px 8px 20px rgba(0,0,0,0.2);
            width: 350px;
            text-align: center;
        }
        .login-box h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .login-box label {
            display: block;
            margin-bottom: 8px;
            text-align: left;
            font-weight: bold;
            color: #444;
        }
        .login-box input {
            width: 100%;
            padding: 10px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 8px;
            outline: none;
            font-size: 14px;
        }
        .login-box input:focus {
            border-color: #2575fc;
            box-shadow: 0px 0px 6px rgba(37, 117, 252, 0.5);
        }
        .login-box button {
            width: 100%;
            background: #2575fc;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s ease;
        }
        .login-box button:hover {
            background: #1b5dd8;
        }
        .login-box a {
            display: inline-block;
            margin-top: 15px;
            color: #2575fc;
            text-decoration: none;
            font-size: 14px;
        }
        .login-box a:hover {
            text-decoration: underline;
        }
        .error {
            color: red;
            margin-bottom: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Login</h2>
    <form method="post" action="">
        <label>Email (Username):</label>
        <input type="email" name="email" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>
    </form>
    <a href="register.php">Register</a>
</div>

</body>
</html>
