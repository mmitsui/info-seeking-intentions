<?php
	session_start();
	require_once('../core/Connection.class.php');
	require_once('../core/Base.class.php');
	require_once('../core/Action.class.php');
	require_once('../core/Util.class.php');
	
	Util::getInstance()->checkSession();
	
	if (Util::getInstance()->checkCurrentPage(basename( __FILE__ )))
	{
		if (isset($_POST['system']))
        {
			
			$base = new Base();
            
            $query = "SELECT inputName FROM questions_system ORDER BY questionID ASC";
            $connection = Connection::getInstance();
            $results = $connection->commit($query);
            $answers = array();
            
            while($line = mysql_fetch_array($results, MYSQL_ASSOC)){
                if($line['inputName'] == 'mostLiked' || $line['inputName'] == 'leastLiked'){
                    $answers[$line['inputName']]=addslashes($_POST[$line['inputName']]);
                }else{
                    $answers[$line['inputName']]=$_POST[$line['inputName']];
                }
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
            $query = "INSERT INTO questionnaire_system ".$keys_str." VALUES ".$values_str;
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
<title>Questionnaire: System
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
    
    $query = "SELECT inputName FROM questions_system ORDER BY questionID ASC";
    $connection = Connection::getInstance();
    $results = $connection->commit($query);
    
    while($line = mysql_fetch_array($results, MYSQL_ASSOC)){
        if($line['inputName'] != 'mostLiked' && $line['inputName'] != 'leastLiked'){
            echo "result = result && isItemSelected(form.".$line['inputName'].");\n";
        }else{
            echo "result = result && form.".$line['inputName'].".value.trim() !== '';\n";
        
        }
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
<form action="q_system.php" method="post" onsubmit="return validate(this)">
<table class="body" width=85%>
<tr><th colspan=3>Please answer the following questions on the scale of 1 to 5, 1 being lowest and 5 being highest.</th></tr>
<tr><td><br/></td></tr>
<tr><td colspan=3><div style="display: none; background: Red; text-align:center;" id="alert"><strong>You MUST answer ALL questions!</strong></div></td></tr>

<?php
    $query = "SELECT * FROM questions_system ORDER BY questionID ASC";
    $connection = Connection::getInstance();
    $results = $connection->commit($query);
    while($line = mysql_fetch_array($results, MYSQL_ASSOC)){
        if($line['questionID'] > 6){
            break;
        }
        echo "<tr bgcolor=".getColor($index)."><td colspan=3>".strval($line['questionID']).". ".$line['question']."</td></tr>";
        $index += 1;
        echo "<tr bgcolor=".getColor($index)."><td align=center>";
        echo $line['lowtext'];
        for($x=1;$x<=5;$x++){
            echo "<input type=\"radio\" name=\"".$line['inputName']."\" value=\"".strval($x)."\" />".strval($x)."\n";
        }
        echo $line['hitext'];
        echo "</td></tr>";
        $index += 1;
    }
    ?>

<tr bgcolor="#F2F2F2"><td colspan=3>7. Using the Coagmento system was...</td></tr><tr bgcolor="White"><td align=center>Uninteresting <input type="radio" name="systemInteresting" value="1" />1
<input type="radio" name="systemInteresting" value="2" />2
<input type="radio" name="systemInteresting" value="3" />3
<input type="radio" name="systemInteresting" value="4" />4
<input type="radio" name="systemInteresting" value="5" />5 Interesting
</td></tr>

<tr bgcolor="White"><td align=center>Not Enjoyable<input type="radio" name="systemEnjoyable" value="1" />1
<input type="radio" name="systemEnjoyable" value="2" />2
<input type="radio" name="systemEnjoyable" value="3" />3
<input type="radio" name="systemEnjoyable" value="4" />4
<input type="radio" name="systemEnjoyable" value="5" />5 Enjoyable
</td></tr>

<tr bgcolor="White"><td align=center>Dull <input type="radio" name="systemExciting" value="1" />1
<input type="radio" name="systemExciting" value="2" />2
<input type="radio" name="systemExciting" value="3" />3
<input type="radio" name="systemExciting" value="4" />4
<input type="radio" name="systemExciting" value="5" />5 Exciting
</td></tr>

<tr bgcolor="White"><td align=center>Not Fun<input type="radio" name="systemFun" value="1" />1
<input type="radio" name="systemFun" value="2" />2
<input type="radio" name="systemFun" value="3" />3
<input type="radio" name="systemFun" value="4" />4
<input type="radio" name="systemFun" value="5" />5 Fun
</td></tr>


<tr bgcolor="#F2F2F2"><td colspan=3>8. What did you like most about the Coagmento tool?</td></tr><tr bgcolor="White"><td align=center><textarea name="leastLiked" cols=55 rows=3></textarea>
</td></tr>

<tr bgcolor="#F2F2F2"><td colspan=3>9. What did you like least about the Coagmento tool?</td></tr><tr bgcolor="White"><td align=center><textarea name="mostLiked" cols=55 rows=3></textarea>
</td></tr>






<tr><td colspan=3 align=center><br/><input type="hidden" name="system" value="true"/>
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
