<?php

	session_start();
	require_once('core/Connection.class.php');
    require_once('core/Base.class.php');
    require_once('core/Util.class.php');


  date_default_timezone_set('America/New_York');



  function clean_timestr($start_string){
    return preg_replace('/[[:^print:]]/', '', $start_string);
  }


  function timestrToInt($start_string){
    $start_time = preg_replace('/[[:^print:]]/', '', $start_string);

    if(substr_count($start_time,":")<2){
      while(substr_count($start_time,":") != 2){
        $start_time = "00:". $start_time;
      }
    }


    if(strpos($start_time,".")!==false){
      $start_time = substr($start_time, 0, strpos($start_time, "."));
    }

    // $start_time = substr($start_time,0,-2);
    $start_time = strtotime($start_time) - strtotime('TODAY');

    return $start_time;
  }

  function intToPHPTime($seconds){
    $t = round($seconds);
    return sprintf('%02d:%02d:%02d', ($t/3600),($t/60%60), $t%60);
  }

  $cxn = Connection::getInstance();


  $submitted = 0;
  $message = '';

  $participantID = $_GET['participantID'];
  $questionID = $_GET['questionID'];
  $taskNum = $_GET['taskNum'];
  $anoType = $_GET['anoType'];
  $res = $cxn->commit("SELECT * FROM users WHERE participantID='$participantID'");
  $userID = $_GET['userID'];
//  $userID = -1;
//  while($line = mysql_fetch_array($res,MYSQL_ASSOC)){
//    $userID = $line['userID'];
//    break;
//  }

  $filename = "user$userID"."task$taskNum".".mp4";



  $assignmentID='';
  if(isset($_POST['intention']) or isset($_POST['reformulation']) or isset($_POST['save']) or isset($_POST['unsave'])){
    $submitted = 1;
    $assignmentID=$_POST['assignmentID'];
  }


  if(isset($_POST['intention'])){
    $message = "Your changes were made to the intention.";
  }else if(isset($_POST['reformulation'])){
    $message = "Your changes were made to the reformulation.";
  }else if(isset($_POST['save'])){
    $message = "Your changes were made to bookmark save action.";
  } else if(isset($_POST['unsave'])){
    $message = "Your changes were made to bookmark unsave action.";
  }


  $query = '';

  if(isset($_POST['intention'])){
    $queryID = $_POST['queryID'];
    $reformulationType = trim($_POST['reformulationType']);
    $reformulationType="'$reformulationType'";
    $query = "UPDATE video_intent_assignments SET queryID=$queryID,reformulationType=$reformulationType WHERE assignmentID=$assignmentID";
  }else if(isset($_POST['reformulation'])){
    $queryID1 = $_POST['queryID1'];
    $queryID2 = $_POST['queryID2'];

    $reformulationType1 = trim($_POST['reformulationType1']);
    $reformulationType1="'$reformulationType1'";

    $reformulationType2 = trim($_POST['reformulationType2']);
    $reformulationType2="'$reformulationType2'";

    $query = "UPDATE video_reformulation_history SET queryID1=$queryID1,queryID2=$queryID2,reformulationType1=$reformulationType1,reformulationType2=$reformulationType2 WHERE assignmentID=$assignmentID";
  }else if(isset($_POST['save'])){
    $bookmarkID = $_POST['bookmarkID'];
    $query = "UPDATE video_save_history SET bookmarkID=$bookmarkID WHERE assignmentID=$assignmentID";
  } else if(isset($_POST['unsave'])){
    $bookmarkID = $_POST['bookmarkID'];
    $query = "UPDATE video_unsave_history SET bookmarkID=$bookmarkID WHERE assignmentID=$assignmentID";
  }



  if(isset($_POST['intention']) or isset($_POST['reformulation']) or isset($_POST['save']) or isset($_POST['unsave'])){
    $cxn->commit($query);
  }

  $questionID = $_GET['questionID'];
  $query = "";
  //Secondary update: saves time and eliminates chance of error
  if(isset($_POST['intention'])){
    $queryID = $_POST['queryID'];
    $reformulationType = trim($_POST['reformulationType']);

    $reformulationType="'$reformulationType'";


    $query = "SELECT * FROM video_intent_assignments WHERE assignmentID=$assignmentID";
    $results = $cxn->commit($query);
    $row = mysql_fetch_array($results,MYSQL_ASSOC);
    $time_start_str = $row['time_start'];
    $query = "UPDATE video_reformulation_history SET queryID1=$queryID,reformulationType1=$reformulationType WHERE userID=$userID AND questionID=$questionID AND time_start_1='$time_start_str'";
//    echo "1".$query;
    $cxn->commit($query);
    $query = "UPDATE video_reformulation_history SET queryID2=$queryID,reformulationType2=$reformulationType WHERE userID=$userID AND questionID=$questionID AND time_start_2='$time_start_str'";
//    echo "2".$query;
    $cxn->commit($query);

  }else if(isset($_POST['reformulation'])){
    $queryID1 = $_POST['queryID1'];
    $queryID2 = $_POST['queryID2'];
    $reformulationType1 = trim($_POST['reformulationType1']);
    $reformulationType1="'$reformulationType1'";

    $reformulationType2 = trim($_POST['reformulationType2']);
    $reformulationType2="'$reformulationType2'";



    $query = "SELECT * FROM video_reformulation_history WHERE assignmentID=$assignmentID";
    $results = $cxn->commit($query);
    $row = mysql_fetch_array($results,MYSQL_ASSOC);
    $time_start_str_1 = $row['time_start_1'];
    $time_start_str_2 = $row['time_start_2'];
    $query = "UPDATE video_intent_assignments SET queryID=$queryID1,reformulationType=$reformulationType1 WHERE userID=$userID AND questionID=$questionID AND time_start='$time_start_str_1'";
//    echo "3".$query;
    $cxn->commit($query);
    $query = "UPDATE video_intent_assignments SET queryID=$queryID2,reformulationType=$reformulationType2 WHERE userID=$userID AND questionID=$questionID AND time_start='$time_start_str_2'";
//    echo "4".$query;
    $cxn->commit($query);


  }







  $questionID= $_GET['questionID'];
  $query = "SELECT * from recruits WHERE userID='$userID'";
  $cxn = Connection::getInstance();
  $results = $cxn->commit($query);
  if(mysql_num_rows($results)==0){
    echo "<center><h2>Edit Users</h2></center>\n";
    echo "<p>The user you specified cannot be found.  Please go back and select another user.</p>\n";
  }else{

    $line = mysql_fetch_array($results,MYSQL_ASSOC);
    $firstname = $line['firstName'];
    $lastname = $line['lastName'];
    $projectID = $line['projectID'];

    $query = "SELECT * from users WHERE userID='$userID'";
    $cxn = Connection::getInstance();
    $results = $cxn->commit($query);
    $line = mysql_fetch_array($results,MYSQL_ASSOC);

    $table = '';

    if($anoType=='intention'){
      $table = 'video_intent_assignments';
    }else if($anoType=='reformulation'){
      $table = 'video_reformulation_history';
    }else if($anoType=='save'){
      $table = 'video_save_history';
    }else if($anoType=='unsave'){
      $table = 'video_unsave_history';
    }
    $results = $cxn->commit("SELECT * FROM $table WHERE userID=$userID and questionID=$questionID ORDER BY assignmentID ASC");

    $time_to_int = array();
    while($line=mysql_fetch_array($results,MYSQL_ASSOC)){
      if($_GET['anoType']=='reformulation'){

        $startTime1 = timestrToInt(clean_timestr($line['time_start_1']));
        $startTime2 = timestrToInt(clean_timestr($line['time_start_2']));

        if($anoType=='save' or $anoType=='unsave'){
          $startTime1 -=5;
          $startTime2 -=5;
        }else{
          $startTime1 -=2;
          $startTime2 -=2;
        }

        $time_to_int[$line['time_start_1']] = $startTime1;
        $time_to_int[$line['time_start_2']] = $startTime2;

      }else{

        $startTime = timestrToInt(clean_timestr($line['time_start']));

        if($anoType=='save' or $anoType=='unsave'){
          $startTime -=5;
        }else{
          $startTime -=2;
        }

        $time_to_int[$line['time_start']] = $startTime;

      }



    }



    $query = '';

    if($anoType=='save' or $anoType=='unsave'){
      $query = "SELECT * FROM bookmarks WHERE userID=$userID AND questionID=$questionID";
    }else if($anoType=='intention' or $anoType=='reformulation'){
      $query = "SELECT * FROM queries WHERE userID=$userID AND questionID=$questionID";
    }

    $results = $cxn->commit($query);
    $id_values = array();

    while($line = mysql_fetch_array($results,MYSQL_ASSOC)){

      if($anoType=='save' or $anoType=='unsave'){
        $id_values[] = $line['bookmarkID'];
      }else if($anoType=='intention' or $anoType=='reformulation'){
        $id_values[] = $line['queryID'];
      }

    }

    if($anoType=='intention' or $anoType=='reformulation'){
      $query = "SELECT * FROM pages WHERE userID=$userID AND questionID=$questionID";
      $results = $cxn->commit($query);
      while($line = mysql_fetch_array($results,MYSQL_ASSOC)){
          $id_values[] = $line['pageID'];
      }
    }
    ?>


<html>
<head>
  <title>IIR: Label Annotation Data</title>

  <link rel="stylesheet" href="study_styles/bootstrap-lumen/css/bootstrap.min.css">
  <link rel="stylesheet" href="study_styles/custom/text.css">
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

</head>
<noscript>
  <style type="text/css">
    .pagecontainer {display:none;}
  </style>
  <div class="noscriptmsg">
    You don't have Javascript enabled.  You must enable it in your browser to proceed with the task.
  </div>
</noscript>

<script src="lib/jquery-2.1.3.min.js"></script>
<script type="text/javascript">

  var timestring_to_int = <?php echo json_encode($time_to_int);?>;
  var item_ids = <?php echo json_encode($id_values);?>;

  function playvideo(timestring){

    document.getElementById("session_video").currentTime = timestring_to_int[timestring];
//    document.getElementById("video-overlay").style.display="none";
//    document.getElementById("session_video_overlay").style.display="block";
    document.getElementById("session_video").play();

  }

  function valid_input(datatype,num){

    var valid=false;
    var validstr = "";
    var valid_reformulations = ["None","Generalization","New","Repeat","Specialization","Spelling Correction","Stem Identical","Word Substitution"];

    if(datatype=='intention'){
      var noquery = !$.trim($("#"+num.toString()+"-queryID").val());

      var queryvalue = $.trim($("#"+num.toString()+"-queryID").val());

      var reformulationvalue = $.trim($("#"+num.toString()+"-reformulationType").val());

      valid = !noquery && (item_ids.indexOf(queryvalue)!=-1);
      if(!valid){
        validstr = validstr + "Bad queryID value. ";
      }
      valid = valid && ((valid_reformulations.indexOf(reformulationvalue)!= -1) );
      if(!valid){
        validstr = validstr + "Bad reformulation type.";
      }


    }else if(datatype=='reformulation'){
      var noquery1 = !$.trim($("#"+num.toString()+"-queryID1").val());
      var noquery2 = !$.trim($("#"+num.toString()+"-queryID2").val());

      var queryvalue1 = $.trim($("#"+num.toString()+"-queryID1").val());
      var queryvalue2 = $.trim($("#"+num.toString()+"-queryID2").val());

      var reformulationvalue1 = $.trim($("#"+num.toString()+"-reformulationType1").val());
      var reformulationvalue2 = $.trim($("#"+num.toString()+"-reformulationType2").val());

      valid = (parseInt(queryvalue1)<parseInt(queryvalue2)) && (queryvalue1!=queryvalue2) && !noquery1 && !noquery2 && (item_ids.indexOf(queryvalue1)!=-1) && (item_ids.indexOf(queryvalue2)!=-1);
      if(!valid){
        validstr = validstr + "Bad queryID value. ";
      }
      valid = valid && ((valid_reformulations.indexOf(reformulationvalue1)!= -1) );
      valid = valid && ((valid_reformulations.indexOf(reformulationvalue2)!= -1) );

      if(!valid){
        validstr = validstr + "Bad reformulation type.";
      }
    }else if(datatype=='save'){
      var nobookmark = !$.trim($("#"+num.toString()+"-bookmarkID").val());

      var bookmarkvalue = $.trim($("#"+num.toString()+"-bookmarkID").val());

      valid = !nobookmark && (item_ids.indexOf(bookmarkvalue)!=-1);
      if(!valid){
        validstr = validstr + "Bad bookmarkID value.";
      }

    }else if(datatype=='unsave'){
      var nobookmark = !$.trim($("#"+num.toString()+"-bookmarkID").val());

      var bookmarkvalue = $.trim($("#"+num.toString()+"-bookmarkID").val());

      valid = !nobookmark && (item_ids.indexOf(bookmarkvalue)!=-1);
      if(!valid){
        validstr = validstr + "Bad bookmarkID value.";
      }

    }else{
      alert("ERROR: Bad data type.");
      return false;
    }


    if(!valid){
      alert("ERROR: "+validstr);
    }
    return valid;
  }


</script>

<body class="body">
<div class="panel panel-default" style="width:95%;  margin:auto">
  <div class="panel-body">


    <div id="login_div" style="display:block;">


<?php

    echo "<center><h2>Edit Annotations: $participantID</h2><br/><br/><button class='btn' onclick=\"location.href='selectUserForAnnotation.php'; return false;\">Return To Users Page</button></center><hr/><br/>\n";

    if($submitted != 0){
        echo "<div class=\"alert alert-success\">\n";
        echo "<strong>Success!</strong> $message\n";
        echo "</div>\n";

    }

    $query = "";
    if($anoType=='save'){
      $query = "SELECT bookmarkID as itemID,title as itemValue,'bm_save' as itemType,userID,projectID,stageID,questionID,`localTimestamp`,localDate,localTime FROM  (SELECT * FROM (SELECT `action`,`value` FROM actions WHERE userID=$userID AND questionID=$questionID AND action='Save Bookmark') actions INNER JOIN bookmarks on actions.value=bookmarks.bookmarkID) derived;";
    }else if($anoType=='unsave'){
      $query = "SELECT bookmarkID as itemID,title as itemValue,'bm_delete' as itemType,userID,projectID,stageID,questionID,`localTimestamp`,localDate,localTime FROM (SELECT * FROM (SELECT `action`,`value` FROM actions WHERE userID=$userID AND questionID=$questionID AND action='delete_bookmarks') actions INNER JOIN bookmarks on actions.value=bookmarks.bookmarkID) derived;";
    }else if($anoType=='intention' or $anoType=='reformulation'){
      $query = "SELECT * FROM (SELECT * FROM (SELECT 'page' as itemType,pageID as itemID,title as itemValue,userID,stageID,questionID,`localTimestamp`,`localDate`,`localTime` FROM pages) pages UNION SELECT 'query' as itemType,queryID as itemID,`query` as itemValue,userID,stageID,questionID,`localTimestamp`,`localDate`,`localTime` FROM queries) a WHERE userID=$userID AND questionID=$questionID ORDER BY `localTimestamp`;";
    }


    $results = $cxn->commit($query);







//    echo "<div id='video-overlay'>";
//    echo "<img width='100%' src='../tutorial/save.png' />";
//    echo "</div>";

    echo "<div class='panel panel-default'>\n";
    echo "<div class='panel-body'>\n";
    echo "<div class='row'>\n";
    echo "<div class='col-md-8'>\n";
    echo "<video controls id='session_video' width='100%' poster='tutorial/query_segment.png' style='background:transparent url(\"tutorial/query_segment.png\") no-repeat 0 0;background-size:contain;-webkit-background-size:contain;-moz-background-size:contain;-o-background-size:contain;'>\n";
    echo "<source id='mp4source' type='video/mp4' src='data/videos/mp4/$filename' >\n";
    echo "</video><br/>\n";
    echo "</div>\n";

    echo "<div class='col-md-4'>\n";
    echo "<div style='width:375px;height:400px;overflow:auto;'>\n";
    echo "<table class='table table-striped'>\n";
    echo "<thead><tr><th>Time</th><th>Type</th><th>ID</th><th>Value</th></tr></thead>\n";
    echo "<tbody>\n";

    while($line=mysql_fetch_array($results,MYSQL_ASSOC)){
      echo "<tr>\n";
      $itemType = $line['itemType'];
      $itemValue = htmlspecialchars($line['itemValue']);
      $itemID = $line['itemID'];
      $itemTime = $line['localTime'];
      echo "<td>$itemTime</td><td>$itemType</td><td>$itemID</td><td>$itemValue</td>\n";
      echo "</tr>\n";
    }
    echo "</tbody>\n";
    echo "</table>\n";
    echo "</div>\n";
    echo "</div>\n";

    echo "</div>\n";
    echo "</div>\n";
    echo "</div>\n\n";



    $anoText = '';
    $allQuery = '';
    $nullQuery = '';
    if($anoType=='intention'){
      $anoText = 'Intention';
      $allQuery = "SELECT * FROM video_intent_assignments WHERE userID=$userID AND questionID=$questionID AND queryID IS NOT NULL AND reformulationType IS NOT NULL ORDER BY assignmentID ASC";
      $nullQuery = "SELECT * FROM video_intent_assignments WHERE userID=$userID AND questionID=$questionID AND (queryID IS NULL OR reformulationType IS NULL) ORDER BY assignmentID ASC";
    }else if($anoType=='reformulation'){
      $anoText = 'Reformulation';
      $allQuery = "SELECT * FROM video_reformulation_history WHERE userID=$userID AND questionID=$questionID AND queryID1 IS NOT NULL AND queryID2 IS NOT NULL AND reformulationType1 IS NOT NULL AND reformulationType2 IS NOT NULL ORDER BY assignmentID ASC";
      $nullQuery = "SELECT * FROM video_reformulation_history WHERE userID=$userID AND questionID=$questionID AND (queryID1 IS NULL OR queryID2 IS NULL OR reformulationType1 IS NULL OR reformulationType2 IS NULL) ORDER BY assignmentID ASC";
    }else if($anoType=='save'){
      $anoText = 'Save';
      $allQuery = "SELECT * FROM video_save_history WHERE userID=$userID AND questionID=$questionID AND bookmarkID IS NOT NULL ORDER BY assignmentID ASC";
      $nullQuery = "SELECT * FROM video_save_history WHERE userID=$userID AND questionID=$questionID AND bookmarkID IS NULL ORDER BY assignmentID ASC";
    }else if($anoType=='unsave'){
      $anoText = 'Unsave';
      $allQuery = "SELECT * FROM video_unsave_history WHERE userID=$userID AND questionID=$questionID AND bookmarkID IS NOT NULL ORDER BY assignmentID ASC";
      $nullQuery = "SELECT * FROM video_unsave_history WHERE userID=$userID AND questionID=$questionID AND bookmarkID IS NULL ORDER BY assignmentID ASC";
    }



    $ct = 0;
    foreach(array('Unassigned'=>$nullQuery,'Assigned'=>$allQuery) as $assignType=>$queryType){
      echo "<center><h2>$assignType $anoText</h2></center><hr/><br/>\n";
      echo "<table class='table table-striped'>\n";
      $results = $cxn->commit($queryType);


      if($anoType=='intention'){
        echo "<thead><tr><th>Start Time</th><th>Play</th><th>Query ID</th><th>Reformulation Type</th><th>Submit</th></tr></thead>\n";
      }else if($anoType=='reformulation'){
        echo "<thead><tr><th>Start Time 1</th><th>Start Time 2</th><th>Play 1</th><th>Play 2</th><th>Query ID 1</th><th>Query ID 2</th><th>Reformulation Type 1</th><th>Reformulation Type 2</th><th>Submit</th></tr></thead>\n";
      }else if($anoType=='save'){
        echo "<thead><tr><th>Start Time</th><th>Play</th><th>Bookmark ID</th><th>Submit</th></tr></thead>\n";
      }else if($anoType=='unsave'){
        echo "<thead><tr><th>Start Time</th><th>Play</th><th>Bookmark ID</th><th>Submit</th></tr></thead>\n";
      }
      echo "<tbody>";


      while($line = mysql_fetch_array($results,MYSQL_ASSOC)){
        $ct += 1;
        $assignmentID=$line['assignmentID'];
        $hiddeninputassignmentID="<input type=\"hidden\" name=\"assignmentID\" value=$assignmentID>";

        $formprefix="<form action='labelAnnotationData.php?participantID=$participantID&questionID=$questionID&taskNum=$taskNum&anoType=$anoType&userID=$userID' method='post' enctype='multipart/form-data'>";
        $formsuffix="</form>";
        $buttonscript = "<button id='playpausebutton' class='btn btn-success' onclick=\"playvideo('".$line['time_start']."');return false;\"><i id='playpauseicon' class='fa fa-repeat'></i> Play</button>";


        if($anoType=='intention'){
          $idValue = '';
          $submitscript = "<button class='btn btn-primary' onclick=\"return valid_input('intention',$ct);\">Submit</button>";
          if($line['queryID']!=NULL){
            $idValue = $line['queryID'];
          }
          $reformulationValue = '';
          if($line['reformulationType']!=NULL){
            $reformulationValue = $line['reformulationType'];
          }


          $hiddeninputanoType = "<input type=\"hidden\" id=\"intention\" name=\"intention\" value=\"1\">";
          $input = "<input type=\"text\" id=\"$ct-queryID\" name=\"queryID\" value=\"$idValue\">";
          $input2 = "<input type=\"text\" id=\"$ct-reformulationType\" name=\"reformulationType\" value=\"$reformulationValue\">";


          $startTime = $line['time_start'];
          $timeinput = "<input type=\"hidden\" name=\"time_start\" value=\"$startTime\">";

          echo "$formprefix<tr><td>$startTime</td><td>$buttonscript</td><td>$hiddeninputanoType $hiddeninputassignmentID $input</td><td>$input2</td><td>$submitscript</td></tr>$formsuffix\n";
        }else if($anoType=='reformulation'){
          $submitscript = "<button class='btn btn-primary' onclick=\"return valid_input('reformulation',$ct);\">Submit</button>";
          $idValue1 = '';
          $idValue2 = '';
          if($line['queryID1']!=NULL and $line['queryID2']!=NULL){
            $idValue1 = $line['queryID1'];
            $idValue2 = $line['queryID2'];
          }

          $reformulationValue1 = '';
          $reformulationValue2 = '';
          if($line['reformulationType1']!=NULL){
            $reformulationValue1 = $line['reformulationType1'];
          }
          if($line['reformulationType2']!=NULL){
            $reformulationValue2 = $line['reformulationType2'];
          }

          $hiddeninputanoType = "<input type=\"hidden\" id=\"reformulation\" name=\"reformulation\" value=\"1\">";
          $input1 = "<input type=\"text\" id=\"$ct-queryID1\" name=\"queryID1\" value=\"$idValue1\">";
          $input2 = "<input type=\"text\" id=\"$ct-queryID2\" name=\"queryID2\" value=\"$idValue2\">";
          $input3 = "<input type=\"text\" id=\"$ct-reformulationType1\" name=\"reformulationType1\" value=\"$reformulationValue1\">";
          $input4 = "<input type=\"text\" id=\"$ct-reformulationType2\" name=\"reformulationType2\" value=\"$reformulationValue2\">";
          $startTime1 = $line['time_start_1'];
          $startTime2 = $line['time_start_2'];
          $buttonscript1 = "<button id='playpausebutton' class='btn btn-success' onclick=\"playvideo('".$line['time_start_1']."');return false;\"><i id='playpauseicon' class='fa fa-repeat'></i> Play</button>";
          $buttonscript2 = "<button id='playpausebutton' class='btn btn-success' onclick=\"playvideo('".$line['time_start_2']."');return false;\"><i id='playpauseicon' class='fa fa-repeat'></i> Play</button>";


          echo "$formprefix<tr><td>$startTime1</td><td>$startTime2</td><td>$buttonscript1</td><td>$buttonscript2</td><td>$hiddeninputanoType $hiddeninputassignmentID $input1</td><td>$input2</td><td>$input3</td><td>$input4</td><td>$submitscript</td></tr>$formsuffix\n";
        }else if($anoType=='save'){
          $submitscript = "<button class='btn btn-primary' onclick=\"return valid_input('save',$ct);\">Submit</button>";
          $idValue = '';
          if($line['bookmarkID']!=NULL){
            $idValue = $line['bookmarkID'];
          }
          $hiddeninputanoType = "<input type=\"hidden\" id=\"save\" name=\"save\" value=\"1\">";
          $input = "<input type=\"text\" id=\"$ct-bookmarkID\" name=\"bookmarkID\" value=\"$idValue\">";
          $startTime = $line['time_start'];

          echo "$formprefix<tr><td>$startTime</td><td>$buttonscript</td><td>$hiddeninputanoType $hiddeninputassignmentID $input</td><td>$submitscript</td></tr>$formsuffix\n";
        }else if($anoType=='unsave'){
          $submitscript = "<button class='btn btn-primary' onclick=\"return valid_input('unsave',$ct);\">Submit</button>";
          $idValue = '';
          if($line['bookmarkID']!=NULL){
            $idValue = $line['bookmarkID'];
          }
          $hiddeninputanoType = "<input type=\"hidden\" id=\"unsave\" name=\"unsave\" value=\"1\">";
          $input = "<input type=\"text\" id=\"$ct-bookmarkID\" name=\"bookmarkID\" value=\"$idValue\">";
          $startTime = $line['time_start'];

          echo "$formprefix<tr><td>$startTime</td><td>$buttonscript</td><td>$hiddeninputanoType $hiddeninputassignmentID $input</td><td>$submitscript</td></tr>$formsuffix\n";
        }
      }

      echo "</tbody>";
      echo "</table>";

    }


  }
  ?>

</div>
</div>
</body></html>
