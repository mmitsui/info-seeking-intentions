<?php


session_start();
require_once('../core/Base.class.php');
require_once('../core/Util.class.php');
require_once('../core/Connection.class.php');
require_once('../core/Questionnaires.class.php');


function printLikertTwo($question,$key,$data){
	$pref = $key;
	echo "<div style=\"border:1px solid gray; border-right-width:0px;border-left-width:0px\">\n";
	echo "<label>$question</label>\n";
	echo "<div id=\"".$pref."_div\" class=\"container\">\n";
	echo "<div class=\"pure-g\">\n";
	$count = 1;
	foreach($data as $k=>$v){
		$style = "";
		if(($count)%2){
			$style = "style=\"background-color:#F2F2F2\"";
		}
		$countstr = "_$count";
		echo "<div $style class=\"pure-u-1-8\">";
		echo "<label for=\"".$pref."$countstr\" class=\"pure-radio\">";
		echo "<input id=\"".$pref."$countstr\" type=\"radio\" name=\"".$pref."\" value=\"$v\">$k";
		echo "</label>";
		echo "</div>\n";
		$count += 1;
	}
	echo "</div>\n";
	echo "</div>\n";
	echo "</div>\n\n";
}

Util::getInstance()->checkSession();

$base = Base::getInstance();

if (Util::getInstance()->checkCurrentPage(basename( __FILE__ )))
{
	$collaborativeStudy = Base::getInstance()->getStudyID();

	$userID = $base->getUserID();
	$connection = Connection::getInstance();
	$res = $connection->commit("SELECT `group` FROM users WHERE userID='$userID'");
	$line = mysql_fetch_array($res,MYSQL_ASSOC);
	$group = $line['group'];

	if($group=='control'){
		Util::getInstance()->moveToNextStage();
	}
	else if (isset($_POST['evalsources']))
	{
		$base = new Base();
		$stageID = $base->getStageID();

		$userID=$base->getUserID();
		$projectID=$base->getProjectID();
		$connection = Connection::getInstance();
		$bookmarks_res = $connection->commit("SELECT * FROM bookmarks_group2 ORDER BY bookmarkID");
		$N_BOOKMARKS = mysql_num_rows($bookmarks_res);

		$questionnaire = Questionnaires::getInstance();

		$ks = array("$userID","$projectID");
		$vs = array('userID','projectID');
		$time = $base->getTime();
		$date = $base->getDate();
		$timestamp = $base->getTimestamp();

		print_r($_POST);
		for($x=1;$x<=$N_BOOKMARKS;$x+=1){
			$use_information = $_POST["use_information_$x"];
			$author_qualifications = $_POST["author_qualifications_$x"];
			$rating = $_POST["rating_$x"];

			$connection->commit("INSERT INTO questionnaire_sourceratings (userID,projectID,`date`,`time`,`timestamp`,use_information,author_qualifications,rating) VALUES ('$userID','$projectID','$date','$time','$timestamp','$use_information','$author_qualifications','$rating')");
		}


		Util::getInstance()->saveAction(basename( __FILE__ ),$stageID,$base);
		Util::getInstance()->moveToNextStage();
	}
	else
	{
		$base = new Base();
		$userID = $base->getUserID();
		$stageID = $base->getStageID();
		$projectID = $base->getProjectID();

		$questionnaire = Questionnaires::getInstance();
		$questionnaire->clearCache();
		$questionnaire->populateQuestionsFromDatabase("summer2015-repeated","questionID ASC");
		$questionnaire->setBaseDirectory("../");
		$connection = Connection::getInstance();
		$bookmarks_res = $connection->commit("SELECT * FROM bookmarks_group2 ORDER BY bookmarkID");
		$N_BOOKMARKS = mysql_num_rows($bookmarks_res);



?>

<html>
<head>
	<link rel="stylesheet" href="../study_styles/pure-release-0.5.0/buttons.css">
	<link rel="stylesheet" href="../study_styles/pure-release-0.5.0/forms.css">
	<link rel="stylesheet" href="../study_styles/pure-release-0.5.0/grids-min.css">
<script type='text/javascript' src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script type='text/javascript' src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.13.1/jquery.validate.min.js"></script>
<link href="https://s3.amazonaws.com/mturk-public/bs30/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="../study_styles/custom/text.css">
<link rel="stylesheet" href="../study_styles/custom/background.css">
<script>
// When the document is ready
$(document).ready(function () {





$('#next2').show();
$('#prev2').show();
$('#begin_header').show();
$('#preview_header').hide();



$('#submitButton').hide();


$('#next2').click(function(event) {
    $('.current').removeClass('current').hide()
        .next().show().addClass('current');


    $('#instructions_panel').hide();


    if ($('.current').hasClass('last')) {




        $('#next2').attr('disabled', true);

        $('#submitButton').show();
        $('#end-alert').show();
    }else{

        $('#submitButton').hide();
        $('#end-alert').hide();
    }

    $('#prev2').attr('disabled', null);




    event.preventDefault();
    event.stopPropagation();
});

$('#prev2').click(function(event) {
    $('.current').removeClass('current').hide()
        .prev().show().addClass('current');

        $('#instructions_panel').hide();


    if ($('.current').hasClass('first')) {
    $('#instructions_panel').show();


        $('#prev2').attr('disabled', true);
        window.scrollTo(0,$('#begin_header').offset().top);
        $('#submitButton').hide();
        $('#end-alert').hide();
    }else{

        $('#submitButton').hide();
        $('#end-alert').hide();
    }

    $('#next2').attr('disabled', null);



    event.preventDefault();
    event.stopPropagation();
});


$.validator.setDefaults({
    ignore: []
});

//validation rules
$("#sum2015_qform").validate({

    submitHandler: function(form) {
        // do other things for a valid form
        $("#badinputhead").hide();
        $("#badinputfoot").hide();
        form.submit();
    },
    invalidHandler: function(event, validator) {
        // 'this' refers to the form

        var errors = validator.numberOfInvalids();
        if (errors) {
          var message = errors == 1
            ? 'You missed 1 field. It has been highlighted'
            : 'You missed ' + errors + ' fields. They have been highlighted';
          $("#badinputhead").show();
          $("#badinputfoot").show();
        } else {
          $("#badinputhead").hide();
          $("#badinputfoot").hide();
        }
    },

    rules: {


<?php

for($x=1;$x<=$N_BOOKMARKS;$x++){

	echo "\"use_information_$x\":{required: true},\n";
	echo "\"author_qualifications_$x\":{required: true},\n";
	echo "\"rating_$x\":{required: true}\n";
	if($x < $N_BOOKMARKS){
		echo ",";
	}
}

?>

		}
    ,
    messages: {

<?php
for($x=1;$x<=$N_BOOKMARKS;$x++){

	echo "\"use_information_$x\":{required: \"<span style='color:red'>Please enter your response.</span>\"},\n";
	echo "\"author_qualifications_$x\":{required: \"<span style='color:red'>Please enter your response.</span> \"},\n";
	echo "\"rating_$x\":{required: \"<span style='color:red'>Please enter your response.</span>\"}\n";
	if($x < $N_BOOKMARKS){
		echo ",\n";
	}
}
?>
}

    });

    });
</script>

</head>
<body>
	<div style="width:90%; margin: 0 auto">
		<center><h2>Collaborative Research Study Source Evaluation</h2></center>
<p>
	<strong>Questionnaire:</strong> Below are some online information sources that members of your group have already bookmarked for your project.
	Click on the title of the source (in blue) to see it online.
	It will open in a new tab.
</p>
<p>
	After you review each source, please answer the questions shown below.
</p>
<hr>
<div id="badinputhead" class="alert alert-danger" style="display:none" role="alert">Some of your inputs are blank or incorrect.  Please check your input and submit again.</div>
<form id="sum2015_qform" class="pure-form" method="post" action="evalsources.php">
<div id="main">
<div id="div0" class="first current">
<h2 id="begin_header" style="display:none">Click 'Next' to begin the task.  Submit it after you've completed all inputs.</h2></div>




<?php




// Print task
//


for($x=1;$x<=$N_BOOKMARKS;$x++){
	$line = mysql_fetch_array($bookmarks_res,MYSQL_ASSOC);
	$bookmarkID = $line['bookmarkID'];
	$url = $line['url'];
	$title = $line['title'];

	if($x == $N_BOOKMARKS){
		echo "<div id=\"div$x\" class=\"last\" style=\"display:none\">";
	}else{
		echo "<div id=\"div$x\" style=\"display:none\">";
	}



	if ($x == $N_BOOKMARKS){
		echo "<div id=\"end-alert\" style=\"display:none\" class=\"alert alert-info\" role=\"alert\">When finished, submit your results below.</div>";
	}

	echo "<input type=\"hidden\" name=\"bookmarkID_$x\" value=\"$bookmarkID\"/>";


	// Source/bookmark
	echo "<div class=\"grayrect\" style=\"font-size: 20\"><span><strong>($x/$N_BOOKMARKS) Click on this link to view the source:</strong> <a href=\"$url\" target=\"_blank\">$title</a></span></div>";
	echo "<h2 id=\"header_$x\">Answer the questions below</h2>";




	// Question 1

	echo "<div class=\"pure-form-stacked\">";
	echo "<fieldset>";
	echo "<div class=\"pure-control-group\">\n";
	echo "<div id=\"use_information_$x"."_div\">";
	echo "<label name=\"use_information_$x\">What specific information from this source would you use in your report?</label>\n";
	echo "<textarea name=\"use_information_$x\" id=\"use_information_$x\" rows=\"5\" cols=\"80\" required></textarea>\n";
	echo "<br>\n";
	echo "</div>\n";
	echo "</div>\n\n";
	// Question 2
	echo "<div class=\"pure-control-group\">\n";
	echo "<div id=\"author_qualifications_$x"."_div\">";
	echo "<label name=\"author_qualifications_$x\">What qualifications does the author of this article/website have as evidence of expertise or trustworthiness?</label>\n";
	echo "<textarea name=\"author_qualifications_$x\" id=\"author_qualifications_$x\" rows=\"5\" cols=\"80\" required></textarea>\n";
	echo "<br>\n";
	echo "</div>\n";
	echo "</div>\n\n";

	// Question 3 - Likert

	printLikertTwo("How useful is this source? Rate it:","rating_$x",array(
    "1" => "1",
    "2" => "2",
		"3" => "3",
		"4" => "4",
		"5" => "5",
	));
	echo "</fieldset>";
	echo "</div>";

	echo "</div>";

}

?>
</div>

<hr />
<button id="prev2" disabled="disabled" class="btn btn-default" style="display:none"><< Prev</button>
<button id="next2" class="btn btn-default" style="display:none">Next >></button>
<div id="badinputfoot" style="display:none" class="alert alert-danger" role="alert">Some of your inputs are blank or incorrect.  Please check your input and submit again.</div>
<style type="text/css">fieldset { padding: 10px; background:#fbfbfb; border-radius:5px; margin-bottom:5px; }
</style>
<br/><br/>
<input type="hidden" name="evalsources" value="true"/>
  <button id="submitButton" class="pure-button pure-button-primary" type="submit">Finish</button>
</form>
</div>
</body>
</html>

<?php
	}
}
else {
	echo "Something went wrong. Please <a href=\"../index.php\">try again </a>.\n";
}

	?>
