<?php
/*
Simpler login page for workspace. Allows redirect.
Maybe we can use this as a replacement for the sidebar login as well.
*/
	session_start();

    require_once('core/Connection.class.php');
	require_once("core/Base.class.php");
	require_once("core/Util.class.php");

	$base = Base::getInstance();
	$cxn = Connection::getInstance();
	$success = array();




    //Check if <username/email,password> exists
	if(isset($_POST['userID']) && isset($_POST['emailType']) && isset($_POST['researcher'])){
	    $userID = $_POST['userID'];
	    $emailType = $_POST['emailType'];
	    $researcher = $_POST['researcher'];

	    $query = "SELECT * FROM recruits WHERE userID=$userID";
        $results = $cxn->commit($query);
        $line = mysql_fetch_array($results,MYSQL_ASSOC);
        $email = $line['email1'];
        $firstName = $line['firstName'];
        $lastName = $line['lastName'];
        $predate = $line['date_firstchoice'];
        $postdate = $line['date_secondchoice'];


        $query = "SELECT * FROM users WHERE userID=$userID";
        $results = $cxn->commit($query);
        $line = mysql_fetch_array($results,MYSQL_ASSOC);
        $username = $line['username'];
        $password = $line['password'];
        $extensionID = $line['extensionID'];



        $message = '';
        $header = '';
        $title = '';
        $signature = '';
        $researcherEmail = '';
        $researcherFullName = '';

        if($researcher=='Matt'){
            $researcherEmail = 'mmitsui@scarletmail.rutgers.edu';
            $researcherFullName = "Matthew Mitsui";
            $signature = "Matthew Mitsui<br/>
Ph.D. Candidate<br/>
Department of Computer Science<br/>
Rutgers University<br/>
mmitsui@scarletmail.rutgers.edu
";
        }else if($researcher=='Eun'){
            $researcherEmail = 'er484@scarletmail.rutgers.edu';
            $researcherFullName = "Eun Rha";
            $signature = "Eun Rha<br/>
Ph.D. Candidate<br/>
School of Communication & Information<br/>
Rutgers University<br/>
er484@scarletmail.rutgers.edu
";
        }else if($researcher=='Jiqun'){
            $researcherEmail = 'jl2033@scarletmail.rutgers.edu';
            $researcherFullName = "Jiqun Liu";
            $signature = "Jiqun Liu<br/>
Ph.D. Student<br/>
School of Communication & Information<br/>
Rutgers University<br/>
jl2033@scarletmail.rutgers.edu 
";
        }

        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= "From: Information Seeking Intentions <$researcherEmail>" . "\r\n";
        $headers .= "Bcc: $researcherFullName <$researcherEmail>" . "\r\n";


        if($emailType=='extension'){
            $title = 'Search intentions study: Extension instructions';
            $message = "<body>
            Thank you once again for your interest in our study.  You have completed the entry interview, and your workspace is now ready.  To begin the study, please download the extension at the URL below.<br/><br/>

Once you download the extension, you may begin the study. You will be sent regular e-mails throughout the study to remind you to record and annotate your search activity.<br/><br/>

Good luck! Do not hesitate to contact us at EMAIL if you have any questions.<br/><br/>

Download URL: https://chrome.google.com/webstore/detail/workplace-study-extension/EXTENSIONID <br/>
Username: USERNAME <br/>
Password: PASSWORD <br/><br/><br/>
SIGNATURE
</body>
            ";

            $message = str_replace("EMAIL",$researcherEmail,$message);
            $message = str_replace("EXTENSIONID",$extensionID,$message);
            $message = str_replace("USERNAME",$username,$message);
            $message = str_replace("PASSWORD",$password,$message);
            $message = str_replace("SIGNATURE",$signature,$message);


        }else if($emailType=='entryinterview'){
            $title = 'Study Reminder: Entry Interview';
            $message = "<body>
Hello FIRSTNAME LASTNAME,<br/><br/>
This is a reminder for your entry interview for our research study. Your interview will begin at:<br/><br/> 
Date of Pre-Task Interview: PREDATE<br/><br/>
If you cannot make this time, please contact us at EMAIL.  In addition, please re-register at https://tinyurl.com/istudynat. We look forward to seeing you soon!<br/><br/><br/>
SIGNATURE
</body>
";

            $message = str_replace("FIRSTNAME",$firstName,$message);
            $message = str_replace("LASTNAME",$lastName,$message);
            $message = str_replace("PREDATE",$predate,$message);
            $message = str_replace("EMAIL",$researcherEmail,$message);
            $message = str_replace("SIGNATURE",$signature,$message);

        }else if($emailType=='exitinterview'){
            $title = 'Study Reminder: Exit Interview';
            $message = "<body>
Hello FIRSTNAME LASTNAME,<br/><br/>
Thank you for your participation in our study!  You’re almost done! This is a reminder for your exit interview for our research study. Your interview will begin at:<br/><br/> 
Date of Post-Task Interview: POSTDATE<br/><br/>
If you cannot make this time, please contact us at EMAIL. We look forward to seeing you soon!<br/><br/><br/>
SIGNATURE
</body>
";

            $message = str_replace("FIRSTNAME",$firstName,$message);
            $message = str_replace("LASTNAME",$lastName,$message);
            $message = str_replace("POSTDATE",$postdate,$message);
            $message = str_replace("EMAIL",$researcherEmail,$message);
            $message = str_replace("SIGNATURE",$signature,$message);
        }


        $message = "<html xmlns='http://www.w3.org/1999/xhtml'><head><meta http-equiv='Content-Type content='text/html; charset=utf-8' /></head>".$message;
        $message .= "</html>";
        mail ($email, $title, $message, $headers);
        mail ($researcherEmail, $title, $message, $headers);


        $success['success'] = true;
        echo json_encode($success);
    }



    ?>
