<?php
	require_once('core/Connection.class.php');




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
    $closed=false;
    if(!$closed){
?>
<html>
<head>
	<title>
        CONSENT FORM TO TAKE PART IN A
        MOBILE/ELECTRONIC DEVICE OR TECHNOLOGY STUDY
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
	}
	
	

	
	

</script>
<style type="text/css">
		.cursorType{
		cursor:pointer;
		cursor:hand;
		}
</style>
</head>
<body class="style1">
<br/>
<div class="panel panel-default" style="width:95%;  margin:auto">
    <div class="panel-body">
<div id="signupConsent">
	<h3>CONSENT FORM TO TAKE PART IN A
        MOBILE/ELECTRONIC DEVICE OR TECHNOLOGY STUDY</h3>
	<form method="post" onsubmit="return validateForm(this)">



        <div class="panel panel-primary">
            <div class="panel-heading">
                CONSENT   FOR   FURTHER   USE   OF   RECORDED   DATA
            </div>
            <div class="panel-body">
                <p>We would like to ask your permission to use the data collected in this investigation for further research, for demonstration in teaching, and for presentation during conferences. As described above, the search log data and interview audio recordings will not include your name or other identifying information. If you do not want to give your permission for us to use your data, you may still participate in the study and receive compensation if you complete the study.</p>

                <p>Use of your data could entail any of the following:</p>


                <p>1. Researchers, both at Rutgers and at other institutions, re-analyzing the search logs, annotations of search tasks and intentions, and interview audio recordings. Such use would be only on approval of the Principal Investigator.</p>

                    <p>2. Playing excerpts of audio recordings of the interview during presentations of the research results of this project at scholarly conferences or other research or educational meetings.</p>

                <p>If you agree to our making use of the data recorded during your tasks as specified above, please click Accept below. If you do not wish to permit such use, do not click Accept below. If you do not click Accept, the logs and recordings will be treated as previously described.</p>

                <p>If you have any questions about the study or study procedures, you may contact us at Dr. Nicholas J Belkin, 4 Huntington Street, New Brunswick, NJ 08901, 848-932-7608, belkin@comminfo.rutgers.edu; Dr. Chirag Shah, 4 Huntington Street, New Brunswick, NJ 08901, 848-932-8807, chirags@rutgers.edu.</p>

            </div>
        </div>

        <p>
            Please retain a copy of this form for your records. If you are 18 years of age or older, understand the statements above, and will consent to participate in the study, click on the "Agree" button. If not, please close this tab.
        </p>

		<table class="style1" width=90%>

	
			<tr>
				<td>
					<p><input type="checkbox" name="readConsent" id="readConsent" onclick="enableAccept(this)" /><strong> I HAVE READ AND UNDERSTOOD THE TERMS AND CONDITIONS AND LIKE TO PROVIDE MY CONSENT FOR FURTHER USE OF RECORDED DATA. </strong></p>
				</td
			</tr>	
			<tr>
					<td align="center" colspan=2>
                        <button class="btn btn-default" type="submit">Continue</button>

                        <input type="hidden" id="consentRead" name="consentRead"/>
					</td>
					
			</tr>
		</table>
    </form>
</div>
    </div></div>
</body>
</html>

<?php
    }else{
        echo "<html>\n";
        echo "<head>\n";
        echo "<title>Exploratory Search Study: Currently Closed</title>\n";
        echo "</head>\n";
        echo "<body class=\"body\">\n<center>\n<br/><br/>\n";
        echo "<table class=body align=center>\n";
        echo "<tr><td align=center>Our study is currently closed at this time, and we are currently not accepting new recruits.  We apologize for any inconvenience.</td></tr>\n";
        echo "</table></body>\n";
        echo "</html>";
    }
?>


