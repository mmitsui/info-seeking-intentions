<?php
	session_start();
	require_once('../core/Connection.class.php');
	require_once('../core/Settings.class.php');
	require_once('../core/Base.class.php');


	function random_password_generator($length = 10) {
			$char_lower = 'abcdefghijklmnopqrstuvwxyz';
			$char_upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$char_num = '0123456789';
			$char_punc = '*&%!?#*$@^';
			$randomString = '';

			$randomString .= $char_upper[rand(0, strlen($char_upper) - 1)];
			$randomString .= $char_lower[rand(0, strlen($char_lower) - 1)];
			$randomString .= $char_lower[rand(0, strlen($char_lower) - 1)];
			$randomString .= $char_num[rand(0, strlen($char_num) - 1)];
			$randomString .= $char_lower[rand(0, strlen($char_lower) - 1)];
			$randomString .= $char_upper[rand(0, strlen($char_upper) - 1)];
			$randomString .= $char_punc[rand(0, strlen($char_punc) - 1)];

			return $randomString;
	}

  $base = Base::getInstance();
	$stg = Settings::getInstance();
	$cxn = Connection::getInstance();

	if(isset($_POST["action"]) && $_POST["action"] == "send-email"){

		$email = $cxn->esc($_POST["email"]);
		$query = "SELECT R.userID as userID,U.username as username FROM recruits R,users U WHERE R.email1='$email' AND R.userID=U.userID";
		$results = $cxn->commit($query);
		if(mysql_num_rows($results) > 0){
			$line = mysql_fetch_array($results, MYSQL_ASSOC);
			$userID = $line['userID'];
			$userName = $line['username'];
			$pwd = random_password_generator();
			$pwd_sha1 = sha1($pwd);

			//store in database
			$message = "We're sorry to hear you lost your password.  We have provided your new login information below.\n\nUsername: $userName\nPassword: $pwd\n\nPlease keep these for your records.";
			$m = $message;
			$q = sprintf("INSERT INTO contact_messages (`message`, `email`, `userID`, `username`) VALUES ('COAG: Forgotten Password', '%s', %d, '%s')", $email, $userID, $userName);
			$cxn->commit($q);


			$q = "UPDATE users SET `password_sha1`='$pwd_sha1' WHERE userID=$userID";
			$cxn->commit($q);


			//send ourselves an email
			$email_message = $m;
			mail($stg->getContactEmails(), "Coagmento Spring 2015 - New Password", $email_message);
			mail($email, "Coagmento Spring 2015 - New Password", $m);
			exit("email-sent");
		}else{
			exit("wrong-email");
		}

	}else{
?>
<html>
<head>
	<link rel="stylesheet" href="../study_styles/custom/text.css">
	<link rel="stylesheet" href="../study_styles/pure-release-0.5.0/buttons.css">
	<link rel="stylesheet" href="../study_styles/pure-release-0.5.0/forms.css">
	<title>Contact Us</title>
	<style type="text/css">
	#container{
		width: 500px;
		margin: 10px auto;
	}
	form{
		width: 500px;
	}

	.row{
		margin-bottom: 10px;
	}
	.row label{
		display: inline-block;
		width: 200px;
		font-size: 12px;
	}
	.row input[type=email]{
		width: 300px;
	}
	.row textarea{
		width: 100%;
	}
	</style>
	<script src="../lib/jquery-2.1.3.min.js"></script>
</head>
<body class="body">
	<div id="container">
		<h2>Get New Password</h2>
		<p>We're sorry to hear you're having trouble with your password.</p>
		<p>Please provide us the e-mail address you gave at login.  We'll e-mail you with your username and a new password.</p>
		<form action="#" class="pure-form" method="post">
			<div class="row">
				<label>Your email</label><input type="email" name="email" placeholder="E-mail" required/>
			</div>
			<div class="row">
				<button type="submit" class="pure-button pure-button-primary" >Submit</button>
			</div>
		</form>
	</div>

	<script>
		var cform = $("form");
		cform.on("submit", function(e){
			//send ajax request
			var param = {
				email : cform.find("[name=email]").val(),
				action : "send-email"
			};
			$.ajax({
				url: "generatePassword.php",
				data: param,
				method: "post",
				success: function(resp){
					if(resp == "email-sent"){
						alert("This e-mail was sent.  You may now close your window.");
						window.close();
					} else if (resp=="wrong-email"){
						alert("Our records do not match the e-mail you gave.  Please try again.");

					}else {
						alert("Something went wrong, could not send email"+resp);
					}
				}
			})
			e.preventDefault();
		})
	</script>
</body>
</html>
<?php
}
?>
