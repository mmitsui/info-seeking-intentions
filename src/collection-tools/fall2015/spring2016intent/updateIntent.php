<?php


session_start();
require_once('core/Base.class.php');
require_once('core/Util.class.php');
require_once('core/Connection.class.php');
require_once('core/Questionnaires.class.php');







function commitanswer_save($userID,$questionID){
	$base = Base::getInstance();
	$cxn = Connection::getInstance();

	$results = $cxn->commit("SELECT * FROM video_segments WHERE userID=$userID AND questionID=$questionID");
	$n_unsaves_in_table = 0;
	while($line=mysql_fetch_array($results,MYSQL_ASSOC)){
		if(typeMatch($line['Details'],'S (save)')){
			$n_unsaves_in_table += 1;
		}
	}

	$results = $cxn->commit("SELECT * FROM video_save_history WHERE userID=$userID AND questionID=$questionID ORDER BY assignmentID ASC");
	$assignmentIDs_array = array();
	while($line=mysql_fetch_array($results,MYSQL_ASSOC)){
		array_push($assignmentIDs_array,$line['assignmentID']);
	}


	$results = $cxn->commit("SELECT * FROM bookmarks WHERE userID=$userID AND questionID=$questionID ORDER BY localTimestamp");
	$n_unsave_actions = mysql_num_rows($results);

	if($n_unsaves_in_table != $n_unsave_actions){
		echo "NOT EQUAL $userID";
		return;
	}
	$ordered_bookmarkIDs = array();
	while($line = mysql_fetch_array($results,MYSQL_ASSOC)){
		array_push($ordered_bookmarkIDs,$line['bookmarkID']);
	}


	foreach($ordered_bookmarkIDs as $key=>$value){
		$assignmentID = $assignmentIDs_array[$key];
		$bookmarkID = $value;
		$cxn->commit("UPDATE video_save_history SET bookmarkID=$bookmarkID WHERE assignmentID=$assignmentID");
	}


}


function typeMatch($detailkey,$detailvalue){
	$detailkey = preg_replace('/[[:^print:]]/', '', $detailkey);
	$detailvalue = preg_replace('/[[:^print:]]/', '', $detailvalue);
	return $detailkey==$detailvalue;
}

function commitanswer_unsave($userID,$questionID){
	$base = Base::getInstance();
	$cxn = Connection::getInstance();

	$results = $cxn->commit("SELECT * FROM video_segments WHERE userID=$userID AND questionID=$questionID");
	$n_unsaves_in_table = 0;
	while($line=mysql_fetch_array($results,MYSQL_ASSOC)){
		if(typeMatch($line['Details'],'U (unsave)')){
			$n_unsaves_in_table += 1;
		}
	}

	$results = $cxn->commit("SELECT * FROM video_unsave_history WHERE userID=$userID AND questionID=$questionID ORDER BY assignmentID ASC");
	$assignmentIDs_array = array();
	while($line=mysql_fetch_array($results,MYSQL_ASSOC)){
		array_push($assignmentIDs_array,$line['assignmentID']);
	}


	$results = $cxn->commit("SELECT * FROM actions WHERE userID=$userID AND questionID=$questionID AND `action`='delete_bookmarks' ORDER BY timestamp");
	$n_unsave_actions = mysql_num_rows($results);

	if($n_unsaves_in_table != $n_unsave_actions){
		echo "NOT EQUAL $userID";
		return;
	}
	$ordered_bookmarkIDs = array();
	while($line = mysql_fetch_array($results,MYSQL_ASSOC)){
		array_push($ordered_bookmarkIDs,$line['value']);
	}


	foreach($ordered_bookmarkIDs as $key=>$value){
		$assignmentID = $assignmentIDs_array[$key];
		$bookmarkID = $value;
		$cxn->commit("UPDATE video_unsave_history SET bookmarkID=$bookmarkID WHERE assignmentID=$assignmentID");
	}


}


$base = Base::getInstance();
$cxn = Connection::getInstance();
$results = $cxn->commit("SELECT * FROM users WHERE userID < 48 AND participantID IS NOT NULL");

while($line = mysql_fetch_array($results,MYSQL_ASSOC)){
	$userID = $line['userID'];
	$questionID1 = $line['topicAreaID1'];
	$questionID2 = $line['topicAreaID2'];
	commitanswer_save($userID,$questionID1);
	commitanswer_save($userID,$questionID2);
	commitanswer_unsave($userID,$questionID1);
	commitanswer_unsave($userID,$questionID2);
}




?>

<html>
<head>

	<link rel="stylesheet" href="../study_styles/bootstrap-lumen/css/bootstrap.min.css">
	<link rel="stylesheet" href="../study_styles/custom/text.css">
	<link rel="stylesheet" href="../styles.css">
	<link rel="stylesheet" href="../study_styles/pure-release-0.5.0/grids-min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
	<script src="../lib/jquery-2.1.3.min.js"></script>
	<script src="../lib/validation/jquery-validation-1.13.1/dist/jquery.validate.js"></script>
	<script src="../lib/validation/validation.js"></script>
	<title>
		Research Study
    </title>




    <style>
    select {
      font-size:13px;
    }

		.left {
		  position:fixed; // keep fixed to window
		  padding: 10px;

			margin-left: 75%;

		  top: 0; left: 0; bottom: 0; // position to top left of window
			position: fixed;

    	overflow-y: scroll;


		  height:100%; //set dimensions
		  transition: width ease .5s; // fluid transition when resizing

		  /* Sass/Scss only:
		    Using a selector (.open-nav) with an "&" afterward is actually selecting
		  any parent selector. For instance, this outputs "body.open-nav .left { ... }"
		  More info: http://thesassway.com/intermediate/referencing-parent-selectors-using-ampersand
		  */
		  body.open-nav & {
		    width:200px;
		  }

		  ul {
		    list-style:none;
		    margin:0; padding:0;

		    li {
		      margin-bottom:25px;
		    }
		  }

		  a {
		    color:shade(darkslategray, 50%);
		    text-decoration:none;
		    border-bottom:1px solid transparent;
		    transition:
		      color ease .35s,
		      border-bottom-color ease .35s;

		    &:hover {
		       color:white;
		       border-bottom-color:white;
		    }

		    &.open {
		      font-size:1.75rem;
		      font-weight:700;
		      border:0;
		    }
		  }
		}
    </style>




<style type="text/css">
		.cursorType{
		cursor:pointer;
		cursor:hand;
		}
</style>

<style type="text/css">
		legend{
		color:white;
		background-color:#404040;
		padding-left:5px;
		padding-right:5px;
		padding-top:3px;
		padding-bottom:3px;
		border-radius:5px;
		}
</style>

</head>

<body>
Done!
</body>
</html>