<?php

//get sql details
$myfile = fopen(".keys/sql_keys", "r") or die("Unable to open file!");
$serverAttr = fread($myfile,filesize(".keys/sql_keys"));

$serverAttr = explode("\n", $serverAttr);

$servername = explode("=", $serverAttr[0])[1];
$dbUsername =  explode("=", $serverAttr[1])[1];
$dbPassword =  explode("=", $serverAttr[2])[1];
$dbname =  explode("=", $serverAttr[3])[1];

fclose($myfile);

$name = 'penfold-x';

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

echo $currentMatchDay;

//get fixtures previous to curretn matchday
$query = "SELECT * FROM fixtures JOIN predictions on fixtures.id = predictions.match_id AND fixtures.matchday = $currentMatchDay";	

if ($result = $con->query($query)) {
	foreach ($result as $res) {
		var_dump($res);
	}
}



/*$query = "SELECT * FROM fixtures JOIN predictions ON fixtures.id = predictions.match_id AND fixtures.matchday = $currentMatchDay AND predictions.username = '$name'";
if ($result = $con->query($query)) {
        //echo var_dump($res);
        foreach ($result as $res){
            $homeGoalsId = $res['id'] . '_home';
            $AwayGoalsId = $res['id'] . '_away';
            if ($res['home_goals'] === NULL) {
                    $homeGoals = 0;
            }
            else {
                    $homeGoals = $res['home_goals'];
            }
            if ($res['away_goals'] === NULL) {
                    $awayGoals = 0;
            }
            else {
                    $awayGoals = $res['away_goals'];
            }

            if ($res['predicted_home'] === NULL) {
                    $predictedHome = 0;
            }
            else {
                    $predictedHome = $res['predicted_home'];
            }
            if ($res['predicted_away'] === NULL) {
                    $predictedAway = 0;
            }
            else {
                    $predictedAway = $res['predicted_away'];
            }

            //echo(var_dump($res));
            echo "<tr>
            <td style='display:none;'>" . $res['id']  . "</td>" .
            "<td>" . $res['date'] . "</td>" .
            "<td>" . $res['home_team'] . "</td>" .
            "<td>" . $res['away_team'] . "</td>" .
            "<td>" . $homeGoals . "</td>" .
            "<td>" . $awayGoals . "</td>";
            if ($date > $res['date']){
                    echo "<td style='text-wrap:normal;word-wrap:break-word'><input style='width: 40px;' value='$predictedHome' type='number' min='0' id='$homeGoalsId' name='$homeGoalsId' disabled></td>" .
                    "<td style='text-wrap:normal;word-wrap:break-word'><input style='width: 40px;' value='$predictedAway' type='number' min='0' id='$AwayGoalsId' name='$AwayGoalsId' disabled></td></tr>";
            }
            else {
                    echo "<td style='text-wrap:normal;word-wrap:break-word'><input style='width: 40px;' value='$predictedHome' type='number' min='0' id='$homeGoalsId' name='$homeGoalsId'></td>" .
                    "<td style='text-wrap:normal;word-wrap:break-word'><input style='width: 40px;' value='$predictedAway' type='number' min='0' id='$AwayGoalsId' name='$AwayGoalsId'></td></tr>";
            }
        }
}
 */

?>
