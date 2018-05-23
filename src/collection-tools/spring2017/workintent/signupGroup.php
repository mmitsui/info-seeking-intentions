<?php
	session_start();
	require_once('core/Connection.class.php');
	require_once('core/Base.class.php');
	require_once('core/Questionnaires.class.php');
?>
<html>
<head>
	<link rel="stylesheet" href="study_styles/bootstrap-lumen/css/bootstrap.min.css">
	<link rel="stylesheet" href="study_styles/custom/text.css">
	<link rel="stylesheet" href="styles.css">
<title>Search Intentions in Natural Settings Study: Sign Up</title>
<link rel="stylesheet" type="text/css" href="styles.css" />
<style type="text/css">
		.cursorType{
		cursor:pointer;
		cursor:hand;
		}
</style>
</head>


<body class="body" >
	<div class="panel panel-default" style="width:95%; margin:auto">
    <div class="panel-body">


        <?php
//        echo "<center><h3>Study Closed</h3></center>";
//        echo "Our Spring 2015 study on user search intentions has officially been closed. We apologize for the inconvenience but would like to thank you for volunteering to participate.";
//        echo "</div>";
//        exit();
        ?>
<?php
		$questionnaire = Questionnaires::getInstance();
		// Check if questionnaire is compelte.


    function random_password_generator($length = 10) {
        // Removed ambiguous characters
        $char_lower = 'abcdefghjkmnpqrstuvwxyz';
        $char_upper = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        $char_num = '23456789';
        $char_punc = '*&%!?#*$@^';
        $randomString = '';

        $randomString .= $char_upper[rand(0, strlen($char_upper) - 1)];
        $randomString .= $char_lower[rand(0, strlen($char_lower) - 1)];
        $randomString .= $char_lower[rand(0, strlen($char_lower) - 1)];
        $randomString .= $char_num[rand(0, strlen($char_num) - 1)];
        $randomString .= $char_lower[rand(0, strlen($char_lower) - 1)];
        $randomString .= $char_upper[rand(0, strlen($char_upper) - 1)];
        $randomString .= $char_upper[rand(0, strlen($char_upper) - 1)];

        return $randomString;
    }

    function username_generator($id) {
        $name = 'user'.strval($id);
        return $name;
    }

    function emailMatches($email){
        return false;


//        $cxn = Connection::getInstance();
//        $result = $cxn->commit("SELECT * FROM recruits WHERE email1='$email'");
//        return mysql_num_rows($result) >0;

    }



    if(isset($_POST['email1_1']) && emailMatches($_POST['email1_1'])){
        ?>
        <p style='background-color:red;color:white'>We are sorry, but you have already created an account with this e-mail! Please use your e-mail to log in to your plugin if you are having issues.</p>
        <p>Please <a href="mailto:mmitsui@scarletmail.rutgers.edu?subject=Study inquiry">contact us</a> if you have any questions.</p>

        <?php

//        $email1 = $_POST['email1_1'];
//        $subject = "Test Subject";
//        $message = "Test Message";
//        $headers = "Test Headers";
//        mail ($email1, $subject, $message, $headers);
        exit();
    }
    else if (
                  (isset($_POST['num_users'])) &&
	   (isset($_POST['firstName_1'])) &&
	   (isset($_POST['lastName_1'])) &&
	   (isset($_POST['email1_1'])) &&
		(isset($_POST['reEmail_1']))
                  && (isset($_POST['age_1']))
        && (isset($_POST['date_firstchoice_1']))
                  && (isset($_POST['date_secondchoice_1']))
        && (isset($_POST['consent_furtheruse']))
                  && (isset($_POST['interview_medium_1']))
                  && (isset($_POST['medium_credentials_1']))
		)
		{
			$connection = Connection::getInstance();
			$base = new Base();


            $closed = false;
            $section_closed = false;

            if(!$closed && !$section_closed){
                $NUM_USERS = 1;
                $query = "SELECT MAX(projectID) as max from recruits WHERE userID <500";
                $results = $connection->commit($query);
                $line = mysql_fetch_array($results, MYSQL_ASSOC);

                $projectID = $line['max']+1;


								$user_assoc = array();
                for($x=1; $x<=$NUM_USERS; $x++){

                    $query = "SELECT MAX(userID) as max FROM recruits WHERE userID <500";
                    $results = $connection->commit($query);
                    $line = mysql_fetch_array($results,MYSQL_ASSOC);

                    $next_userID = $line['max']+1;
										$next_registrationID= str_pad("$next_userID", 3, '0', STR_PAD_LEFT);
                    $password = random_password_generator();
										$username = username_generator($next_userID);
                    $password_sha1 = sha1($password);
                    $username_sha1 = sha1($username);
                    $firstName= addslashes($_POST["firstName_$x"]);
                    $lastName = addslashes($_POST["lastName_$x"]);
                    $timezone = addslashes($_POST["timezone_$x"]);
                    $email1 = $_POST["email1_$x"];
                    $email_sha1 = sha1($_POST["email1_$x"]);
                    $age = $_POST["age_$x"];
                    $date_firstchoice = $_POST["date_firstchoice_$x"];
                    $date_secondchoice = $_POST["date_secondchoice_$x"];
                    $consent_furtheruseonline = $_POST["consent_furtheruse"];
                    $interview_medium = addslashes($_POST["interview_medium_$x"]);
                    $medium_credentials = addslashes($_POST["medium_credentials_$x"]);

                    $study_source = addslashes($_POST["study_source_$x"]);

                    $time = $base->getTime();
                    $date = $base->getDate();
                    $timestamp = $base->getTimestamp();
                    $user_ip = $base->getIP();
//										$age = $_POST["age_$x"];
										$user_assoc["un_$x"]=$username;
										$user_assoc["pwd_$x"]=$password;


                    $results = $connection->commit($query);

                    $query = "INSERT INTO recruits (firstName, lastName, `timezone`,email1, email_sha1, approved, `date`, `time`, `timestamp`,userID,projectID,registrationID,`age`,`date_firstchoice`,`date_secondchoice`,`consent_furtheruseonline`,`interview_medium`,`medium_credentials`,`recruitment_source`) VALUES ('$firstName','$lastName','$timezone','$email1','$email_sha1','1', '$date', '$time', '$timestamp','$next_userID','$next_userID','$next_registrationID','$age','$date_firstchoice','$date_secondchoice','$consent_furtheruseonline','$interview_medium','$medium_credentials','$study_source')";
//                    $query = "INSERT INTO recruits (firstName, lastName, age, email1, approved, `date`, `time`, `timestamp`,userID,projectID,registrationID) VALUES('$firstName','$lastName','$age','$email1','1', '$date', '$time', '$timestamp','$next_userID','$next_userID','$next_registrationID')";
                    $results = $connection->commit($query);
                    $recruitsID = $connection->getLastID();

                    $query = "INSERT INTO users (userID,projectID,username,username_sha1,password,password_sha1,`status`,study,optout,numUsers,`group`) VALUES ('$next_userID','$next_userID','$username','$username_sha1','$password','$password_sha1','1','1','0','$NUM_USERS','study')";
                    $results = $connection->commit($query);
										$userID = $next_userID;

										foreach($_POST as $k=>$v){
											if(strpos($k,"_$x")==strlen($k)-strlen("_$x")){
												$keytoadd = substr($k,0,-strlen("_$x"));
												$questionnaire->addAnswer($keytoadd,$v);
											}
										}
										$questionnaire->commitAnswersToDatabase(array("$userID"),array('userID'),'questionnaire_recruitment');
                }


                // SEND NOTIFICATION EMAIL TO RESEARCHER
                $headers  = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headers .= 'From: Information Seeking Intentions <mmitsui@scarletmail.rutgers.edu>' . "\r\n";
                $headers .= 'Bcc: Matthew Mitsui <mmitsui@scarletmail.rutgers.edu>' . "\r\n";



                $subject = "Search intentions in natural settings study participation confirmation";

                $message = "<html xmlns='http://www.w3.org/1999/xhtml'><head><meta http-equiv='Content-Type content='text/html; charset=utf-8' />";
                $message .= "\r\n";
                $message .= "<title>Search intentions in natural settings study participation confirmation email</title></head>\n<body>\n";
                $message .= "\r\n";

                $message .= "Thank you for your interest in taking part in our study! This is a confirmation e-mail to confirm your participation.  Please save this for your records.<br/><br/>

During this study you will conduct task-based searches 
                                        and will be asked explain your search intentions 
                                        at various points of your search. You will receive <strong>$95 cash</strong> for participating in the study.<br/><br/>
                                        Below are the date and time of your entry and exit interviews.  Please remember these times.  You will be sent a reminder e-mail before each interview.<br/><br/>

If you have chosen these dates in error, please re-register.  In addition, you may also contact us at mmitsui@scarletmail.rutgers.edu to inform us of the correction.<br/><br/>

Do not hesitate to contact us if you have any further questions.<br/><br/>";
                $message .= "\r\n";
                $message .= "<strong>Participant name: </strong>";

                for($x=1;$x<=$NUM_USERS;$x++){
                    $firstName = $_POST["firstName_$x"];
                    $lastName = $_POST["lastName_$x"];
                    $message .= $firstName." ".$lastName."<br/>";
                    $message .= "\r\n";
                }



                $date_firstchoice = $_POST["date_firstchoice_1"];
                $date_secondchoice = $_POST["date_secondchoice_1"];

                $message .= "<p><strong>Date of Pre-Task Interview:</strong> $date_firstchoice</p>\n";
                $message .= "<p><strong>Date of Post-Task Interview:</strong> $date_secondchoice</p>\n";

                $message_experimenter = "There is a new participant for the study! The participant's information is below:<br/><br/>";
                $message_experimenter .= "<strong>Participant name: </strong>";
                $message_experimenter .= $firstName." ".$lastName."<br/>";
                $message_experimenter .= "<p><strong>Date of Pre-Task Interview:</strong> $date_firstchoice</p>\n";
                $message_experimenter .= "<p><strong>Date of Post-Task Interview:</strong> $date_secondchoice</p>\n";
                $message_experimenter .= "<p><strong>Data Entry URL:</strong> http://coagmento.org/workintent/userDataEntry.php?userID=$userID</p>\n";


                $username = $user_assoc["un_1"];
                $password = $user_assoc["pwd_1"];
//                $message .= "<p>Username: $username</p>\n";
//                $message .= "<p>Password: $password</p>\n";
//                $message .= "<br/>\r\n";
                $message .= "\r\n";
                $message .= "</body></html>";


                mail ('mmitsui@scarletmail.rutgers.edu', $subject, $message_experimenter, $headers); //Copy to researchers conducting the study
                mail ('mmitsui88@gmail.com', $subject, $message_experimenter, $headers); //Copy to researchers conducting the study
                mail ('belkin@rutgers.edu', $subject, $message_experimenter, $headers); //Copy to researchers conducting the study
                mail ('er484@scarletmail.rutgers.edu', $subject, $message_experimenter, $headers); //Copy to researchers conducting the study
                mail ('jl2033@scarletmail.rutgers.edu', $subject, $message_experimenter, $headers); //Copy to researchers conducting the study


								// mail ('kevin.eric.albertson@gmail.com', $subject, $message, $headers); //Copy to researchers conducting the study
                for($x=1;$x<=$NUM_USERS;$x++){
                    $email = $_POST["email1_$x"];
                    $firstName = $_POST["firstName_$x"];
                    $lastName = $_POST["lastName_$x"];
                    $message = $firstName." ".$lastName.",<br/><br/>".$message;
                    $message .= "\r\n";
                    mail ($email1, $subject, $message, $headers); //Notificaiton to Participant's primary email
                }


                // WEB APPLICATION NOTIFICATION TO THE PARTICIPANT

								echo "<h3>Registration Complete!</h3>";
								echo "<table>\n";
                echo "<tr><td></td></tr>\n";
                echo "<tr><td align=left>Thank you for submitting your request for participating in this study. 
                        <br/>
                        <br/>
                        ";
                echo "<tr><td><strong>Participant information</strong></td></tr>\n";

                for($x=1;$x<=$NUM_USERS;$x++){
                    $email1 = $_POST["email1_$x"];
                    $firstName = $_POST["firstName_$x"];
                    $lastName = $_POST["lastName_$x"];
                    $username =$user_assoc["un_$x"];
                    $password = $user_assoc["pwd_$x"];
                    $date_firstchoice = $_POST["date_firstchoice_$x"];
                    $date_secondchoice = $_POST["date_secondchoice_$x"];




                    if($NUM_USERS>=2){
                        echo "<tr><td><br><br></td></tr>";
                        echo "<tr><td><strong>Participant $x</strong></td></tr>\n";
                    }
                    echo "<tr><td>First name: $firstName</td></tr>\n";
                    echo "<tr><td>Last name: $lastName</td></tr>\n";
                    echo "<tr><td>Email: $email1</td></tr>\n";
                    echo "<tr><td>Date of Pre-Task Interview: $date_firstchoice</td></tr>\n";
                    echo "<tr><td>Date of Post-Task Interview: $date_secondchoice</td></tr>\n";

                }

                if($NUM_USERS>=2){
                    echo "<tr><td><br><br></td></tr>";
                }




								echo "<br><br>";
                echo "<tr><td><hr/>You can close this window now or navigate away.</td></tr>\n";
                echo "</table>\n";
            }else if($closed){
                echo "<p style='background-color:red;'>Sorry! The user study registration is currently closed.</p>\n";
                echo "<br/><br/>\n";
                echo "<hr/>\n";
                echo "<p>The number of participants required has been reached at this point.</p>\n";
                echo "<p>If more user participation is required, we will reopen the study registration and send another round of recruitment emails.</p>\n";
                echo "<hr/>\n";
            }else if ($section_closed){
                echo "<br/><br/>\n";
                echo "<hr/>\n";
                echo "<p>The number of required for this type of grouping has been reached at this point.</p>\n";
                echo "<p>If you wanted to register as a pair but would still like to participate, please register as individual users.</p>\n";
                echo "<hr/>\n";
            }


		}
		else
		{
			echo "<p>You forgot to complete one or more required values. Please click the button below to return to the sign up form.</p>\n";
			echo "<input type=\"button\" value=\"Go Back\" onClick=\"javascript:history.go(-1)\" />";
		}

?>
<br/>
</div></div>
</body>
</html>
