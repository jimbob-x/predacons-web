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
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>D</title>
	<link rel="stylesheet" href="assets/styles/d.css">
        <script src="assets/js/d.js"></script>
        <link href='https://fonts.googleapis.com/css?family=Mandali' rel='stylesheet'>
    </head>
    <body>
    <div class="game">
        <div class="game">
            <h3>D - under construction</h3> 
            <div class="game-body">
                <div class="game-options">
                    <input type="button" id="btnStart" class="btn" value="start" onclick="startblackjack()">
                    <input type="button" class="btn" value="hit me" onclick="hitMe()">
                    <input type="button" class="btn" value="stay" onclick="stay()">
                </div>    
                <div class="status" id="status"></div>    
                <div id="deck" class="deck">
                    <div id="deckcount">52</div>
                </div>    
                <div id="players" class="players"></div>    
                <div class="clear"></div>
            </div>
        </div>
    </body>
</html>
