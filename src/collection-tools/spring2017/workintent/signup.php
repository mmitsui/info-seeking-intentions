<?php
session_start();
require_once('core/Connection.class.php');
require_once('core/Questionnaires.class.php');


date_default_timezone_set('America/New_York');

$num_recruits = 0;
$recruit_limit =44; // Current Recruitment Limit as of 07/15/2014
$section_closed = false;
$closed = false;


$cxn = Connection::getInstance();
$query = "SELECT COUNT(*) as ct from recruits WHERE userID <500";
$results = $cxn->commit($query);
$line = mysql_fetch_array($results, MYSQL_ASSOC);
$num_recruits = $line['ct'];




function availableDates(){
    $cxn = Connection::getInstance();
    $query = "SELECT * FROM questionnaire_questions WHERE `key`='date_firstchoice' AND questionID=1038 AND question_cat='fall2015intent'";
    $results = $cxn->commit($query);
    $line = mysql_fetch_array($results, MYSQL_ASSOC);
    $js = json_decode($line['question_data']);
    $dates_available = array();
    foreach($js->{'options'} as $key=>$val){
        $thesplit = explode(",",$val);
        $monthday = $thesplit[0];

//        array_push($dates_available,$val);
        if(strtotime($monthday)-strtotime("today midnight")>=86400){
            array_push($dates_available,$val);
        }
    }




    $query = "SELECT * FROM recruits WHERE date_firstchoice != ''";
    $results = $cxn->commit($query);
    $dates_taken = array();
    while($line = mysql_fetch_array($results, MYSQL_ASSOC)){
        array_push($dates_taken,$line['date_firstchoice']);
    }


    return array_diff($dates_available,$dates_taken);

}





function availableDates2(){
    $cxn = Connection::getInstance();
    $query = "SELECT * FROM questionnaire_questions WHERE `key`='date_secondchoice' AND questionID=1091 AND question_cat='fall2015intent'";
    $results = $cxn->commit($query);
    $line = mysql_fetch_array($results, MYSQL_ASSOC);
    $js = json_decode($line['question_data']);
    $dates_available = array();
    foreach($js->{'options'} as $key=>$val){
        $thesplit = explode(",",$val);
        $monthday = $thesplit[0];

        array_push($dates_available,$val);
//        if(strtotime($monthday)-strtotime("today midnight")>=86400){
//            array_push($dates_available,$val);
//        }
    }

    $query = "SELECT * FROM recruits WHERE date_firstchoice != ''";
    $results = $cxn->commit($query);
    $dates_taken = array();
    while($line = mysql_fetch_array($results, MYSQL_ASSOC)){
        array_push($dates_taken,$line['date_secondchoice']);
    }


    return array_diff($dates_available,$dates_taken);
}


function allSlotsTaken(){
    return count(availableDates()) <= 0;
}




if(!isset($_POST['consentRead'])){

    ?>

    <html>
    <head>
    </head>
    <body>
    <h3>Search Intentions Study: Read Consent Form!!!!</h3>
    <hr>
    <p>You have to first read the consent page and agree to the conditions before registering for participating in the study.</p>
    <p>Please visit <a href="http://coagmento.org/workintent/consent.php">consent form</a> first, read and accept the study conditions.</p>
    </body>
    </html>

    <?php
    exit();

}

if($num_recruits<=$recruit_limit && !$closed && !$section_closed && !allSlotsTaken())
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


            var validateDates = function(){
                var date1 = parseInt($('#date_firstchoice_1 option:selected').attr('val'));
                var date2 = parseInt($('#date_secondchoice_1 option:selected').attr('val'));


                if(date2-date1!=864000 && date2-date1!=860400){

//                if(date2-date1 != 259200){
                    if($(this).is($('#date_firstchoice_1'))){
                        $("#date_secondchoice_1").val($("#date_secondchoice_1 option:first").val());

                        $("#date_secondchoice_span").html("Please choose the Monday 10 days after your pre-task interview.");
//                        $("#date_secondchoice_span").html("Please choose the Monday after your pre-task interview.");
                        $("#date_firstchoice_span").html("");

                    }else{
                        $("#date_firstchoice_1").val($("#date_firstchoice_1 option:first").val());
                        $("#date_firstchoice_span").html("Please choose the Friday 10 days before your pre-task interview.");
//                        $("#date_firstchoice_span").html("Please choose the Friday before your pre-task interview.");
                        $("#date_secondchoice_span").html("");

                    }
                }else{
                    $("#date_firstchoice_span").html("");

                    $("#date_secondchoice_span").html("");

                }

            }



		$().ready(function(){
			$.validator.addMethod("notEqualTo", function (value, element, param)
			{
			    var target = $(param);
			    if (value) return value != target.val();
			    else return this.optional(element);
			}, "Does not match");

			$('#date_firstchoice_1').change(validateDates);
            $('#date_secondchoice_1').change(validateDates);
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
                study_source_1: {
                    required: true,
                },
                interview_medium_1:{
                required:true
                },
                medium_credentials_1:
                    {
                        required: true
                    },


                date_firstchoice_1: {
                    required:true
                 },

                date_secondchoice_1: {
                    required:true
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
                study_source_1: {
                    required: "<span style='color:red'>Please how you discovered this study.</span>",
                },
                 date_firstchoice_1: {
                 	required: "<span style='color:red'>Please enter a date.</span>",
                 },

                date_secondchoice_1: {
                    required: "<span style='color:red'>Please enter a date.</span>",
                },
            reEmail_1: {
                required: "<span style='color:red'>Please enter your e-mail address.</span>",
                    email: "<span style='color:red'>Please enter a valid e-mail address.</span>",
                    equalTo: "<span style='color:red'>Please enter the same e-mail address again.</span>",
            },
                interview_medium_1:{
                    required: "<span style='color:red'>Please enter your preferred method of contact.</span>",
                },
                medium_credentials_1:
                    {
                        required: "<span style='color:red'>Please enter your username.</span>",
                    },

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
				<li>Use this form to register for the paid research study on <em>Search Intentions in Natural Settings</em>.</li>
				<li>Please fill out the information below then click Submit.</li>
                    <li>You will then receive a confirmation e-mail about completing an entry questionnaire.</li>
				<li>Afterward the entry questionnaire, you will receive a confirmation email within 24-48 hours with details about downloading and installing the software for the study.</li>

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


                                    <div>
                                        <p>Welcome! This is the sign-up form to register for the paid research study.</p>
                                    </div>
                                    <p>

                                        The research project, <i>Search Intentions in Natural Settings</i>, funded by the National Science Foundation, seeks
                                        participants in a study of information seeking. Participants will conduct searches for their work in a naturalistic
                                        setting - i.e., their work environment - for information relating to different kinds of information search tasks
                                        related to their employment. Participants will first conduct an initial interview in which they are asked for
                                        demographic information, introduced to the study’s software, and asked about regular search tasks, lasting
                                        <strong>about one hour</strong>. This will be followed by an experimental session.
                                        Participants will be asked to <strong>record their searching activity over the course of five days</strong> and to
                                        annotate the tasks that they conduct which will take <strong>about one hour each day</strong>, as well as to explain
                                        their search intentions at self-selected points during their tasks. Various aspects of their searching
                                        behavior will be recorded for subsequent analysis. The study will conclude with an exit interview,
                                        in which participants will be asked to analyze their search experiences and to give characterizations of the tasks they performed during the five days; this last session will last about one hour.

                                    </p>

                                    <p>
                                        All volunteers for this study will receive <strong>$95.00</strong> cash for their participation. Taking part in this study will
                                        help to advance the understanding of the search process and contribute towards development of search systems
                                        that can automatically adapt to a user's specific search goals.

                                    </p>








                                    <p>Requirements:
                                    <ul>
                                        <li>You must be at least 18 years old to participate.</li>
                                        <li>You must be a non-student and employed.</li>
                                        <li>Your work must at least occasionally entail information seeking.</li>
                                        <li>Proficiency in English is required.</li>
                                        <li>Intermediate typing and online search skills are required.</li>
                                        <li>You must use Google Chrome throughout the duration of the study.</li>
                                    </ul>
                                    </p>



                                    <p>
                                        You will not be offered or receive any special consideration if you take part in this research; it is purely
                                        voluntary. This study has been approved by the Rutgers Institutional Review Board (IRB Study #18-057), and will be
                                        supervised by Dr. Nicholas Belkin (belkin@rutgers.edu) and Dr. Chirag Shah (chirags@rutgers.edu) at the School
                                        of Communication and Information.
                                    </p>

                                    <p>For more information about this study, please send e-mail to Matthew Mitsui at
                                        <a href="mailto:mmitsui@scarletmail.rutgers.edu?Subject=Study%20Inquiry" target="_top">mmitsui@scarletmail.rutgers.edu</a>.
                                        You can also contact Matthew Mitsui to ask questions or get more information about the project. </p>


								</td>
							</tr>
	  					</table>
					</div>


<form id="spr2015_regform" class="pure-form" method="post" action="signupGroup.php">
<?php
echo "<input type=\"hidden\" name=\"num_users\" value=\"$NUM_USERS\">";

for($x=1;$x<=$NUM_USERS;$x++){

  echo "<h3>Participant Form</h3>";

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
  echo "<label for=\"email1_$x\">Email Address</label>";
  echo "<input id=\"email1_$x\" name=\"email1_$x\" type=\"text\" placeholder=\"Primary Email\" required>";
  echo "</div>";

  echo "<div class=\"pure-control-group\">";
  echo "<label for=\"reEmail_$x\">Confirm Email</label>";
  echo "<input id=\"reEmail_$x\" name=\"reEmail_$x\" type=\"text\" placeholder=\"Confirm Email\" required>";
  echo "</div>";


    echo "<div class=\"pure-control-group\">";
    echo "<label for=\"study_source_$x\">How did you find out about this study? (Facebook, Twitter, Mailing List, etc.)</label>";
    echo "<input id=\"study_source_$x\" name=\"study_source_$x\" type=\"text\" size='50' placeholder=\"Source\" required>";
    echo "</div>";

    echo "<br>";


    echo "<div class=\"pure-control-group\">";
    echo "<label for=\"interview_medium_$x\">For this study, we would like to conduct an entry interview and an exit interview.  For these, we would like to use Skype, Google Hangouts, or any medium with equivalent video and audio capabilities.  Which works best for you?</label>";
    echo "<select id=\"interview_medium_$x\" name=\"interview_medium_$x\">";
    echo "<option disabled selected value> -- select an option -- </option>";
    echo "<option value='Skype'>Skype</option>";
    echo "<option value='Google Hangouts'>Google Hangouts</option>";
    echo "<option value='Other'>Other</option>";
    echo "</select>";

    echo "</div>";

    echo "<div class=\"pure-control-group\">";
    echo "<label for=\"medium_credentials_$x\">For the above, provide the username we can use to contact you (e.g., Skype Name). If you selected 'Other', please also provide the name of the application.</label>";
    echo "<input id=\"medium_credentials_$x\" name=\"medium_credentials_$x\" type=\"text\" placeholder=\"Username (& Application)\" required>";
    echo "</div>";



  echo "</fieldset>";
  echo "</div>";
  if($NUM_USERS >1){
    echo "<hr>";
  }


  if(isset($_POST['readConsent']) && $_POST['readConsent']){
      echo "<input type='hidden' name='consent_furtheruse' value='1'>";

  }else{
      echo "<input type='hidden' name='consent_furtheruse' value='0'>";
  }


//Demographic Survey


    ?>

    <div class="pure-control-group">
        <div id="date_firstchoice_1_div"><label name="date_firstchoice_1">Please choose a date for your pre-task interview (all listed times are in EST):</label>
            <span style="color:red" id="date_firstchoice_span"></span>
            <select name="date_firstchoice_1" id="date_firstchoice_1" required>
                <option disabled selected>--Select one--</option>
                <?php

                foreach(availableDates() as $d){
                    $thesplit = explode(",",$d);
                    $monthday = $thesplit[0];
                    $t = strtotime($monthday);

                    echo "<option val='$t'>";
                    echo "$d";
                    echo "</option>";
                }
                ?>
            </select>
            <br>
        </div>
    </div>



    <div class="pure-control-group">
        <div id="date_secondchoice_1_div"><label name="date_secondchoice_1">Please choose a date for your post-task interview (this must be on the Monday after your pre-task interview; all listed times are in EST):</label>
            <span style="color:red" id="date_secondchoice_span"></span>
            <select name="date_secondchoice_1" id="date_secondchoice_1" required>
                <option disabled selected>--Select one--</option>
                <?php

                foreach(availableDates2() as $d){

                    $thesplit = explode(",",$d);
                    $monthday = $thesplit[0];
                    $t = strtotime($monthday);




                    echo "<option val='$t'>";
                    echo "$d";
                    echo "</option>";
                }
                ?>
            </select>
            <br>
        </div>
    </div>



    <?php




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
}
else{


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
