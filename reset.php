<?php
session_start();

//get sql details
$myfile = fopen(".keys/sql_keys", "r") or die("Unable to open file!");
$serverAttr = fread($myfile,filesize(".keys/sql_keys"));

$serverAttr = explode("\n", $serverAttr);

$servername = explode("=", $serverAttr[0])[1];
$dbUsername =  explode("=", $serverAttr[1])[1];
$dbPassword =  explode("=", $serverAttr[2])[1];
$dbname =  explode("=", $serverAttr[3])[1];

fclose($myfile);

date_default_timezone_set('UTC');

// Try and connect using the info above.
$con = mysqli_connect($servername, $dbUsername, $dbPassword, $dbname);
if ( mysqli_connect_errno() ) {
	// If there is an error with the connection, stop the script and display the error.
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

// Now we check if the data from the login form was submitted, isset() will check if the data exists.
if ( !isset($_POST['uname'])) {
	// Could not get the data that should have been sent.
	exit('Please fill the username field!');
}

// Prepare our SQL, preparing the SQL statement will prevent SQL injection.
if ($unameStmt = $con->prepare('SELECT username, email FROM users WHERE username = ?')) {
	// Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
	$unameStmt->bind_param('s', $_POST['uname']);
	$unameStmt->execute();
	// Store the result so we can check if the account exists in the database.
	$unameStmt->store_result();
}

if ($unameStmt->num_rows > 0) {
	$unameStmt->bind_result($username, $email);
	$unameStmt->fetch();
	// Account exists, now we send a reset email the email.
} else {
    if ($emailStmt = $con->prepare('SELECT username, email FROM users WHERE email = ?')) {
        // Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
        $emailStmt->bind_param('s', $_POST['uname']);
        $emailStmt->execute();
        // Store the result so we can check if the account exists in the database.
        $emailStmt->store_result();
    }

    if ($emailStmt->num_rows > 0) {
        $emailStmt->bind_result($username, $email);
        $emailStmt->fetch();
        // Account exists, now we send a reset email the email.
    }
}

$con->close();

$conn = mysqli_connect($servername, $dbUsername, $dbPassword, $dbname);

$expFormat = mktime(
    date("H")+1, date("i"), date("s"), date("m") ,date("d"), date("Y")
);
//$email = 'penfold@penfold.com';
$expDate = date("Y-m-d H:i:s", $expFormat);
$key = md5(2418*2  + $email);
$addKey = substr(md5(uniqid(rand(), 1)), 3, 10);
$key = $key . $addKey;

$insertKey = "INSERT INTO passwordReset (email, username, tempKey, expDate) VALUES ('$email', '$username', '$key', '$expDate')";
mysqli_query($conn, $insertKey);

$conn->close();


// The message
$message = "Please click on the following link to reset your password.\r\n";
$message .= 'https://d-predictions.co.uk/predacons-web/resetPassword.php?key='.$key.'&email='.$email.'&action=reset';

// In case any of our lines are larger than 70 characters, we should use wordwrap()
$message = wordwrap($message, 70, "\r\n");

$headers = array(
	'From' => 'Prediction Master',
	'reply-To' => 'd.predictions.master@gmail.com'
);

// Send
mail('d-predictions@protonmail.com', 'My Subject', $message, $headers);
//mail($email, 'My Subject', $message);


echo "<script>
alert('Email for password reset sent to your account, please activate in the next 60 mins.');
window.location.href='login.html';
</script>";

?>
