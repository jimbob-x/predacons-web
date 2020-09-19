<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	echo "<script>
	alert('You must be logged in to access this page..');
	window.location.href='login.html';
	</script>";
}
else {
	$name = $_SESSION['name'];
	$_SESSION['sessId'] = md5(2418*2  + $name);
	
	//TO DO
	//write session id to userdb
}

//get sql details
$myfile = fopen(".keys/sql_keys", "r") or die("Unable to open file!");
$serverAttr = fread($myfile,filesize(".keys/sql_keys"));

$serverAttr = explode("\n", $serverAttr);

$servername = explode("=", $serverAttr[0])[1];
$dbUsername =  explode("=", $serverAttr[1])[1];
$dbPassword =  explode("=", $serverAttr[2])[1];
$dbname =  explode("=", $serverAttr[3])[1];

fclose($myfile);

$con = mysqli_connect($servername, $dbUsername, $dbPassword, $dbname);
if ( mysqli_connect_errno() ) {
        // If there is an error with the connection, stop the script and display the error.
        exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}


?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <link rel="shortcut icon" href="assets/media/cress.png" type="image/x-icon">
        <link href="assets/styles/home.css" rel="stylesheet" type="text/css">
	<link href='https://fonts.googleapis.com/css?family=Mandali' rel='stylesheet'>       
    </head>
    <body class="loggedin">
	<nav class="navtop">
            <div>
		<a href="home.php"><h1>Predacons</h1></a>
		<!--a href="d.php?sessId=<?php echo $session ?>">D</a-->
                <!--a href="d.php">D</a-->
		<!--a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a-->
		<a href="fixtures.php">Fixtures</a>
		<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
	    </div>
	</nav>
	<div class="content">
	    <p>Welcome back, <?=$name?>!</p>
            
            <div id="imgbox">
                <img src="assets/media/penfold0.png">
                <img src="assets/media/penfold1.png">
	    </div>
	    <div id="tablebox">
            <table border='1' style='table-layout:auto'>
		<tr>
                    <th>USER</th>
		    <th>POINTS</th>
		</tr>
		<?php
                    $query = "SELECT username, SUM(points) FROM predictions WHERE points IS NOT NULL GROUP BY username ORDER BY SUM(points) DESC;";
                   if ($result = $con->query($query)) {
                       foreach ($result as $res){
                           $username = $res['username'];
                           $pts = $res['SUM(points)'];
                           echo "<tr><td>$username</td><td>$pts</td></tr>";
                       }
                   }
                ?>
	    </div>
	</div>
        
    </body>
</html>
