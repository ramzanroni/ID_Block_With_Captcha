<?php
// 	session_start();
// 	 $mysqli= new mysqli("localhost", "root", "", "hello");
// 	if (!isset($_SESSION['mysession'])) 
// {
// 	header("Location: index.php");
// }
// $query=$mysqli->query("SELECT * FROM users WHERE id=".$_SESSION['mysession']);
// $userRow=$query->fetch_array();
// $mysqli->close();


session_start();
$message="";
$captcha=true;
if (count($_POST)>0 && isset($_POST["captcha_code"]) && $_POST["captcha_code"] !=$_SESSION["captcha_code"]) 
{
	$captcha=false;
	$message="Enter the correct Captcha code";
}
$mysqli= new mysqli('localhost', 'root', '', 'login_captcha');
$ip=$_SERVER['REMOTE_ADDR'];
$result = $mysqli->query("SELECT count(ip_address) AS failed_login_attempt FROM falied_login WHERE ip_address = '$ip'  AND date BETWEEN DATE_SUB( NOW() , INTERVAL 1 DAY ) AND NOW()");
$row  = $result->fetch_array();
$failed_login_attempt = $row['failed_login_attempt'];
$result->free();
if (count($_POST)>0 && $captcha == true)
{
	$result = $mysqli->query("SELECT * FROM users WHERE user_name='" . $_POST["user_name"] . "' and password = '". $_POST["password"]."'");
	$row = $result->fetch_array();
	$result->free();
	if (is_array($row)) 
	{
		$_SESSION["user_id"] = $row["id"];
		$_SESSION["user_name"] = $row["user_name"];
		$mysqli->query("DELETE FROM falied_login WHERE ip_address= '$ip'");
	}
	else
	{
		$message="Invalid Username or Password";
		if ($failed_login_attempt<3) 
		{
			$mysqli->query("INSERT INTO falied_login(ip_address, date) VALUES ('$ip', NOW())");
		}
		else
		{
			$message="You have tried more than 3 invalid attempts. Enter Captcha code.";
		}
	}
}
if (isset($_SESSION["user_id"]))
 {
	header("Location: user_dashboard.php");
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>User Login</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" integrity="sha512-+4zCK9k+qNFUR5X+cKL9EIR+ZOhtIloNl9GIKS57V1MyNsYpYcUrUeQc9vNfzsWfV28IaLL3i96P9sdNyeRssA==" crossorigin="anonymous" />
</head>
<body>
	<div class="container">
		<form method="post" name="frmUser" action="">
			<div>
				<?php
					if ($message!="") 
					{
						echo $message;
					}
				?>
			</div>
			<h1>User Login </h1>
			<div class="form-group">
				<label>User Name</label>
				<input type="text" name="user_name" class="form-control">
			</div>
			<div class="form-group">
				<label>Password</label>
				<input type="password" name="password" class="form-control">
			</div>
			<?php
				if (isset($failed_login_attempt) && $failed_login_attempt>= 3)
				 {
					?>
					<img src="captcha.php">
			<input type="text" name="captcha_code" class="form-control" placeholder="Please Enter the Captcha code">
			
			<?php					
				}
			?>
			<div class="form-group">
				<input type="submit" name="submit" class="form-control btn btn-success">
			</div>
		</form>
	</div>
</body>
</html>