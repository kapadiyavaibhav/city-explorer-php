<?php
session_start();
include "db_connect.php";
//include "header.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $check = mysqli_query($conn, "SELECT * FROM user WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Email already registered!";
    } else {
        $query = "INSERT INTO user (email, password) VALUES ('$email', '$password')";
        if (mysqli_query($conn, $query)) {
            $success = "Registration successful! <a href='login.php'>Login here</a>";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
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
        .register-box {
            background: #fff;
            padding: 35px 40px;
            border-radius: 15px;
            box-shadow: 0px 8px 20px rgba(0,0,0,0.2);
            width: 360px;
            text-align: center;
        }
        .register-box h2 {
            margin-bottom: 25px;
            color: #333;
        }
        .form-group {
            text-align: left;
            margin-bottom: 18px;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #444;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
            outline: none;
            transition: 0.3s;
        }
        .form-group input:focus {
            border-color: #2575fc;
            box-shadow: 0px 0px 6px rgba(37, 117, 252, 0.4);
        }
        .btn-submit {
            width: 100%;
            background: #2575fc;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-submit:hover {
            background: #1b5dd8;
        }
        .message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 6px;
            font-size: 14px;
        }
        .error {
            background: #f8d7da;
            color: #a94442;
            border: 1px solid #f5c6cb;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>

<div class="register-box">
    <h2>Register</h2>

    <?php if (!empty($error)) { ?>
        <div class="message error"><?php echo $error; ?></div>
    <?php } ?>
    <?php if (!empty($success)) { ?>
        <div class="message success"><?php echo $success; ?></div>
    <?php } ?>

    <form method="post" action="">
        <div class="form-group">
            <label>Email (Username):</label>
            <input type="email" name="email" required>
        </div>

        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>

        <button type="submit" class="btn-submit">Register</button>
    </form>
    <p>If already have account?</p> <a href="login.php">login</a>
</div>

</body>
</html>
<?php // include "footer.php"; ?>
