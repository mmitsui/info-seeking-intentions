<?php
	session_start();
	require_once('../core/Connection.class.php');
	require_once('../core/Base.class.php');
	require_once('../core/Action.class.php');
	require_once('../core/Util.class.php');
	
	Util::getInstance()->checkSession();
	
	if (Util::getInstance()->checkCurrentPage(basename( __FILE__ )))
	{
        $projectID = Base::getInstance()->getProjectID();
        $query = "SELECT numUsers from users WHERE projectID='$projectID'";
        $connection = Connection::getInstance();
        $results = $connection->commit($query);
        $line = mysql_fetch_array($results, MYSQL_ASSOC);
        $num_users = $line['numUsers'];

        if($num_users <2){
            Util::getInstance()->moveToNextStage();
        }
        else if (isset($_POST['collaborative']))
        {
			
			$base = new Base();
            
            $query = "SELECT inputName FROM questions_collaborative ORDER BY questionID ASC";
            $connection = Connection::getInstance();
            $results = $connection->commit($query);
            $answers = array();
            
            while($line = mysql_fetch_array($results, MYSQL_ASSOC)){
                $answers[$line['inputName']]=$_POST[$line['inputName']];
            }
            
            
			
            
			$localTime = $_POST['localTime'];
			$localDate = $_POST['localDate'];
			$localTimestamp =  $_POST['localTimestamp'];
            
			$projectID = $base->getProjectID();
			$userID = $base->getUserID();
			$time = $base->getTime();
			$date = $base->getDate();
			$timestamp = $base->getTimestamp();
			$stageID = $base->getStageID();
            $keys_str = "(projectID,userID,stageID,`date`,`time`,`timestamp`,`localDate`,`localTime`,`localTimestamp`";
            $values_str = "('$projectID','$userID','$stageID','$date','$time','$timestamp','$localDate','$localTime','$localTimestamp'";
            foreach($answers as $key=>$value){
                $keys_str = $keys_str . "," . $key;
                $values_str = $values_str . ",'$value'";
                
            }
            $keys_str = $keys_str . ")";
            $values_str = $values_str . ")";
            $query = "INSERT INTO questionnaire_collaborative ".$keys_str." VALUES ".$values_str;
            var_dump($answers);
            echo $query;
            
			$connection = Connection::getInstance();
			$results = $connection->commit($query);
			$lastID = $connection->getLastID();
            
			//Save action
			//Util::getInstance()->saveAction(basename( __FILE__ ),$stageID,$base);
			Util::getInstance()->saveActionWithLocalTime(basename( __FILE__ ),$lastID,$base,$localTime,$localDate,$localTimestamp);
			
			//Next stage
			Util::getInstance()->moveToNextStage();
		}
		else {
    ?>
<html>
<head>
<title>Questionnaire: Collaborative
</title>
<link rel="stylesheet" href="../study_styles/pure-release-0.5.0/buttons.css">
<link rel="stylesheet" href="../study_styles/pure-release-0.5.0/forms.css">
<link rel="stylesheet" href="../study_styles/custom/text.css">

<script type="text/javascript" src="js/util.js"></script>
<script type="text/javascript">
function validate(form)
{
    
    var result = 1;
    
    <?php
    
    $query = "SELECT inputName FROM questions_collaborative ORDER BY questionID ASC";
    $connection = Connection::getInstance();
    $results = $connection->commit($query);
    
    while($line = mysql_fetch_array($results, MYSQL_ASSOC)){
        echo "result = result && isItemSelected(form.".$line['inputName'].");\n";
    }
    ?>
    
    if (!result)
    {
        document.getElementById("alert").style.display = "block";
        window.scrollTo(0,0);
        return false;
    }
    else
    {
        setLocalTime(form);
        return true;
    }
}
</script>

</head>
<?php
	$index=0;
	$color="\"White\"";
    
	function getColor($value)
	{
		if (($value % 2) == 0)
			$color="\"#F2F2F2\"";
		else
			$color="\"White\"";
        
		return $color;
	}
	?>

<body class="body">
<center>
<br/>
<form action="q_collaborative.php" method="post" onsubmit="return validate(this)">
<table class="body" width=85%>
<tr><th colspan=3>Please answer the following questions on the scale of 1 to 5, 1 being lowest and 5 being highest.</th></tr>
<tr><td><br/></td></tr>
<tr><td colspan=3><div style="display: none; background: Red; text-align:center;" id="alert"><strong>You MUST answer ALL questions!</strong></div></td></tr>

<tr bgcolor="#F2F2F2"><td colspan=3>How did you feel while collaborating through the Coagmento tool?</td></tr><tr bgcolor="White"><td align=center>Not absorbed intensely <input type="radio" name="collaborationAbsorbed" value="1" />1
<input type="radio" name="collaborationAbsorbed" value="2" />2
<input type="radio" name="collaborationAbsorbed" value="3" />3
<input type="radio" name="collaborationAbsorbed" value="4" />4
<input type="radio" name="collaborationAbsorbed" value="5" />5 Absorbed intensely
</td></tr>

<tr bgcolor="White"><td align=center>Attention was not focused<input type="radio" name="collaborationFocused" value="1" />1
<input type="radio" name="collaborationFocused" value="2" />2
<input type="radio" name="collaborationFocused" value="3" />3
<input type="radio" name="collaborationFocused" value="4" />4
<input type="radio" name="collaborationFocused" value="5" />5 Attention was focused
</td></tr>

<tr bgcolor="White"><td align=center>Did not concentrate fully <input type="radio" name="collaborationConcentrated" value="1" />1
<input type="radio" name="collaborationConcentrated" value="2" />2
<input type="radio" name="collaborationConcentrated" value="3" />3
<input type="radio" name="collaborationConcentrated" value="4" />4
<input type="radio" name="collaborationConcentrated" value="5" />5 Concentrated fully
</td></tr>

<tr bgcolor="White"><td align=center>Not deeply engrossed (involved)<input type="radio" name="collaborationEngrossed" value="1" />1
<input type="radio" name="collaborationEngrossed" value="2" />2
<input type="radio" name="collaborationEngrossed" value="3" />3
<input type="radio" name="collaborationEngrossed" value="4" />4
<input type="radio" name="collaborationEngrossed" value="5" />5 Deeply engrossed
</td></tr>




<tr><td colspan=3 align=center><br/><input type="hidden" name="collaborative" value="true"/>
<input type="hidden" name="localTime" value=""/>
<input type="hidden" name="localDate" value=""/>
<input type="hidden" name="localTimestamp" value=""/>
<button type="submit" class="pure-button pure-button-primary">Next</button>
</td></tr>
</table>
</form>
<br/>
</center>
</body>
</html>
<?php
    }
	}
	else {
		echo "<tr><td>Something went wrong. Please <a href=\"../index.php\">try again</a>.</td></tr>\n";
	}
	
    ?>
