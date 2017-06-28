<?php
	session_start();
	require_once('../core/Connection.class.php');
	require_once('../core/Base.class.php');
	require_once('../core/Action.class.php');
	require_once('../core/Util.class.php');
	
	Util::getInstance()->checkSession();
	
	if (Util::getInstance()->checkCurrentPage(basename( __FILE__ )))
	{ 
		if (isset($_POST['topic']))
		   {
			
			$base = new Base();
               
            $query = "SELECT inputName FROM questions_topic WHERE questionID != 5 ORDER BY questionID ASC";
            $connection = Connection::getInstance();
            $results = $connection->commit($query);
            $answers = array();
               
               while($line = mysql_fetch_array($results, MYSQL_ASSOC)){
               		if($line['inputName'] != 'instructionLocation' && $line['inputName'] != 'instructionLocation_text' ){
                     	$answers[$line['inputName']]=$_POST[$line['inputName']];
                    }else if($line['inputName'] == 'instructionLocation' && $_POST['researchInstruction'] == 'Yes'){
                    	    $answers[$line['inputName']]=$_POST[$line['inputName']];
                    }else if($line['inputName'] == 'instructionLocation_text' && $_POST['researchInstruction'] == 'Yes'){
                            $answers[$line['inputName']]=$_POST[$line['inputName']];
                    }
                }
            
            $answers['instructionLocation_text']=addslashes($_POST['instructionLocation_text']);
            $answers['instructionCoverage']=addslashes($_POST['instructionCoverage']);

			

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
             $query = "INSERT INTO questionnaire_topic ".$keys_str." VALUES ".$values_str;
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
<title>Questionnaire: Topic
</title>
<link rel="stylesheet" href="../study_styles/pure-release-0.5.0/buttons.css">
<link rel="stylesheet" href="../study_styles/pure-release-0.5.0/forms.css">
<link rel="stylesheet" href="../study_styles/custom/text.css">

<script type="text/javascript" src="js/util.js"></script>
<script type="text/javascript">


var alertColor = "Red";
	var okColor = "White";
	
	
	function init(){
		document.getElementById("researchInstruction_select").selectedIndex = -1;
	}
	
	function validateField(field)
	{	
		if (field.value.trim() == "") 
		{
			changeColor(field,alertColor);
			return false;
		}
		else
		{
			changeColor(field,okColor);
			return true;
		}
	}
	
	function changeColor(field,color) 
	{
		field.style.backgroundColor = color; 
	}
	
	function validate(form)
	{
        
        var result = 1;
        
        <?php
        
        $query = "SELECT inputName FROM questions_topic WHERE questionID NOT IN (3,4,5) ORDER BY questionID ASC";
        $connection = Connection::getInstance();
        $results = $connection->commit($query);
        
        while($line = mysql_fetch_array($results, MYSQL_ASSOC)){
             echo "result = isItemSelected(form.".$line['inputName'].") && result;\n";
        }
        ?>
        
        var e = document.getElementById("myform").elements;
//        for(i = 0; i < e.length; i++){
//        console.log(e[i].name);
//        }
        var e;
        result = result && (form.researchInstruction.selectedIndex >=0);
        var x = document.getElementById("researchInstruction_select").value;
		if(x == "Yes" || x == "No"){
			var x = document.getElementById("researchInstruction_select").value;
			if(x == "Yes"){
				result = result && isItemSelected(form.instructionLocation);
				if(document.getElementById("option-four").checked){
// 								console.log("BEFORE3"+validateField(form.instructionLocation_text));

					e = validateField(form.instructionLocation_text);
 					result = result && e;
				}
				e = validateField(form.instructionCoverage);
			}
// 											console.log("BEFORE3"+validateField(form.instructionCoverage));
			
			result = result && e;
		}

		
		
		
		
		
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
	
	
	
	
	
	
	
	function showHideSelect(){
	
		var x = document.getElementById("researchInstruction_select").value;
		if(x == "Yes"){
			document.getElementById("row1").style.display="block";
			document.getElementById("row2").style.display="block";
		}else if (x == "No"){
			document.getElementById("row1").style.display="none";
			document.getElementById("row2").style.display="none";
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

<body class="body" onload="init()">
<center>
	<br/>
	<form action="q_topic.php" method="post" onsubmit="return validate(this)" id="myform">
      <table class="body" width=85%>
		<tr><th colspan=3>Please answer the following questions on the scale of 1 to 5, 1 being lowest and 5 being highest.</th></tr>
		<tr><td><br/></td></tr>
<tr><td colspan=3><div style="display: none; background: Red; text-align:center;" id="alert"><strong>You MUST answer ALL questions!</strong></div></td></tr>

<?php
    $query = "SELECT * FROM questions_topic WHERE questionID<='2' ORDER BY questionID ASC";
    $connection = Connection::getInstance();
    $results = $connection->commit($query);
    while($line = mysql_fetch_array($results, MYSQL_ASSOC)){
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
    
    $query = "SELECT * FROM questions_topic WHERE questionID<='5' AND questionID >='3' ORDER BY questionID ASC";
    $connection = Connection::getInstance();
    $results = $connection->commit($query);
    $ct = 1;
    while($line = mysql_fetch_array($results, MYSQL_ASSOC)){
    	if($ct==1){
    		echo "<tr bgcolor=".getColor($index)."><td colspan=3>".strval($line['questionID']).". ".$line['question']."<br>";
//     		$index += 1;
			echo "<select id=\"researchInstruction_select\" name=\"".$line['inputName']."\" onchange=\"showHideSelect()\">";
			echo "<option>Yes</option>";
			echo "<option>No</option>";
			echo "</select>";
    		echo "</td></tr>";
    	}else if($ct==2){
    		echo "<tr id=\"row1\" style=\"display:none\" bgcolor=".getColor($index)."><td colspan=3>".strval($line['questionID']-1)."A. ".$line['question']."<br>";
//     		$index += 1;
    		
    		echo "<label for=\"option-one\" class=\"pure-radio\">\n";
			echo "<input type=\"radio\" id=\"option-one\" name=\"".$line['inputName']."\" value=\"High school class\" />"."High school class"."<br>\n";        	
    		echo "</label>\n";
//     		$index += 1;
    		
    		echo "<label for=\"option-two\" class=\"pure-radio\">\n";
			echo "<input type=\"radio\" id=\"option-two\" name=\"".$line['inputName']."\" value=\"College class\" />"."College class"."<br>\n";        	
    		echo "</label>\n";
//     		$index += 1;
    		
    		echo "<label for=\"option-three\" class=\"pure-radio\">\n";
			echo "<input type=\"radio\" id=\"option-three\" name=\"".$line['inputName']."\" value=\"Both high school and college classes\" />"."Both high school and college classes"."<br>\n";        	
    		echo "</label>\n";
//     		$index += 1;
    		
    		echo "<label for=\"option-four\" class=\"pure-radio\">\n";
			echo "<input type=\"radio\" id=\"option-four\" name=\"".$line['inputName']."\" value=\"Other\" />"."Other  ";        	
    		echo "</label>\n";
    		echo "<br> If other: <input name=\"".$line['inputName']."_text\" type=\"text\" rows=\"1\" cols=\"50\">\n";
    		echo "</td></tr>";
    	}else{
    	    echo "<tr id=\"row2\" style=\"display:none\" bgcolor=".getColor($index)."><td colspan=3>".strval($line['questionID']-2)."B. ".$line['question']."<br>";
    	    echo "<input name=\"".$line['inputName']."\" type=\"text\" rows=\"1\" cols=\"50\">";
			echo "</td></tr>";
    	}
    	$ct += 1;
    	$index += 1;
    }
        ?>
        </table>
        <hr><br>
        <table class="body" width=85%>
        <tr><th colspan=3>When doing library research, how often do you:</th></tr>
        <?php
        $index = 0;
        $query = "SELECT * FROM questions_topic WHERE questionID >='6' ORDER BY questionID ASC";
		$connection = Connection::getInstance();
		$results = $connection->commit($query);
		while($line = mysql_fetch_array($results, MYSQL_ASSOC)){
			echo "<tr bgcolor=".getColor($index)."><td colspan=3>".strval($line['questionID']-2).". ".$line['question']."<br>";			
			echo "<label for=\"".$line['inputName']."-option-one\" class=\"pure-radio\">\n";
			echo "<input type=\"radio\" id=\"".$line['inputName']."-option-one\" name=\"".$line['inputName']."\" value=\"1\" />"."1 - Never"."<br>\n";        	
    		echo "</label>\n";
    		
    		echo "<label for=\"".$line['inputName']."-option-two\" class=\"pure-radio\">\n";
			echo "<input type=\"radio\" id=\"".$line['inputName']."-option-two\" name=\"".$line['inputName']."\" value=\"2\" />"."2 - Rarely"."<br>\n";        	
    		echo "</label>\n";
    		
    		echo "<label for=\"".$line['inputName']."-option-three\" class=\"pure-radio\">\n";
			echo "<input type=\"radio\" id=\"".$line['inputName']."-option-three\" name=\"".$line['inputName']."\" value=\"3\" />"."3 - Sometimes"."<br>\n";        	
    		echo "</label>\n";
    		
    		echo "<label for=\"".$line['inputName']."-option-four\" class=\"pure-radio\">\n";
			echo "<input type=\"radio\" id=\"".$line['inputName']."-option-four\" name=\"".$line['inputName']."\" value=\"4\" />"."4 - Often"."<br>\n";        	
    		echo "</label>\n";
    		
    		echo "<label for=\"".$line['inputName']."-option-five\" class=\"pure-radio\">\n";
			echo "<input type=\"radio\" id=\"".$line['inputName']."-option-five\" name=\"".$line['inputName']."\" value=\"5\" />"."5 - Always"."<br>\n";        	
    		echo "</label>\n";
    		
			echo "</td></tr>";
			$index += 1;

		}
    
        ?>
			
		<tr><td colspan=3 align=center><br/><input type="hidden" name="topic" value="true"/>
									 		<input type="hidden" name="localTime" value=""/>
							 				<input type="hidden" name="localDate" value=""/>
							 				<input type="hidden" name="localTimestamp" value=""/>
											<button type="submit" class="pure-button pure-button-primary">Submit</button>
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
