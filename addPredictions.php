<?php

session_start();

$name = $_SESSION['name'];

//get sql details
$myfile = fopen(".keys/sql_keys", "r") or die("Unable to open file!");
$serverAttr = fread($myfile,filesize(".keys/sql_keys"));

$serverAttr = explode("\n", $serverAttr);

$servername = explode("=", $serverAttr[0])[1];
$username = explode("=", $serverAttr[1])[1];
$dbPassword = explode("=", $serverAttr[2])[1];
$dbname = explode("=", $serverAttr[3])[1];

fclose($myfile);


// Create connection
$conn = new mysqli($servername, $username, $dbPassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//create array of match id: (home: prediction, away: prediciton)
$predArray = array();


foreach ($_POST as $key => $value) {
	$pieces = explode("_", $key);
	$id = $pieces[0];
	$h_a = $pieces[1];
	if (array_key_exists($id, $predArray)) {
      	    $predArray[$id][$h_a] = $value;
	}
	else {
       	    $predArray[$id] = array();
            $predArray[$id][$h_a] = $value;
	}
}


//build multiple line SQL query from created array
$sqlAdd = '';

foreach ($predArray as $predKey => $predVal) {
	if ($predVal['home'] != "" and $predVal['away'] != "") {
        	$home = intval($predVal['home']);
	        $away = intval($predVal['away']);
   		$sqlAdd .= "UPDATE predictions SET predicted_home=$home, predicted_away=$away WHERE username='$name' AND match_id='$predKey';";
	}
}



if ($conn->multi_query($sqlAdd) === TRUE) {
  echo "<script>alert('Predictions added successfully')</script>";
} else {
  echo "<script>alert('Error creating fixtures in database, contact site admin')</script>";
}

$conn->close();

echo "<script>
window.location.href='fixtures.php';
</script>";

?>
