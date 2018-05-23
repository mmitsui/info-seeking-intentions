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
	if(isset($_GET['userID']) && isset($_GET['interviewtype'])){
        $userID = $_GET['userID'];
        $interviewtype = $_GET['interviewtype'];

        $filename = "";
        $folder = "";
        if($interviewtype=='entry'){
			$folder='Entry Interviews';
            $filename = "user$userID"."_entry.wav";
        }else if($interviewtype=='exit'){
            $folder='Exit Interviews';
            $filename = "user$userID"."_exit.wav";
        }


        $file = "../workintent/session_data/".$folder."/".$filename;




        if(file_exists($file)){
////            echo filesize($file);
//            header('Content-Description: File Transfer');
//            header('Content-Type: application/octet-stream');
//            header('Content-Disposition: attachment; filename='.basename($file));
//            header('Content-Transfer-Encoding: binary');
//            header('Expires: 0');
//            header('Cache-Control: must-revalidate');
//            header('Pragma: public');
//            header('Content-Length: ' . filesize($file));
//            ob_clean();
//            flush();
//            readfile($file);
//            exit();
//////        	echo "File exists!";
////			readfile($file);

            echo "<audio controls>";
            echo "<source src=\"$file\" type=\"audio/wav\">";
            echo "Your browser does not support the audio element.";
			echo "</audio>";
		}else{
			echo "File $filename does not exist";
		}

    }else{
		echo "User ID or interview type not provided";

	}



    ?>
