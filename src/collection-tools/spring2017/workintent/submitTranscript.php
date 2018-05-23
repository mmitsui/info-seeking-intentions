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



//                            echo "<input type=\"hidden\" name=\"userID\" value=\"$userID\">";
//                            <input type="hidden" name="type" value="entry">
//                            <input type="file" class="form-control-file" name="entry_transcript_file" id="entry_transcript_file">
//                            <button class="btn btn-success" id="entry_transcript_button">Submit Transcript</button>


    //Check if <username/email,password> exists
    if(!isset($_FILES["entry_transcript_file"]['name']) and !isset($_FILES["exit_transcript_file"]['name'])){
        $success['success'] = false;
        $success['message'] = "File not provided.";
    }
	else if(isset($_POST['userID']) && isset($_POST['type'])){
        $userID = $_POST['userID'];
        $type = $_POST['type'];

        $folder = '';

        $transcriptname = '';
        $filename='';
        $tmpname='';

        if($type=='entry'){
            $fileinfo = pathinfo($_FILES["entry_transcript_file"]["name"]);
            $filename = $_FILES["entry_transcript_file"]["name"];
            $tmpname = $_FILES["entry_transcript_file"]["tmp_name"];
            $folder = 'Entry Interviews';
            $transcriptname = "user$userID"."_entry_transcript.".$fileinfo['extension'];
        }else if ($type=='exit'){
            $fileinfo = pathinfo($_FILES["exit_transcript_file"]["name"]);
            $filename = $_FILES["exit_transcript_file"]["name"];
            $tmpname = $_FILES["exit_transcript_file"]["tmp_name"];
            $folder = 'Exit Interviews';
            $transcriptname = "user$userID"."_exit_transcript.".$fileinfo['extension'];
        }

        $filedestination = "../workintent/session_data/".$folder."/".$filename;
        $transcriptdestination = "../workintent/session_data/".$folder."/".$transcriptname;
        $success1 = copy($tmpname,$filedestination);
        $success2 = copy($tmpname,$transcriptdestination);

        $success['success'] = true;
        $success['message'] = "$filedestination$transcriptdestination$success1$success2";
    }else{
		$success['success'] = false;
		$success['message'] = "Improper input. Please try again or consult tech support.";
	}

	echo json_encode($success);



    ?>
