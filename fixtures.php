<?php
session_start();


if (!isset($_SESSION['loggedin'])) {
        echo "<script>
        alert('You must be logged in to access this page..');
        window.location.href='login.html';
        </script>";
}
else {
        $name = $_SESSION['name'];

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

// Try and connect using the info above.

echo '<link rel="stylesheet" href="assets/styles/fixtures.css" type="text/css">';

echo '<html>
    <head>
        <meta charset="utf-8">
        <link rel="shortcut icon" href="assets/media/cress.png" type="image/x-icon">
        <link href="assets/styles/home.css" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Mandali" rel="stylesheet">
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
        </nav>';


$con = mysqli_connect($servername, $dbUsername, $dbPassword, $dbname);
if ( mysqli_connect_errno() ) {
        // If there is an error with the connection, stop the script and display the error.
        exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}


//get current matchday
if ($matchDay = $con->prepare('SELECT current_matchday FROM fixtures LIMIT 1')) {
    // Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
    $matchDay->execute();
    // Store the result so we can check if the account exists in the database.
    $matchDay->store_result();
}

if ($matchDay->num_rows > 0) {
    $matchDay->bind_result($currentMatchDay);
    $matchDay->fetch();
}

//get current fixtures
$con = mysqli_connect($servername, $dbUsername, $dbPassword, $dbname);
if ( mysqli_connect_errno() ) {
    // If there is an error with the connection, stop the script and display the error.
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

echo "<form action='addPredictions.php' method='post' id='predictions'>";
echo "<table border='1' style='table-layout: auto;'><tr><th>MATCH START</th><th>HOME</th><th>AWAY</th><th>HOME GOALS</th><th>AWAY GOALS</th><th>HOME<br>PREDICTED</th><th>AWAY<br>PREDICTED</th></tr>";

$date = date('Y-m-d H:i:s');
$date = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($date)));

//get date 2 weeks in the future
$two_weeks = date('Y-m-d H:i:s', mktime(23, 59, 59, date('m'), date('d') + 14, date('Y')));

$query = "SELECT * FROM fixtures JOIN predictions ON fixtures.id = predictions.match_id AND fixtures.date < '$two_weeks' AND fixtures.date > '$date' AND predictions.username = '$name' ORDER BY fixtures.date";
if ($result = $con->query($query)) {
	//echo var_dump($res);
	foreach ($result as $res){
            //$predicted = 0;
            //$colour = 'white';
	    $homeColour = '#d61a1a';
	    $awayColour = '#d61a1a';		
	    $homeGoalsId = $res['id'] . '_home';
	    $AwayGoalsId = $res['id'] . '_away';
	    if ($res['home_goals'] === NULL) {
		    $homeGoals = NULL;		    
	    } 
	    else {
		    $homeGoals = $res['home_goals'];
	    }
            if ($res['away_goals'] === NULL) {
                    $awayGoals = NULL;
	    }
	    else {
		    $awayGoals = $res['away_goals'];
	    }
            
	    if ($res['predicted_home'] === NULL) {
		    $predictedHome = NULL;
	    }
	    else {
		    $predictedHome = $res['predicted_home'];
		    $homeColour = '#1da51d';
	    }
            if ($res['predicted_away'] === NULL) {
		    $predictedAway = NULL;
	    }
	    else {
		    $predictedAway = $res['predicted_away'];
		    $awayColour = '#1da51d';
	    }

	    /*
	    if ($res['predicted_home'] === NULL and $res['predicted_away'] === NULL) {
		    $predicted = 0;
		    $colour = '#d61a1a';
	    }
	    else {
		    $predicted = 1;
		    $colour = '#1da51d';
	    }
	     */	    
            //echo(var_dump($res));
            echo "<tr>
	    <td id='matchId' style='display:none;'>" . $res['id']  . "</td>" . 
            //"<td id='predicted' style='display:none;'>" . $predicted  . "</td>" .
            "<td>" . $res['date'] . "</td>" . 
            "<td>" . $res['home_team'] . "</td>" .
            "<td>" . $res['away_team'] . "</td>" .
            "<td>" . $homeGoals . "</td>" .
            "<td>" . $awayGoals . "</td>";
	    if ($date > $res['date']) {
		    echo "<td style='text-wrap:normal;word-wrap:break-word'><input style='width:40px;background-color:$homeColour;' value='$predictedHome' type='number' min='0' id='$homeGoalsId' name='$homeGoalsId' disabled></td>" .
	            "<td style='text-wrap:normal;word-wrap:break-word'><input style='width:40px;background-color:$awayColour;' value='$predictedAway' type='number' min='0' id='$AwayGoalsId' name='$AwayGoalsId' disabled></td></tr>";
	    }
	    else {
		    echo "<td style='text-wrap:normal;word-wrap:break-word'><input style='width:40px;background-color:$homeColour;' value='$predictedHome' type='number' min='0' id='$homeGoalsId' name='$homeGoalsId'></td>" .
	            "<td style='text-wrap:normal;word-wrap:break-word'><input style='width:40px;background-color:$awayColour;' value='$predictedAway' type='number' min='0' id='$AwayGoalsId' name='$AwayGoalsId'></td></tr>";
	    }
	}
}

echo "</table></form><br>";
echo "<div><button type='submit' form='predictions' value='Submit'>Submit</button></div><br>";
echo "<div><button type='button' disabled>Previous</button><button type='button' disabled>Next</button></div>";
echo "</body></html>";

?>

