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








if($num_recruits<=$recruit_limit &&!$closed)
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
		form.action = "consent.php";
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



<body class="body" >

<br/>
<div class="panel panel-default" style="width:95%;  margin:auto">
  <div class="panel-body">
  <div id="signupIntro" align="center">
  	<h3>Research Study Registration</h3>
  	<form method="post" onsubmit="return validateForm(this)">
  		<table class="body" width=90%>
  			<tr>
  			  <td colspan=2>

    <div>
        <p>Welcome! This is the sign-up form to register for the paid research study.</p></div>
      <p>The research project,  <em>Search Intentions in Natural Settings</em>, funded by
      the National Science Foundation, seeks participants in a study of information
      seeking. Participants will conduct searches for their work in a naturalistic setting
      - i.e., their work environment - for information relating to different kinds of
      information search tasks related to their employment. Participants will first conduct
      an initial interview in which they are asked for demographic information, introduced
      to the study’s software, and asked about regular search tasks, lasting  <strong>about one hour</strong>.
      This will be followed by an experimental session. Participants will be asked to  <strong>record
        their searching activity over the course of five days</strong> and to annotate the tasks
      that they conduct which will take  <strong>about one hour each day</strong>, as well as to explain
      their search intentions at self-selected points during their tasks. Various aspects of their
      searching behavior will be recorded for subsequent analysis. The study will conclude with
      an exit interview, in which participants will be asked to analyze their search experiences
      and to give characterizations of the tasks they performed during the five days; this
      last session will last  <strong>about one hour</strong>.

      </p>

    <p>
      All volunteers for this study will receive  <strong>$100 cash</strong>  for their participation.
      Taking part in this study will help to advance the understanding of the search
      process and contribute towards development of search systems that can automatically
      adapt to a user's specific search goals.

    </p>








    <p>Requirements:
      <ul>
        <li>You must be at least 18 years old to participate.</li>
        <li>You must be a non-student and employed.</li>
        <li>Proficiency in English is required.</li>
        <li>Your work must at least occasionally entail information seeking.</li>
        <li>Intermediate typing and online search skills are required.</li>
        <li>You must use Google Chrome throughout the duration of the study.</li>
      </ul>
    </p>



    <p>You will not be offered or receive any special consideration if you take part
      in this research; it is purely voluntary. This study has been approved by the
      Rutgers Institutional Review Board (IRB Study #XX), and will be supervised by Dr.
      Nicholas Belkin (belkin@rutgers.edu) and Dr. Chirag Shah (chirags@rutgers.edu) at the
      School of Communication and Information.</p>

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
  					<td align="center" colspan=2>
        <button class='btn'>Continue</button>
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

else if (!$closed) {
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
}
?>