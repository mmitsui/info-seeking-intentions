
<html>
<head>
<title>Coagmento Study Group Activity</title>
<link rel="stylesheet" href="../study_styles/pure-release-0.5.0/buttons.css">
<link rel="stylesheet" href="../study_styles/pure-release-0.5.0/forms.css">
<link rel="stylesheet" href="../study_styles/pure-release-0.5.0/tables.css">
<link rel="stylesheet" href="../study_styles/custom/text.css">

</head>
<script type="text/javascript">

var is_ff;
var alertColor = "Red";
var okColor = "White";


function validate(form)
{
    return true;
}

function changeColor(field,color)
{
    field.style.background = color;
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
<div id="login_div" style="display:block;">
<div class="pagecontainer">
	<center>
<table class="body" width="90%">
<tr><td><center><h2>Coagmento Study Group Activity</h2></center></td></tr>
</table>
<form class="pure-form" id="login_form" action="#" method="post">
<fieldset>

<div class="row">
<label for="username">Username</label></td><td>&nbsp;&nbsp; <input type="text" id="username" name="username" size=20 required />
</div>

<div class="row">
<labe for="password"l>Password</label></td><td>&nbsp;&nbsp; <input type="password" id="password" name="password" size=20 required />
</div>

<div class="row">
  <button type="submit" class="pure-button pure-button-primary" >Submit</button>
</div>
</fieldset>
</center>
</form>
</div>





<?php

	session_start();
	require_once('../core/Connection.class.php');
	require_once('../core/Base.class.php');
	require_once('../core/Util.class.php');
    require_once('../core/Stage.class.php');

    echo "<hr><br><br>";

    function getColor($value)
	{
		if (($value % 2) == 0)
			$color="\"#F2F2F2\"";
		else
			$color="\"White\"";

		return $color;
	}




	if (isset($_POST['username']))
	{
		$username = $_POST['username'];
		$instructorName = '';
		$instructorID = 0;
		$port = 0;
		$apikey="87b40a9c3818d6cde3d9960db9c4d1a57199ec86fc165f082fbeac072154d559";
		$stageID=-1;
		$password=$_POST['password'];

		if($username=='belkin'){
			$instructorName = "Dr. Nicholas Belkin";
			$apikey="87b40a9c3818d6cde3d9960db9c4d1a57199ec86fc165f082fbeac072154d559";
		}else if($username=='ninwac'){
			$instructorName = "Dr. Nina Wacholder";
			$apikey="87b40a9c3818d6cde3d9960db9c4d1a57199ec86fc165f082fbeac072154d559";
		}else if($username=='mmitsui'){
			$apikey="87b40a9c3818d6cde3d9960db9c4d1a57199ec86fc165f082fbeac072154d559";
		}else if($username=='eun.baik'){
      $instructorName = "Dr. Nicholas Belkin";
      $apikey="87b40a9c3818d6cde3d9960db9c4d1a57199ec86fc165f082fbeac072154d559";
    }else if($username=='s.bar'){
      $instructorName = "Dr. Nina Wacholder";
      $apikey="87b40a9c3818d6cde3d9960db9c4d1a57199ec86fc165f082fbeac072154d559";
    }

		$query = "SELECT * FROM instructors WHERE username='$username' AND password='$password'";

		$connection = Connection::getInstance();
		$results = $connection->commit($query);
    $base = Base::getInstance();







		if (mysql_num_rows($results) > 0 || ($_POST['username'] == 'mmitsui' && $_POST['password']=='BJ3&9X')
    || ($_POST['username'] == 'eun.baik' && $_POST['password']=='?UvL6#')
    || ($_POST['username'] == 's.bar' && $_POST['password']=='S&xmb!')) //insert session one end stage if necessary
		{

            echo "<center><h4>Study Group Activity For: $instructorName</h4><thead>";
						echo "<center><table class=\"pure-table pure-table-striped\">";
						echo "<thead><th style=\"width:700px;\">Group</th><th style=\"width:190px;\">Link</th></thead>";
            $instructorID=0;$port=0;
            if(mysql_num_rows($results) > 0){
              $line = mysql_fetch_array($results,MYSQL_ASSOC);
              $instructorID = $line['instructorID'];
              $port = $line['etherpadPort'];
            }
            $query = '';
            if($_POST['username'] == 'eun.baik'){
              $instructorID=1;
            }else if($_POST['username'] == 's.bar'){
              $instructorID=2;
            }

            if($_POST['username'] != 'mmitsui'){
              $query = "SELECT instructorID,projectID FROM recruits R WHERE R.instructorID='$instructorID' AND R.userID < 1000 GROUP BY R.projectID";
            }else{
              $query = "SELECT instructorID,projectID FROM recruits R WHERE R.instructorID IN (1,2) AND R.userID < 1000 GROUP BY R.projectID";
            }
            $results = $connection->commit($query);

						echo "<tbody>";
            while($line = mysql_fetch_array($results,MYSQL_ASSOC)){

                if($_POST['username'] == 'mmitsui' && $line['instructorID']==1){
                  $port = 9000;
                }else if($_POST['username'] == 'mmitsui' && $line['instructorID']==2){
                  $port = 9005;
                }else if($_POST['username'] == 'eun.baik'){
                  $port = 9000;
                }else if($_POST['username'] == 's.bar'){
                  $port = 9005;
                }


                $projectID=$line['projectID'];
                $namequery = "SELECT firstName, lastName FROM recruits WHERE projectID='$projectID'";


                $cxn = Connection::getInstance();
                $group = array();

                $q = "SELECT * FROM (select username,userID FROM users WHERE users.projectID=$projectID) b INNER JOIN (SELECT userID, firstName, lastName FROM recruits) a ON a.userID=b.userID ORDER BY lastName";
                if(mysql_num_rows($cxn->commit($q))==0){
                  continue;
                }

								echo "<tr>";
								echo "<td>";
								// $nameresults = $connection->commit($namequery);
                // while($nameline = mysql_fetch_array($nameresults,MYSQL_ASSOC)){
								// 		$firstName = $nameline['firstName'];
								// 		$lastName = $nameline['lastName'];
								// 		echo "<li>$firstName $lastName</li>";
                // }
                $cxn = Connection::getInstance();
                $group = array();

                $q = "SELECT * FROM (select username,userID FROM users WHERE users.projectID=$projectID) b INNER JOIN (SELECT userID, firstName, lastName FROM recruits) a ON a.userID=b.userID ORDER BY lastName";
                $inner_results = $cxn->commit($q);
                while($row = mysql_fetch_assoc($inner_results)){
                  $group[$row['firstName']." ".$row['lastName']] = array(
                    "bookmarks" => 0,
                    "snippets" => 0,
                    "searches" => 0,
                    "lastlogin" => "Never logged in."
                  );
                }

                foreach(array("bookmarks","snippets","searches") as $name){
                  if($name == "searches"){
                    $name = "queries";
                  }
                  $q =  "select u.firstName,u.lastName, b.userID, count(b.userID) as count from $name b, recruits u where b.projectID=$projectID AND b.userID = u.userID group by userID";
                  $bss_results = $cxn->commit($q);
                  if($name == "queries"){
                    $name = "searches";
                  }
                  while($row = mysql_fetch_assoc($bss_results)){
                    $group[$row['firstName']." ".$row['lastName']]["$name"] = $row["count"];
                  }

                  $q =  "select u.firstName,u.lastName, b.userID, count(b.userID) as count,max(b.`timestamp`) as lastlogin from actions b, recruits u where b.projectID=$projectID AND b.userID = u.userID AND b.action='login' group by userID";
                  $bss_results = $cxn->commit($q);
                  while($row = mysql_fetch_assoc($bss_results)){
                    $d = date('m/d/Y h:i:s', $row['lastlogin']);
                    $group[$row['firstName']." ".$row['lastName']]["lastlogin"] = $d;
                  }
                }

                echo "<table style=\"width:100%\">";
                echo "<tr><th>Name</th><th>Bookmarks</th><th>Snippets</th><th>Searches</th><th>Last Login</th></tr>";
                foreach($group as $name=>$data){
                  $nbookmarks = $data["bookmarks"];
                  $nsnippets = $data["snippets"];
                  $nsearches = $data["searches"];
                  echo "<tr>";
                  echo "<td>$name</td>";
                  echo "<td>$nbookmarks</td>";
                  echo "<td>$nsnippets</td>";
                  echo "<td>$nsearches</td>";
                  echo "<td>".$data['lastlogin']."</td>";
                  echo "</tr>";
                }
                echo "</table>";

								// echo "</ul>";
                echo "</td>";

								$padID = "spring2015_report-$projectID--1-";
								$url = "http://coagmentopad.rutgers.edu:".$port."/api/1/getReadOnlyID?apikey=".$apikey."&padID=".$padID;

								$padquery = "SELECT readOnlyID,available FROM etherpad_submissions WHERE projectID='$projectID'";
								$padresults = $connection->commit($padquery);
								$exists = mysql_num_rows($padresults)>0;
								$readOnlyID = '';
								$available = 0;

								if($exists){
									$padline = mysql_fetch_array($padresults,MYSQL_ASSOC);
									$available = $padline['available'];
									$readOnlyID = $padline['readOnlyID'];
								}

								echo "<td>";
								if(!$exists || !$available){
									$data=file_get_contents($url);
									$data_str = $data;
									$data=json_decode($data);
                  // print_r($data);
									$valid = ($data->{'code'} == 0);


									if($valid){
											$readOnlyID = addslashes($data->{'data'}->{'readOnlyID'});
											$url = "http://coagmentopad.rutgers.edu:".$port."/p/".$readOnlyID;
											echo "<center><button class=\"pure-button pure-button-primary\" onclick=\"javascript:window.open('$url','_blank')\">Get Pad</button></center>";
									}else{
											echo "(No Etherpad available.)";
									}


									$q='';
									if(!$exists){
										$q="INSERT INTO etherpad_submissions (projectID,readOnlyID,available) VALUES ('$projectID','$readOnlyID','$valid')";
									}else{
										$q="UPDATE etherpad_submissions SET available='$valid' WHERE projectID='$projectID'";
									}
									$r = $connection->commit($q);

								}else{
									$url = "http://coagmentopad.rutgers.edu:".$port."/p/".$readOnlyID;
									echo "<center><button class=\"pure-button pure-button-primary\" onclick=\"javascript:window.open('$url','_blank')\">Get Pad</button></center>";
								}
								echo "</td>";
								echo "</tr>";







            }

						echo "</tbody></table></center>";

        }else{
                echo "<div style=\"background-color:red;\">The credentials you have entered are incorrect.  Please check your input and try again.</div>";
        }
    }


    ?>









</body></html>
