<?php

function phpAlert($msg) {
    echo '<script type="text/javascript">alert("' . $msg . '")</script>';
}

//header('Location: home.html');

//get sql details
$myfile = fopen(".keys/sql_keys", "r") or die("Unable to open file!");
$serverAttr = fread($myfile,filesize(".keys/sql_keys"));

$serverAttr = explode("\n", $serverAttr);

$servername = explode("=", $serverAttr[0])[1];
$username = explode("=", $serverAttr[1])[1];
$dbPassword = explode("=", $serverAttr[2])[1];
$dbname = explode("=", $serverAttr[3])[1];

fclose($myfile);

if ( !isset($_POST['uname'], $_POST['psw']) ) {
    // Could not get the data that should have been sent.
    exit('Please fill both the username and password fields!');
}


$uname = $_POST["uname"];
$email = $_POST["email"];
$password = md5($_POST["psw"]);
$today = date("Y-m-d H:i:s");

// Create connection
$conn = new mysqli($servername, $username, $dbPassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM users where username='$uname'";
$result = $conn->query($sql);


if ($result->num_rows > 0) {
  
// output data of each row
while($row = $result->fetch_assoc()) {
    $existingUser = $row['username'];
    echo "<script>
    alert('User already exists, try a different username');
    window.location.href='new-acc.html';
    </script>";    
}
} else {
    $insertUser = "INSERT INTO users (username, email, password, joined) VALUES ('$uname', '$email', '$password', '$today')";
    mysqli_query($conn, $insertUser) or die (mysql_error());

    //$addPredictions = "INSERT INTO predictions"
}

$conn->close();

//add new user and fixtures to predictions table

// Create connection
$conn = new mysqli($servername, $username, $dbPassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$fixtureQuery = "SELECT id FROM fixtures";
$fixtureResult = $conn->query($fixtureQuery);

$sqlNewFixtures = '';
foreach($fixtureResult as $fix) {   
    $fixInput = $fix['id'];
    $sqlNewFixtures .= "INSERT INTO predictions (username, match_id) VALUES ('$uname', '$fixInput');";
}

$conn->close();
//add new user and fixtures to predictions table

// Create connection
$conn = new mysqli($servername, $username, $dbPassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($conn->multi_query($sqlNewFixtures) === TRUE) {
  echo "<script>alert('New records created successfully')</script>";
} else {
  echo "<script>alert('Error creating fixtures in database, contact site admin')</script>";
}

$conn->close();

echo "<script>
alert('Account succesfully created');
window.location.href='login.html';
</script>";

//echo '<script>alert("Account succesfully created")</script>'; 

//header('Location: login.html');

?>
