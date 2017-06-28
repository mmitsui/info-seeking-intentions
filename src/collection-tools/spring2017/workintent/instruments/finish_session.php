<?php
	session_start();
	require_once('../core/Util.class.php');
	require_once('../core/Base.class.php');
	require_once('../core/Connection.class.php');

	Util::getInstance()->checkSession();

	if (Util::getInstance()->checkCurrentPage(basename( __FILE__ )))
	{

		Util::getInstance()->moveToNextStageEndSession();

		$query = "UPDATE users SET status = 0 WHERE userID = ".Base::getInstance()->getUserID();
		$connection = Connection::getInstance();
		$results = $connection->commit($query);

		session_destroy();

		/// Retrieve email addresses of the user
		$email1 ="";
		$email2 ="";
		$query = "SELECT email1, email2 FROM recruits WHERE userID=" .Base::getInstance()->getUserID();
		$results = $connection->commit($query);

		if(mysql_num_rows($results)>0)
		{
			$line = mysql_fetch_array($results, MYSQL_ASSOC);
			$email1 = $line['email1'];

		}




        //send:
        //1) snippets
        //2) bookmarks
        //3) etherpad
		//Finish session 1



				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				$headers .= 'From: Matthew Mitsui <mmitsui@scarletmail.rutgers.edu>' . "\r\n";

				$subject = "Interactive search study completion";

				$message = "<html xmlns='http://www.w3.org/1999/xhtml'><head><meta http-equiv='Content-Type content='text/html; charset=utf-8' />";
				$message .= "\r\n";
				$message .= "<title>Interactive search study completion</title></head>\n<body>\n";
				$message .= "\r\n";
				$message .= "Hello,<br/><br/>This concludes your participation in the study.  Thank you for volunteering your time!<br/><br/>";
				$message .= "\r\n";
				$message .= "Feel free to contact me if you have any questions.<br/><br/>Sincerely,<br/>Matthew Mitsui<br/>PhD Student<br/>Rutgers University School of Communication and Information<br/>mmitsui@scarletmail.rutgers.edu<br/>";
				$message .= "\r\n";

        //snippets
        $projectID = Base::getInstance()->getProjectID();

        $message .= "<br><br>";


        //bookmarks
        // $query = "SELECT * from bookmarks WHERE projectID='$projectID'";
        // $connection = Connection::getInstance();
        // $results = $connection->commit($query);
        // $message .= "<center><strong><u>Bookmarks</u></strong></center><br><br>";
        // $message .= "\r\n";
        // $ct=0;
        // while($line = mysql_fetch_array($results,MYSQL_ASSOC)){
        //     $ct+=1;
        //     $title = $line['title'];
        //     $url = $line['url'];
        //     $rating = $line['rating'];
        //     // $note = $line['note'];
				//
        //     $message .= "<u>Bookmark $ct</u><br>";
        //     $message .= "<u>Page title:</u> $title<br>";
        //     $message .= "<u>URL:</u> $url<br>";
        //     $message .= "<u>Rating:</u> $rating<br>";
        //     // $message .= "<u>Note:</u> $note<br>";
				//
        // }
				//
        // $message .= "<br><br>";


        //etherpad
				$message .= "</body></html>";

				// mail ('chris.leeder@rutgers.edu', $subject, $message, $headers); //Copy to researchers conducting the study
				mail ('mmitsui@scarletmail.rutgers.edu', $subject, $message, $headers); //Copy to researchers conducting the study
				// mail ($email1, $subject, $message, $headers); //Notificaiton to Participant's primary email

        $base = new Base();
        Util::getInstance()->saveAction('logout',0,$base);



				//Save action
				// Send an email at the end of session 1 instruting users to log in 2 days after to complete the second session




		?>
		<html>
		<head>
		<title>Research Study
		</title>
		</head>
		<link rel="stylesheet" href="../study_styles/bootstrap-lumen/css/bootstrap.min.css">
		<link rel="stylesheet" href="../study_styles/custom/text.css">
		<link rel="stylesheet" href="../styles.css">

		<body class="body">
		<center>

			<center><h3>Thank you for participating in this study!</h3></center>
			<table class="body" width="503">
				<tr><td align="center"><br/><td/></tr>
				<tr><td align="center"><br/>You will now participate in an exit interview before leaving.<td/></tr>
				<tr><td align="center"><br/>Afterwards, please sign for receiving your incentive payment on the way out.<td/></tr>
				<tr><td align="center"><br/><br /><td/></tr>
				<!--<tr><td align="center"><a href="../logout.php">Click here to exit.</a></td></tr>-->
			</table>
		</center>
		</body>
		</html>
<?php


}

?>
