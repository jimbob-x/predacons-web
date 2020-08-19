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

$conn = mysqli_connect($servername, $dbUsername, $dbPassword, $dbname);

if (isset($_GET["key"]) && isset($_GET["email"]) && isset($_GET["action"]) 
&& ($_GET["action"]=="reset") && !isset($_POST["action"])){
  $key = $_GET["key"];
  $email = $_GET["email"];
  $curDate = date("Y-m-d H:i:s");

  $query = mysqli_query($conn,
  "SELECT * FROM passwordReset WHERE email='$email' AND tempKey='$key';"
  );
  $row = mysqli_num_rows($query);
  if ($row==""){
  $error .= '<h2>Invalid Link</h2>
<p>The link is invalid/expired. Either you did not copy the correct link
from the email, or you have already used the key in which case it is 
deactivated.</p>
<p><a href="https://d-predictions.co.uk/predacons/reset.html">
Click here</a> to reset password.</p>';
  }else{
  $error = "";
  $row = mysqli_fetch_assoc($query);
  $expDate = $row['expDate'];
  if ($expDate >= $curDate){
  ?>
  <br />
  <form method="post" action="" name="update">
  <input type="hidden" name="action" value="update" />
  <br /><br />
  <label><strong>Enter New Password:</strong></label><br/>
  <input type="password" name="psw" maxlength="15" required />
  <br /><br />
  <input type="hidden" name="email" value="<?php echo $email;?>"/>
  <input type="submit" value="Reset Password"/>
  </form>
<?php
}else{
$error .= "<h2>Link Expired</h2>
<p>The link is expired. You are trying to use an expired link which 
is valid for only 1 hour.<br /><br /></p>";
            }
      }
if($error!=""){
  echo "<div class='error'>".$error."</div><br />";
  } 
}
 
if(isset($_POST["email"]) && isset($_POST["action"]) && ($_POST["action"]=="update")){
  $error="";
  //$pass1 = mysqli_real_escape_string($conn, $_POST["pass1"]);
  $pass = md5($_POST["psw"]);
  $email = $_POST["email"];
  $curDate = date("Y-m-d H:i:s");
  mysqli_query(
    $conn,
    "UPDATE users SET password='$pass'
    WHERE email='$email';"
  );
  mysqli_query($conn, "DELETE FROM passwordReset WHERE email='$email';");

  echo "<script>
  alert('Your password has been reset, please login.');
  window.location.href='login.html';
  </script>";

  //echo '<div class="error"><p>Congratulations! Your password has been updated successfully.</p>
  //<p><a href="https://d-predictions.co.uk">
  //Click here</a> to Login.</p></div><br />';
}
elseif($error!=""){
  echo "<div class='error'>".$error."</div><br/>";
} 
?>
