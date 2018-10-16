
<html>
<head>
<title>IIR: Select User For Annotation</title>
<link rel="stylesheet" href="study_styles/pure-release-0.5.0/buttons.css">
<link rel="stylesheet" href="study_styles/pure-release-0.5.0/forms.css">
<link rel="stylesheet" href="study_styles/pure-release-0.5.0/tables.css">
<link rel="stylesheet" href="study_styles/custom/text.css">


</head>
<script src="lib/jquery-2.1.3.min.js"></script>
<script type="text/javascript">

var is_ff;
var alertColor = "Red";
var okColor = "White";

function goToAnnotation(participantID,topicAreaID,taskNum,userID){
	var anoVal = $("select[participantID="+participantID.toString()+"][topicAreaID="+topicAreaID.toString()+"]").val();

	window.location.href = "http://www.coagmento.org/spring2016intent/labelAnnotationData.php?participantID="+participantID.toString()+"&questionID="+topicAreaID.toString()+"&taskNum="+taskNum.toString()+"&anoType="+anoVal+"&userID="+userID;

}


</script>
<noscript>
<style type="text/css">
.pagecontainer {display:none;}
</style>
<div class="noscriptmsg">
You don't have Javascript enabled.  You must enable it in your browser to proceed with the task.
</div>
</noscript>

<body class="style1">
  <center><h2>Select User</h2></center>
<div id="login_div" style="display:block;">

<?php

	session_start();
	require_once('core/Connection.class.php');
	require_once('core/Base.class.php');
	require_once('core/Util.class.php');
    require_once('core/Stage.class.php');

    echo "<hr><br><br>";

	$connection = Connection::getInstance();

	$query = "SELECT * FROM users WHERE userID < 500 AND participantID IS NOT NULL ORDER BY userID ASC";

	$results = $connection->commit($query);
	$base = Base::getInstance();

	if (mysql_num_rows($results) > 0) //insert session one end stage if necessary
	{


					echo "<center><table class=\"pure-table pure-table-striped\" style=\"border: 2px solid black\">";
					echo "<thead><th>Participant ID</th><th>Question ID</th><th>Total Progress</th><th>Annotation Type</th><th>Enter</th></thead>";

					echo "<tbody>";
          			while($line = mysql_fetch_array($results,MYSQL_ASSOC)){
			  				$userID = $line['userID'];
							$participantID = $line['participantID'];
			  				$topicAreaID1 = $line['topicAreaID1'];
			  				$topicAreaID2 = $line['topicAreaID2'];
							foreach(array($topicAreaID1,$topicAreaID2) as $key=>$topicAreaID){
								$taskNum = $key+1;
								echo "<tr style=\"border: 2px solid black\">";

								$total_null = 0;
								$total = 0;

								$subtotal_null = 0;
								$subtotal=0;


								$select = "<select participantID='$participantID' topicAreaID='$topicAreaID'>";

								$results_annotation = $connection->commit("SELECT * FROM video_intent_assignments WHERE userID=$userID AND questionID=$topicAreaID");
								$subtotal = mysql_num_rows($results_annotation);
								$results_annotation = $connection->commit("SELECT * FROM video_intent_assignments WHERE userID=$userID AND questionID=$topicAreaID AND queryID IS NOT NULL AND reformulationType IS NOT NULL");
								$subtotal_null = mysql_num_rows($results_annotation);
								$perc = round(floatval($subtotal_null)/floatval($subtotal)*100,2);
								$select .= "<option value='intention'>Intentions ($perc%)</option>";
								$total += $subtotal;
								$total_null += $subtotal_null;


								$results_annotation = $connection->commit("SELECT * FROM video_reformulation_history WHERE userID=$userID AND questionID=$topicAreaID");
								$subtotal = mysql_num_rows($results_annotation);
								$results_annotation = $connection->commit("SELECT * FROM video_reformulation_history WHERE userID=$userID AND questionID=$topicAreaID AND queryID1 IS NOT NULL AND queryID2 IS NOT NULL AND reformulationType1 IS NOT NULL AND reformulationType2 IS NOT NULL");
								$subtotal_null = mysql_num_rows($results_annotation);
								$perc = round(floatval($subtotal_null)/floatval($subtotal)*100,2);
								$select .= "<option value='reformulation'>Reformulation ($perc%)</option>";
								$total += $subtotal;
								$total_null += $subtotal_null;


								$results_annotation = $connection->commit("SELECT * FROM video_save_history WHERE userID=$userID AND questionID=$topicAreaID");
								$subtotal = mysql_num_rows($results_annotation);
								$results_annotation = $connection->commit("SELECT * FROM video_save_history WHERE userID=$userID AND questionID=$topicAreaID AND bookmarkID IS NOT NULL");
								$subtotal_null = mysql_num_rows($results_annotation);
								$perc = round(floatval($subtotal_null)/floatval($subtotal)*100,2);
								$select .= "<option value='save'>Save ($perc%)</option>";
								$total += $subtotal;
								$total_null += $subtotal_null;


								$results_annotation = $connection->commit("SELECT * FROM video_unsave_history WHERE userID=$userID AND questionID=$topicAreaID");
								$subtotal = mysql_num_rows($results_annotation);
								$results_annotation = $connection->commit("SELECT * FROM video_unsave_history WHERE userID=$userID AND questionID=$topicAreaID AND bookmarkID IS NOT NULL");
								$subtotal_null = mysql_num_rows($results_annotation);
								$perc = round(floatval($subtotal_null)/floatval($subtotal)*100,2);
								$select .= "<option value='unsave'>Unsave ($perc%)</option>";
								$total += $subtotal;
								$total_null += $subtotal_null;


								$select .="</select>";

								$perc = round(floatval($total_null)/floatval($total)*100,2);
								echo "<td >$participantID</td><td>$topicAreaID</td><td>$perc%</td><td>$select</td><td><button onclick=\"goToAnnotation('$participantID',$topicAreaID,$taskNum,'$userID')\">Submit</button></td>";
								echo "</tr>";

							}
          			}

					echo "</tbody></table></center>";

      }else{
              echo "<div style=\"background-color:red;\">The credentials you have entered are incorrect.  Please check your input and try again.</div>";
      }
    ?>


</body></html>
