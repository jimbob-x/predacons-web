<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
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
                <h1>Predacons</h1>
                <a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
	        <a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
	    </div>
	</nav>
	<div class="content">
            <p>Welcome back, <?=$_SESSION['name']?>!</p>
            <p>More content coming soon</p>
            <div id="imgbox">
                <img src="assets/media/penfold0.png">
                <img src="assets/media/penfold1.png">
            </div>
	</div>
    </body>
</html>
