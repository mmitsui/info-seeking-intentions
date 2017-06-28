<?php

function sessionExists(){
	return isset($_SESSION['CSpace_userID']);
}

function isSessionOrDie(){
	if(!sessionExists()){
		?>
		<html lang="en">
		<head>
			<meta charset="utf-8">
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<title>Login Error</title>
			<link rel="stylesheet" href="../../study_styles/bootstrap-3.3.7-dist/css/bootstrap.min.css">
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
			<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		</head>
		<body>
		<h1 class="text-danger">You are not signed in. Please sign in through the plugin.</h1>

		</body>
		</html>
		<?php
		exit();
	}

}



?>
