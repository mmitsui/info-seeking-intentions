<?php
require_once('core/Connection.class.php');


$num_recruits = 0;
    $recruit_limit =90; // Current Recruitment Limit as of 10/6/2014


$cxn = Connection::getInstance();
$query = "SELECT COUNT(*) as ct from recruits WHERE userID <500";
$results = $cxn->commit($query);
$line = mysql_fetch_array($results, MYSQL_ASSOC);
$num_recruits = $line['ct'];

$closed=false;


function availableDates(){
  $cxn = Connection::getInstance();
  $query = "SELECT * FROM questionnaire_questions WHERE `key`='date_firstchoice' AND questionID=1038 AND question_cat='fall2015intent'";
  $results = $cxn->commit($query);
  $line = mysql_fetch_array($results, MYSQL_ASSOC);
  $js = json_decode($line['question_data']);
  $dates_available = array();
  foreach($js->{'options'} as $key=>$val){
    array_push($dates_available,$val);
  }





  $query = "SELECT * FROM recruits WHERE firstpreference != ''";
  $results = $cxn->commit($query);
  $dates_taken = array();
  while($line = mysql_fetch_array($results, MYSQL_ASSOC)){
    array_push($dates_taken,$line['firstpreference']);
  }


  return array_diff($dates_available,$dates_taken);

}


function allSlotsTaken(){
  return count(availableDates()) <= 0;
}

if($num_recruits<=$recruit_limit && !allSlotsTaken() &&!$closed)
{

?>
<html>
<head>
	<title>
    Research Study Registration: Introduction
    </title>

    <link rel="stylesheet" href="study_styles/bootstrap-lumen/css/bootstrap.min.css">
    <link rel="stylesheet" href="study_styles/custom/text.css">
    <link rel="stylesheet" href="styles.css">

    <script type="text/javascript">

	var alertColor = "Red";
	var okColor = "White";


	function validateForm(form)
	{
		var isValid = 1;
		form.action = "signup.php";
    return true;
	}

function isRadioSelected(radioButtons, obj)
{
    for (i=radioButtons.length-1; i > -1; i--)
        if (radioButtons[i].checked)
        {
            return true;
        }

    return false;
}





</script>
<style type="text/css">
		.cursorType{
		cursor:pointer;
		cursor:hand;
		}
</style>
</head>


<!--<body class="style1">-->
<!---->
<!--Registration for the Summer 2015 study has reached capacity and has been officially closed. We apologize for the inconvenience but would like to thank you for your interest in participating.-->
<!---->
<!---->
<!--</body>-->

<body class="body" >

<?php
//echo "<div class='panel panel-default'>";
//echo "<center><h3>Study Closed</h3></center>";
//echo "Our Spring 2015 study on user search intentions has officially been closed. We apologize for the inconvenience but would like to thank you for volunteering to participate.";
//echo "</div>";
//exit();
?>


?>
<br/>
<div class="panel panel-default" style="width:95%;  margin:auto">
  <div class="panel-body">
  <div id="signupIntro" align="center">
  	<h3>Research Study Registration</h3>
  	<form method="post" onsubmit="return validateForm(this)">
  		<table class="body" width=90%>
  			<tr>
  			  <td colspan=2>

            <div><p>Welcome! This is the sign-up form to register for the paid research study.</p></div>
            <p>The research project, <em>Information Seeking Intentions</em>, funded by the National Science Foundation, seeks Journalism/Media Studies students as participants in a study of information seeking.
                Participants will conduct two searches in an experimental setting for information relating to different kinds of information search tasks related to journalism assignments.
                Each experimental session will last <strong>about two hours</strong>, and will be held in the Communication and Interaction Laboratory in the SC&I main building.
                Participants will be asked to complete a background questionnaire about each search task and assignment, and then conduct searches for information relating to the assignment.
                After each search session participants will be asked to evaluate the information that they found and explain their search intentions at selected points.
                Various aspects of their searching behavior will be recorded for subsequent analysis. </p>

            <p>All volunteers for this study will receive <strong>$30 cash</strong>
                for their participation, and <strong>exemplary participants will receive an additional $10</strong>.
                Taking part in this study will help to advance the understanding of the search process and
                contribute towards development of search systems that can automatically adapt to a user's specific search goals. </p>

            <p>Requirements:
              <ul>
                <li>You must be at least 18 years old to participate. </li>
                <li>Proficiency in English is required.</li>
                <li>Intermediate typing and online search skills are required.</li>
                <li>Normal to corrected vision is required.</li>
                <li>You must have <em>already completed</em> either 04:567:200 (Writing for Media) or 04:567:324 (News Reporting and Writing).</li>
                      <li>Your vision must be normal or corrected-to-normal with contacts. <strong>(You should be able
                              to read text on a computer screen from 25 inches WITHOUT glasses.)</strong></li>
              </ul>
            </p>

            <!-- <p>During this study you will perform online research using an eye tracker and experimental browser plug-in and answer questionnaires.</p>
            <p>The study will last approximately 150 minutes and take place in the School of Communication and Information building.</p>
            <p>You will receive <strong>$40 cash</strong> for participating in the study.</p> -->


            <!-- <p>Requirements:
              <ul>
                <li>You must be at least 18 years old to participate.</li>
                <li>Proficiency in English is required.</li>
                <li>Intermediate typing and online search skills are required.</li>
                <li>Normal to corrected vision is required.</li>

              </ul>
            </p> -->



            <p>Choosing or declining to participate in this study will not affect your  class standing or grades at Rutgers.
                You will not be offered or receive  any special consideration if you take part in this research; it is  purely voluntary.
                This study has been approved by the Rutgers  Institutional Review Board (IRB Study #E14-136),
                and will be supervised  by Dr. Nicholas Belkin (belkin@rutgers.edu) and Dr. Chirag Shah (chirags@rutgers.edu)
                at the School of Communication and Information.</p>

            <p>For more information about this study, please send e-mail to Matthew
                Mitsui at <a href="mailto:mmitsui@scarletmail.rutgers.edu?Subject=Study%20Inquiry" target="_top">mmitsui@scarletmail.rutgers.edu</a>.
                You can also contact Matthew Mitsui to ask questions
                or get more information about the project. </p>
  				</td>
  			</tr>
  <!--
  Registration
  -->

                                                                                                                                                                                                                  </table>
                                                                                                                                                                                                                                                                                                      <hr>
                                                                                                                                                                                                                                                                                                      <table>
  			<tr>
  				<td>
                                                                                                                                                                                                                                                                                                                  <tr>
                                                                                                                                                                                                                                                                                                                  <td align="center" colspan=2>To continue with the participation registration, please click on the continue button.</td></tr>

                                                                                                                                                                                                                                                                                                                  <tr>

                                                                                                                                                                                                                                                                                                                  </table>

                                                                                                                                                                                                                                                                                                                  <table class="body" width=90%>
                                                                                                                                                                                                                                                                                                                  <tr>
                                                                                                                                                                                                                                                                                                                  <td align="center" colspan=2>
                                                                                                                                                                                                                                                                                                                  <div style="display: none; background: Red; text-align:center;" id="alertForm"><strong>Please Complete Select the Number of Participants and Try Again</strong></div>
                                                                                                                                                                                                                                                                                                                  </td>
                                                                                                                                                                                                                                                                                                                  </tr>
                                                                                                                                                                                                                                                                                                                  <tr>

  					<td align="center" colspan=2>
                <button class='btn'>Continue</button>
  					</td>
  				</tr>
  				</td>
  			</tr>
  		</table>
      </form>
</div>
</div>
</div>
</body>
</html>
<?php
}

else if (!$closed)
{
echo "<html>\n";
echo "<head>\n";
echo "<title>Collaborative Search Study: Currently Closed</title>\n";
echo "</head>\n";
echo "<body>\n";
echo "<p style='background-color:red;'>Sorry! The user study registration is currently closed.</p>\n";
echo "<br/><br/>\n";
echo "<hr/>\n";
echo "<p>The number of participants required has been reached at this point.</p>\n";
echo "<p>If more user participation is required, we will reopen the study registration and send another round of recruitment emails.</p>\n";
echo "<hr/>\n";
echo "</body>";
echo "</html>";
                                                                                                                                                                                                                                                                                                                                         }else{
                                                                                                                                                                                                                                                                                                                                         echo "<html>\n";
                                                                                                                                                                                                                                                                                                                                         echo "<head>\n";
                                                                                                                                                                                                                                                                                                                                         echo "<title>Interactive Search Study: Currently Closed</title>\n";
                                                                                                                                                                                                                                                                                                                                         echo "</head>\n";
                                                                                                                                                                                                                                                                                                                                         echo "<body class=\"body\">\n<center>\n<br/><br/>\n";
                                                                                                                                                                                                                                                                                                                                         echo "<table class=body align=center>\n";
                                                                                                                                                                                                                                                                                                                                         echo "<tr><td align=center>Our study is currently closed at this time, and we are currently not accepting new recruits.  We apologize for any inconvenience.</td></tr>\n";
                                                                                                                                                                                                                                                                                                                                         echo "</table></body>\n";
                                                                                                                                                                                                                                                                                                                                         echo "</html>";

                                                                                                                                                                                                                                                                                                                 }

?>
