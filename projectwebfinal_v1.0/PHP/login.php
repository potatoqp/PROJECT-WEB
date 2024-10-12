<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
if (isset($_SESSION["user"])) {
    header("Location: loggedcivilian.php");
}

?>

<!DOCTYPE html>

<html>
    <head>
        <meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
        <title>Login</title>
		<link rel="stylesheet" href="../CSS/MY_stylesheet.css">
        <link rel="stylesheet" href="../CSS/MY_stylesheetLogin.css">
    </head>

    <body>
       
        <?php
        if (isset($_POST["login"])){
            $newUsername = $_POST["username"];
            $newPassword = $_POST["password"];
            require_once "db.php";
            $sql = "SELECT u.id, u.username, u.password, a.id AS admin_id, v.id AS volunteer_id
                    FROM users u
                    LEFT JOIN admins a ON u.id = a.user_id
                    LEFT JOIN volunteers v ON u.id = v.user_id
                    WHERE u.username = '$newUsername'";
            $result = mysqli_query($conn, $sql);
            $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
            if ($user) {
                if ($newPassword === $user["password"]){
                    session_start();
                    $_SESSION["user"] = $user["username"];
					$_SESSION["user_id"] = $user["id"];
                    if (!is_null($user["admin_id"])) {
                        $_SESSION['user_role'] = 'admin';
                    } elseif (!is_null($user["volunteer_id"])) {
						$_SESSION['volunteer_id'] = $user["id"];
                        $_SESSION['user_role'] = 'volunteer';
                    } else {
                        $_SESSION['user_role'] = 'civilian';
                    }
        
            
                    if ($_SESSION['user_role'] === 'admin') {
                        header("Location: loggedadmin.php");
                    } elseif ($_SESSION['user_role'] === 'volunteer') {
                        header("Location: loggedvolunteer.php");
                    } else {
                        header("Location: loggedcivilian.php");
                    }
                    exit();
                }else{
                    echo "<div class ='nouser'> Password does not match</div>";
                }
            }else {
                echo "<div class ='nouser'> Username does not exist</div>";
            }
        }
        ?>
		<div class="login_header">
			<img src="../images/ceid_logo.png">
			<h4 id="title">Login &raquo; Enter into the system</h3>
		</div>
		<div class ="login_body">
			<form action="login.php" method="POST">
				
				<div class="login">
					<input type="text" name="username" placeholder="Enter username" minlength="3" maxlength="20" autocorrect="off" autocapitalize="none">
					<input type="password" name="password" placeholder="Enter Password" minlength="7" maxlength="20">
					
					<div class ="checkbox">
						<input type="checkbox" name="remember" id="rememberBox">
						<label for="rememberBox">Remember Me</label>
					</div>
					<input type="submit" class="loginbutton" id="loginbutton" name="login" value="LOGIN">
				
				</div>

				<br>

				<p id="register"> Dont have an account? <a href="register.php"><b>Register!!!!</b></a></p>
				<!-- <p id="forgot"> Forgot your <a href="main_menu.php">password?</a></p> -->
				
			</form>
		</div>
    </body>
    
</html>


