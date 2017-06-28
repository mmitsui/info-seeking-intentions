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

	if (isset($_POST['pretask_q']))
	{
		$base = new Base();
		$stageID = $base->getStageID();

		$userID=$base->getUserID();
		$projectID=$base->getProjectID();


		/*

		SUBMIT ANSWER!


		*/

		$questionnaire = Questionnaires::getInstance();
		foreach($_POST as $k=>$v){
			if ($k != "pretask_q"){
				$questionnaire->addAnswer($k,$v);
			}
		}
		$age = $_POST['age'];
		$search_years = $_POST['search_years'];
		$questionnaire->commitAnswersToDatabase(array("$userID","$projectID","$stageID","$age","$search_years"),array('userID','projectID','stageID',"age","search_years"),'questionnaire_demographic');

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
		$questionnaire->populateQuestionsFromDatabase("fall2015intent-demog","questionID ASC");
		$questionnaire->setBaseDirectory("../");




?>

<html>
<head>
	<link rel="stylesheet" href="../study_styles/bootstrap-lumen/css/bootstrap.min.css">
	<link rel="stylesheet" href="../study_styles/custom/text.css">
	<link rel="stylesheet" href="../styles.css">
	<script type="text/javascript" src="../lib/jquery-2.1.3.min.js"></script>
	<title>
		Research Study
    </title>


    <style  type="text/css">
    select {
      font-size:13px;
    }
    </style>
    <?php echo $questionnaire->printPreamble();?>

    <script type="text/javascript">


		function toggleLanguage(){

			if($('#language_english-Yes').is(':checked')){
				$('#language_div').hide();
				$('#english_speak_div').hide();
				$('#english_understandspoken_div').hide();
				$('#english_read_div').hide();
				$('#english_write_div').hide();
			}else if($('#language_english-No').is(':checked')){
				$('#language_div').show();
				$('#english_speak_div').show();
				$('#english_understandspoken_div').show();
				$('#english_read_div').show();
				$('#english_write_div').show();
			}else{
				$('#language_div').hide();
				$('#english_speak_div').hide();
				$('#english_understandspoken_div').hide();
				$('#english_read_div').hide();
				$('#english_write_div').hide();
			}



		}
    <?php

		$rules = "
				age: {
					required: true,
					number: true
				},
				search_years: {
					required: true,
					number: true
				},
				language:{
					required:{
						depends:function(){
								return $('#language_english-No').is(':checked');
						}
					}
				},
				english_speak:{
					required:{
						depends:function(){
								return $('#language_english-No').is(':checked');
						}
					}
				},
				english_understandspoken:{
					required:{
						depends:function(){
								return $('#language_english-No').is(':checked');
						}
					}
				},
				english_read:{
					required:{
						depends:function(){
								return $('#language_english-No').is(':checked');
						}
					}
				},
				english_write:{
					required:{
						depends:function(){
								return $('#language_english-No').is(':checked');
						}
					}
				}
				";
				// date_firstchoice_1: {
				//
				// 	notEqualTo: \"#date_secondchoice_1\"
				// },
				// date_secondchoice_1: {
				//
				// 	notEqualTo: \"#date_firstchoice_1\"
				// },

				$messages = "
						age: {
							required:\"<span style='color:red'>Please enter your age.</span>\",
							number:\"<span style='color:red'>Please enter a number.</span>\"
						},
						search_years: {
							required:\"<span style='color:red'>Please enter the years.</span>\",
							number:\"<span style='color:red'>Please enter a number.</span>\"
						},
						";

    echo $questionnaire->printValidation("sum2015_qform",$rules,$messages);
    ?>


    </script>


<style type="text/css">
		.cursorType{
		cursor:pointer;
		cursor:hand;
		}
</style>
</head>
<body class="body">

	<div class="panel panel-default" style="width:95%;  margin:auto">
		<div class="panel-body">
<div style="width:90%; margin: 0 auto">
	<center><h2>Background Questionnaire</h2></center>

	<p>Below are some questions regarding how you typically conduct searching on the internet.  Please complete them to the best of your ability.</p>
	<hr/>

<br/>

<form id="sum2015_qform" class="pure-form" method="post" action="pretask_q.php">
	<div class="pure-form-stacked">
		<fieldset>
	<div class="pure-control-group"><label name="gender_radio">Gender</label><div id="gender_div" class="container"><label for="gender" class="pure-radio"><input id="gender-M" type="radio" name="gender" value="M" required> Male <input id="gender-F" type="radio" name="gender" value="F" > Female </label></div></div><br><div class="pure-control-group"><label for="age">Age (Years)</label><input id="age" name="age" type="text" placeholder="Age" required></div><br/><div class="pure-control-group"><label for="search_years">How many years have you been doing online searching?</label><input id="search_years" name="search_years" type="text" placeholder="Years" required></div><br/><div >
	<label>Please indicate your level of expertise with searching:</label>
	<div id="search_expertise_div" class="container">
	<div class="pure-g">
	<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="search_expertise_1" class="pure-radio"><input id="search_expertise_1" type="radio" name="search_expertise" value="1">1 (Novice)</label></div>
	<div  class="pure-u-1-8"><label for="search_expertise_2" class="pure-radio"><input id="search_expertise_2" type="radio" name="search_expertise" value="2">2</label></div>
	<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="search_expertise_3" class="pure-radio"><input id="search_expertise_3" type="radio" name="search_expertise" value="3">3</label></div>
	<div  class="pure-u-1-8"><label for="search_expertise_4" class="pure-radio"><input id="search_expertise_4" type="radio" name="search_expertise" value="4">4</label></div>
	<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="search_expertise_5" class="pure-radio"><input id="search_expertise_5" type="radio" name="search_expertise" value="5">5</label></div>
	<div  class="pure-u-1-8"><label for="search_expertise_6" class="pure-radio"><input id="search_expertise_6" type="radio" name="search_expertise" value="6">6</label></div>
	<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="search_expertise_7" class="pure-radio"><input id="search_expertise_7" type="radio" name="search_expertise" value="7">7 (Expert)</label></div>
	</div>
	</div>
	</div>

	<br><div >
	<label>How often do you search using search engines or other online search tools?</label>
	<div id="search_frequency_div" class="container">
	<div class="pure-g">
	<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="search_frequency_1" class="pure-radio"><input id="search_frequency_1" type="radio" name="search_frequency" value="1">1 (Never)</label></div>
	<div  class="pure-u-1-8"><label for="search_frequency_2" class="pure-radio"><input id="search_frequency_2" type="radio" name="search_frequency" value="2">2 (5-11 times a year)</label></div>
	<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="search_frequency_3" class="pure-radio"><input id="search_frequency_3" type="radio" name="search_frequency" value="3">3 (1-2 times a month)</label></div>
	<div  class="pure-u-1-8"><label for="search_frequency_4" class="pure-radio"><input id="search_frequency_4" type="radio" name="search_frequency" value="4">4 (1-2 days a week)</label></div>
	<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="search_frequency_5" class="pure-radio"><input id="search_frequency_5" type="radio" name="search_frequency" value="5">5 (3-5 days a week)</label></div>
	<div  class="pure-u-1-8"><label for="search_frequency_6" class="pure-radio"><input id="search_frequency_6" type="radio" name="search_frequency" value="6">6 (Once a day)</label></div>
	<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="search_frequency_7" class="pure-radio"><input id="search_frequency_7" type="radio" name="search_frequency" value="7">7 (Several times a day)</label></div>
	</div>
	</div>
	</div>

	<br><div >
	<label>When you do searching, how often can you usually find what you look for?</label>
	<div id="search_success_div" class="container">
	<div class="pure-g">
	<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="search_success_1" class="pure-radio"><input id="search_success_1" type="radio" name="search_success" value="1">1 (Rarely)</label></div>
	<div  class="pure-u-1-8"><label for="search_success_2" class="pure-radio"><input id="search_success_2" type="radio" name="search_success" value="2">2</label></div>
	<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="search_success_3" class="pure-radio"><input id="search_success_3" type="radio" name="search_success" value="3">3</label></div>
	<div  class="pure-u-1-8"><label for="search_success_4" class="pure-radio"><input id="search_success_4" type="radio" name="search_success" value="4">4 (Sometimes)</label></div>
	<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="search_success_5" class="pure-radio"><input id="search_success_5" type="radio" name="search_success" value="5">5</label></div>
	<div  class="pure-u-1-8"><label for="search_success_6" class="pure-radio"><input id="search_success_6" type="radio" name="search_success" value="6">6</label></div>
	<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="search_success_7" class="pure-radio"><input id="search_success_7" type="radio" name="search_success" value="7">7 (Often)</label></div>
	</div>
	</div>
	</div>

	<br><div >
	<label>How often have you conducted online searching for journalism-related tasks?</label>
	<div id="search_journalism_div" class="container">
	<div class="pure-g">
	<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="search_journalism_1" class="pure-radio"><input id="search_journalism_1" type="radio" name="search_journalism" value="1">1 (Never)</label></div>
	<div  class="pure-u-1-8"><label for="search_journalism_2" class="pure-radio"><input id="search_journalism_2" type="radio" name="search_journalism" value="2">2 (Once or twice)</label></div>
	<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="search_journalism_3" class="pure-radio"><input id="search_journalism_3" type="radio" name="search_journalism" value="3">3 (Three to five times)</label></div>
	<div  class="pure-u-1-8"><label for="search_journalism_4" class="pure-radio"><input id="search_journalism_4" type="radio" name="search_journalism" value="4">4 (More often)</label></div>
	</div>
	</div>
	</div>

	<br>
	<div class="pure-control-group"><label name="language_english_radio">Is your native language English?</label>
		<div id="language_english_div" class="container">
			<label for="language_english" class="pure-radio">
				<input id="language_english-Yes" type="radio" name="language_english" value="Yes" required onclick="toggleLanguage()"> Yes
				<input id="language_english-No" type="radio" name="language_english" value="No" onclick="toggleLanguage()"> No
			</label>
		</div>
	</div>
	<br>

	<div class="pure-control-group">
	<div id="language_div" style="display:none">
		<hr>
		<label name="language">What is your native language?</label>
	<textarea name="language" id="language" rows="5" cols="80" required></textarea>
	<br>
	</div>
	</div>

	<div >

	<div id="english_speak_div" class="container" style="display:none">

		<strong><p>If your native language is not English, please self evaluate your English proficiency:</p></strong>
		<br>
		<label>How well do you speak English?</label>
	<div class="pure-g">
	<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="english_speak_1" class="pure-radio"><input id="english_speak_1" type="radio" name="english_speak" value="1">1 (Not at all)</label></div>
	<div  class="pure-u-1-8"><label for="english_speak_2" class="pure-radio"><input id="english_speak_2" type="radio" name="english_speak" value="2">2</label></div>
	<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="english_speak_3" class="pure-radio"><input id="english_speak_3" type="radio" name="english_speak" value="3">3 (Not well)</label></div>
	<div  class="pure-u-1-8"><label for="english_speak_4" class="pure-radio"><input id="english_speak_4" type="radio" name="english_speak" value="4">4</label></div>
	<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="english_speak_5" class="pure-radio"><input id="english_speak_5" type="radio" name="english_speak" value="5">5 (Well)</label></div>
	<div  class="pure-u-1-8"><label for="english_speak_6" class="pure-radio"><input id="english_speak_6" type="radio" name="english_speak" value="6">6</label></div>
	<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="english_speak_7" class="pure-radio"><input id="english_speak_7" type="radio" name="english_speak" value="7">7 (Very well)</label></div>
	</div>
	</div>
	</div>

	<div >

	<div id="english_understandspoken_div" class="container" style="display:none">
		<br>
		<label>How well do you understand spoken English?</label>
	<div class="pure-g">
	<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="english_understandspoken_1" class="pure-radio"><input id="english_understandspoken_1" type="radio" name="english_understandspoken" value="1">1 (Not at all)</label></div>
	<div  class="pure-u-1-8"><label for="english_understandspoken_2" class="pure-radio"><input id="english_understandspoken_2" type="radio" name="english_understandspoken" value="2">2</label></div>
	<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="english_understandspoken_3" class="pure-radio"><input id="english_understandspoken_3" type="radio" name="english_understandspoken" value="3">3 (Not well)</label></div>
	<div  class="pure-u-1-8"><label for="english_understandspoken_4" class="pure-radio"><input id="english_understandspoken_4" type="radio" name="english_understandspoken" value="4">4</label></div>
	<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="english_understandspoken_5" class="pure-radio"><input id="english_understandspoken_5" type="radio" name="english_understandspoken" value="5">5 (Well)</label></div>
	<div  class="pure-u-1-8"><label for="english_understandspoken_6" class="pure-radio"><input id="english_understandspoken_6" type="radio" name="english_understandspoken" value="6">6</label></div>
	<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="english_understandspoken_7" class="pure-radio"><input id="english_understandspoken_7" type="radio" name="english_understandspoken" value="7">7 (Very well)</label></div>
	</div>
	</div>
	</div>

	<div >

	<div id="english_read_div" class="container" style="display:none">
		<br>
		<label>How well do you read English?</label>
	<div class="pure-g">
	<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="english_read_1" class="pure-radio"><input id="english_read_1" type="radio" name="english_read" value="1">1 (Not at all)</label></div>
	<div  class="pure-u-1-8"><label for="english_read_2" class="pure-radio"><input id="english_read_2" type="radio" name="english_read" value="2">2</label></div>
	<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="english_read_3" class="pure-radio"><input id="english_read_3" type="radio" name="english_read" value="3">3 (Not well)</label></div>
	<div  class="pure-u-1-8"><label for="english_read_4" class="pure-radio"><input id="english_read_4" type="radio" name="english_read" value="4">4</label></div>
	<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="english_read_5" class="pure-radio"><input id="english_read_5" type="radio" name="english_read" value="5">5 (Well)</label></div>
	<div  class="pure-u-1-8"><label for="english_read_6" class="pure-radio"><input id="english_read_6" type="radio" name="english_read" value="6">6</label></div>
	<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="english_read_7" class="pure-radio"><input id="english_read_7" type="radio" name="english_read" value="7">7 (Very well)</label></div>
	</div>
	</div>
	</div>

	<div >

	<div id="english_write_div" class="container" style="display:none">
		<br>
		<label>How well do you write English?</label>
	<div class="pure-g">
	<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="english_write_1" class="pure-radio"><input id="english_write_1" type="radio" name="english_write" value="1">1 (Not at all)</label></div>
	<div  class="pure-u-1-8"><label for="english_write_2" class="pure-radio"><input id="english_write_2" type="radio" name="english_write" value="2">2</label></div>
	<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="english_write_3" class="pure-radio"><input id="english_write_3" type="radio" name="english_write" value="3">3 (Not well)</label></div>
	<div  class="pure-u-1-8"><label for="english_write_4" class="pure-radio"><input id="english_write_4" type="radio" name="english_write" value="4">4</label></div>
	<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="english_write_5" class="pure-radio"><input id="english_write_5" type="radio" name="english_write" value="5">5 (Well)</label></div>
	<div  class="pure-u-1-8"><label for="english_write_6" class="pure-radio"><input id="english_write_6" type="radio" name="english_write" value="6">6</label></div>
	<div style="background-color:#F2F2F2" class="pure-u-1-8"><label for="english_write_7" class="pure-radio"><input id="english_write_7" type="radio" name="english_write" value="7">7 (Very well)</label></div>
	</div>
	</div>
	</div>

	<br></fieldset>
</div>

<hr/>

<input type="hidden" name="pretask_q" value="true"/>
  <button class="btn btn-primary" type="submit">Submit</button>
</form>
</div>
</div>
</div>
</body>
<?php $questionnaire->printPostamble();?>
</html>


<?php
	}
}
else {
	echo "Something went wrong. Please <a href=\"../index.php\">try again </a>.\n";
}

	?>
