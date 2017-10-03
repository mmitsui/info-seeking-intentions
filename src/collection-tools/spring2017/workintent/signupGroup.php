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
        $cxn = Connection::getInstance();
        $result = $cxn->commit("SELECT * FROM recruits WHERE email1='$email'");
        return mysql_num_rows($result) >0;

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
//                  && (isset($_POST['age_1']))
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
                    $firstName= addslashes($_POST["firstName_$x"]);
                    $lastName = addslashes($_POST["lastName_$x"]);
                    $email1 = $_POST["email1_$x"];

                    $time = $base->getTime();
                    $date = $base->getDate();
                    $timestamp = $base->getTimestamp();
                    $user_ip = $base->getIP();
//										$age = $_POST["age_$x"];
										$user_assoc["un_$x"]=$username;
										$user_assoc["pwd_$x"]=$password;


                    $results = $connection->commit($query);

                    $query = "INSERT INTO recruits (firstName, lastName, email1, approved, `date`, `time`, `timestamp`,userID,projectID,registrationID) VALUES('$firstName','$lastName','$email1','1', '$date', '$time', '$timestamp','$next_userID','$next_userID','$next_registrationID')";
//                    $query = "INSERT INTO recruits (firstName, lastName, age, email1, approved, `date`, `time`, `timestamp`,userID,projectID,registrationID) VALUES('$firstName','$lastName','$age','$email1','1', '$date', '$time', '$timestamp','$next_userID','$next_userID','$next_registrationID')";
                    $results = $connection->commit($query);
                    $recruitsID = $connection->getLastID();

                    $query = "INSERT INTO users (userID,projectID,username,password,password_sha1,`status`,study,optout,numUsers,`group`) VALUES ('$next_userID','$next_userID','$username','$password','$password_sha1','1','1','0','$NUM_USERS','study')";
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
                $message .= "Thank you for your interest in taking part in our study. The details are shown below.<br/><br/>";
                $message .= "\r\n";
                $message .= "<strong>Participant name: </strong>";

                for($x=1;$x<=$NUM_USERS;$x++){
                    $firstName = $_POST["firstName_$x"];
                    $lastName = $_POST["lastName_$x"];
                    $message .= $firstName." ".$lastName."<br/>";
                    $message .= "\r\n";
                }



                $username = $user_assoc["un_1"];
                $password = $user_assoc["pwd_1"];
                $message .= "<br/>\r\n";


                $message .= "\r\n";













								$message .= "During this study you will conduct task-based searches 
                                        and will be asked explain your search intentions 
                                        at various points of your search.<br/><br/>";
                $message .= "\r\n";

                http://coagmento.org/workintent/entryQuestionnaire.php
								$message .= "You will receive <strong>$100 cash</strong> for participating in the study.  </strong>.<br/><br/>";

                                $message .= "Before being able to access the study, we would first like you to answer an entry questionnaire regarding demographics and the tasks you typically work on.
                                             To complete the questionnaire, please go here: http://coagmento.org/workintent/entryQuestionnaire.php.<br/><br/>";
								$message .= "\r\n";


                $message .= "\r\n";
                $message .= "After completing the questionnaire, you will receive a confirmation e-mail in 24-48 hours on downloading and installing the plugin for this study. Feel free to <a href=\"mailto:mmitsui@scarletmail.rutgers.edu?subject=Study inquiry\">contact us</a> if you have any questions.";
								$message .= "\r\n";
                $message .= "</body></html>";


                mail ('mmitsui@scarletmail.rutgers.edu', $subject, $message, $headers); //Copy to researchers conducting the study
                mail ('mmitsui88@gmail.com', $subject, $message, $headers); //Copy to researchers conducting the study
//                mail ('belkin@rutgers.edu', $subject, $message, $headers); //Copy to researchers conducting the study
//                mail ('erha43@gmail.com', $subject, $message, $headers); //Copy to researchers conducting the study


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
                        You will receive a confirmation email with a link to the entry questionnaire.
                        Afterwards, within 24-48 hours, you will receive information on downloading and installing the plugin for this study.  
                        If you have questions, feel free to <a href=\"mailto:mmitsui@scarletmail.rutgers.edu?subject=Study inquiry\">contact us</a>.<hr/></td></tr>\n";
                echo "<tr><td><strong>Participant information</strong></td></tr>\n";

                for($x=1;$x<=$NUM_USERS;$x++){
                    $email1 = $_POST["email1_$x"];
                    $firstName = $_POST["firstName_$x"];
                    $lastName = $_POST["lastName_$x"];
                    $username =$user_assoc["un_$x"];
                    $password = $user_assoc["pwd_$x"];




                    if($NUM_USERS>=2){
                        echo "<tr><td><br><br></td></tr>";
                        echo "<tr><td><strong>Participant $x</strong></td></tr>\n";
                    }
                    echo "<tr><td>First name: $firstName</td></tr>\n";
                    echo "<tr><td>Last name: $lastName</td></tr>\n";
                    echo "<tr><td>Email: $email1</td></tr>\n";

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
