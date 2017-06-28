<?php
session_start();
require_once('core/Connection.class.php');
require_once('core/Questionnaires.class.php');


date_default_timezone_set('America/New_York');

$num_recruits = 0;
$recruit_limit =90; // Current Recruitment Limit as of 07/15/2014
$section_closed = false;
$closed=true;
$closed = false;


$cxn = Connection::getInstance();
$query = "SELECT COUNT(*) as ct from recruits WHERE userID <500";
$results = $cxn->commit($query);
$line = mysql_fetch_array($results, MYSQL_ASSOC);
$num_recruits = $line['ct'];






if($num_recruits<=$recruit_limit && !$closed && !$section_closed)
{
	if(1)
	{
        $NUM_USERS = 1;
        $questionnaire = Questionnaires::getInstance();
				$questionnaire->clearCache();
        $questionnaire->populateQuestionsFromDatabase("fall2015intent","questionID ASC");


	?>
<html>
<head>

  <link rel="stylesheet" href="study_styles/bootstrap-lumen/css/bootstrap.min.css">
  <link rel="stylesheet" href="study_styles/custom/text.css">
  <link rel="stylesheet" href="styles.css">
	<title>
			Research Study Registration
    </title>

    <style>
    select {
      font-size:13px;
    }
    </style>
    <?php echo $questionnaire->printPreamble();?>


		<script type="text/javascript">
		$().ready(function(){
			$.validator.addMethod("notEqualTo", function (value, element, param)
			{
			    var target = $(param);
			    if (value) return value != target.val();
			    else return this.optional(element);
			}, "Does not match");
		});
		</script>
    <script>

        jQuery.validator.addMethod("rankedorder", function(value, element) {
            return isRankedOrderValid(value);
            }, "<span style='color:red'>Please specify a ranked order according to the description above.</span>");
        $().ready(function(){$("#spr2015_regform").validate({ignore:"",
            rules: {firstName_1: "required",
            lastName_1: "required",
            age_1: {
                required: true,
                number: true
            },
            email1_1: {
                required: true,
                email: true
            },
            reEmail_1: {
                required: true,
                email: true,
                equalTo: "#email1_1"
            }
            },
            messages: {
            firstName_1: {required:"<span style='color:red'>Please enter your first name.</span>"},
            lastName_1: {required:"<span style='color:red'>Please enter your last name.</span>"},
            age_1: {
                required:"<span style='color:red'>Please enter your age.</span>",
                    number:"<span style='color:red'>Please enter a number.</span>"
            },
            email1_1: {
                required: "<span style='color:red'>Please enter your e-mail address.</span>",
                    email: "<span style='color:red'>Please enter a valid e-mail address.</span>"
            },
            reEmail_1: {
                required: "<span style='color:red'>Please enter your e-mail address.</span>",
                    email: "<span style='color:red'>Please enter a valid e-mail address.</span>",
                    equalTo: "<span style='color:red'>Please enter the same e-mail address again.</span>",
            }
            },
            errorPlacement: function(error, element)
        {
            if ( element.is(":radio") )
            {
                error.appendTo( element.parents('.container') );
            }
            else
            { // This is the default behavior
                error.insertAfter( element );
            }
        }});
        });

    </script>
<script type="text/javascript">
	function viewDetails(check)
	{
		if (check.checked)
			document.getElementById("singleStudyDetails").style.display = "block";
		else
			document.getElementById("singleStudyDetails").style.display = "none";
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

  <div class="panel panel-default" style="width:95%; margin:auto">
    <div class="panel-body">

<div id="signupForm" align="center">
	<h3>Research Study Registration</h3>
		<table class="body" width=90%>
			<tr>
			  <td colspan=2>
				<ul>
				<li>Use this form to register for the paid research study on <em>Information Seeking Intentions</em>.</li>
				<li>Please fill out the information below then click Submit.</li>
				<li>You will receive a confirmation email within 24-48 hours with details about time, date, and location of your session.</li>

				<li><a href="mailto:mmitsui@scarletmail.rutgers.edu?subject=Study inquiry">Contact us</a> if you have any questions.</li>
				</ul>
				</td>
			</tr>
<!--
Registration
-->
			<tr>
				<td>
					<p><strong>Check here to if you would like to read recruitment details again. </strong><input type="checkbox" name="viewInstructionsCheckSingle" id="viewInstructionsCheckSingle" onclick="viewDetails(this)" /></p>
					<br />
					<div style="display: none; background: #F2F2F2; text-align:center; border-style:solid; width:70%; border-color:blue; padding:25px;" id="singleStudyDetails">
	 					<table class="body" width="100%">
							<tr>
								<td>


                  <p>Welcome! This is the sign-up form to register for the paid research study.</p>
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

                                  </ul>
                                  </p>

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
	  					</table>
					</div>


<form id="spr2015_regform" class="pure-form" method="post" action="signupGroup.php">
<?php
echo "<input type=\"hidden\" name=\"num_users\" value=\"$NUM_USERS\">";

for($x=1;$x<=$NUM_USERS;$x++){

  echo "<h3>Participant $x</h3>";

  echo "<div class=\"pure-form-aligned\">";
  echo "<fieldset>";
  echo "<div class=\"pure-control-group\">";
  echo "<label for=\"firstName_$x\">First Name</label>";
  echo "<input id=\"firstName_$x\" name=\"firstName_$x\" type=\"text\" placeholder=\"First Name\" required>";
  echo "</div>";

  echo "<div class=\"pure-control-group\">";
  echo "<label for=\"lastName_$x\">Last Name</label>";
  echo "<input id=\"lastName_$x\" name=\"lastName_$x\" type=\"text\" placeholder=\"Last Name\" required>";
  echo "</div>";

	echo "<div class=\"pure-control-group\">";
  echo "<label for=\"age_$x\">Age</label>";
  echo "<input id=\"age_$x\" name=\"age_$x\" type=\"text\" placeholder=\"Age\" required>";
  echo "</div>";

  echo "<div class=\"pure-control-group\">";
  echo "<label for=\"email1_$x\">Rutgers Email</label>";
  echo "<input id=\"email1_$x\" name=\"email1_$x\" type=\"text\" placeholder=\"Primary Email\" required>";
  echo "</div>";

  echo "<div class=\"pure-control-group\">";
  echo "<label for=\"reEmail_$x\">Confirm Email</label>";
  echo "<input id=\"reEmail_$x\" name=\"reEmail_$x\" type=\"text\" placeholder=\"Confirm Email\" required>";
  echo "</div>";



  echo "</fieldset>";
  echo "</div>";
  if($NUM_USERS >1){
    echo "<hr>";
  }


//Demographic Survey






echo "</fieldset>";
echo "</div>";


}
?>
        <hr>
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
	else
	{
?>
	<html>
	<head>
	</head>
	<body>
	<h3>Interactive Search Study: Complete Previous Page!!!!</h3>
	<hr>
	<p>You have to first submit the number of registrants for participating in the study.</p>
	<p>Please visit the <a href="http://coagmento.org/workintent/signup_intro.php">signup introduction</a> first.</p>
	</body>
	</html>
<?php
	}
}

else if ($closed)
{
	echo "<html>\n";
  echo "<head>\n";
  echo "<title>Interactive Search Study: Currently Closed</title>\n";
  echo "</head>\n";
  echo "<body class=\"body\">\n<center>\n<br/><br/>\n";
  echo "<table class=body align=center>\n";
  echo "<tr><td align=center>Our study is currently closed at this time, and we are currently not accepting new recruits.  We apologize for any inconvenience.</td></tr>\n";
  echo "</table></body>\n";
  echo "</html>";



}else if($section_closed){
  echo "<html>\n";
  echo "<head>\n";
  echo "<title>Interactive Search Study: Currently Closed</title>\n";
  echo "</head>\n";
  echo "<body>\n";
  echo "<br/><br/>\n";
  echo "<hr/>\n";
  echo "<p>The number of required for this type of grouping has been reached at this point.</p>\n";
  echo "<p>If you wanted to register as a pair but would still like to participate, please register as individual users.</p>\n";
  echo "<hr/>\n";
  echo "</body>";
  echo "</html>";
}else{


  echo "<html>\n";
  echo "<head>\n";
  echo "<title>Interactive Search Study: Currently Closed</title>\n";
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
