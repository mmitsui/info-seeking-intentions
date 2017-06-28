<?php


session_start();
require_once('../core/Base.class.php');
require_once('../core/Util.class.php');
require_once('../core/Connection.class.php');
require_once('../core/Questionnaires.class.php');



Util::getInstance()->checkSession();

if (Util::getInstance()->checkCurrentPage(basename( __FILE__ )))
{

	$collaborativeStudy = Base::getInstance()->getStudyID();

	$base = Base::getInstance();

	$base->populateQuestionID();
	$questionID = $base->getQuestionID();



		$base = new Base();
		$userID = $base->getUserID();
		$stageID = $base->getStageID();
		$projectID = $base->getProjectID();
		$cxn = Connection::getInstance();
		$taskNum = $base->getTaskNum();




?>

<html>
<head>
	<link rel="stylesheet" href="../study_styles/custom/text.css">
	<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css"> -->
	<link rel="stylesheet" href="../study_styles/custom/background.css">
	<link rel="stylesheet" href="../study_styles/pure-release-0.5.0/buttons.css">
	<link rel="stylesheet" href="../study_styles/pure-release-0.5.0/forms.css">
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
			margin-left: 70%;

		  top: 0; left: 0; bottom: 0; // position to top left of window
			position: fixed;

    	overflow-y: scroll;


		  height:95%; //set dimensions
		  transition: width ease .5s; // fluid transition when resizing

		  /* Sass/Scss only:
		    Using a selector (.open-nav) with an "&" afterward is actually selecting
		  any parent selector. For instance, this outputs "body.open-nav .left { ... }"
		  More info: http://thesassway.com/intermediate/referencing-parent-selectors-using-ampersand
		  */
		  body.open-nav & {
		    width:250px;
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

</head>



<?php


	$connection = Connection::getInstance();
	$userID = Base::getInstance()->getUserID();
	$results = $connection->commit("SELECT * FROM users WHERE userID='$userID' AND finishIntent1='1'");

if(mysql_num_rows($results)==0){
	?>

<body class="style1">
	<div style="width:90%; margin: 0 auto">
		<p>You have finished the intention annotation portion of the task.  To proceed, please inform your proctor that you have completed this part.</p>
	</div>
</body>
</html>
	<?php
		exit();
	}else{
		$base = Base::getInstance();
		$stageID = $base->getStageID();
		Util::getInstance()->saveAction(basename( __FILE__ ),$stageID,$base);
		Util::getInstance()->moveToNextStage();

		}

}
else {
	echo "Something went wrong. Please <a href=\"../index.php\">try again </a>.\n";
}

	?>
