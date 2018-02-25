
<?php
	session_start();
	require_once('../core/Connection.class.php');
	require_once('../core/Base.class.php');
	require_once('../core/Action.class.php');
	require_once('../core/Util.class.php');
	require_once("utilityFunctions.php");

if (Base::getInstance()->isSessionActive())
{

    $base = new Base();
    $userID = $base->getUserID();
    $projectID = $userID;
    $time = $base->getTime();
    $date = $base->getDate();
    $timestamp = $base->getTimestamp();
    $copy_buffer = $_POST['copies'];
    $values_array = array();
    foreach($copy_buffer as $lTs=>$copy_data){
        $localTimestamp	= $lTs;
        $lts_seconds = $lTs/1000.0;
        $localDate = date("Y-m-d", $lts_seconds);
        $localTime = date("h:i:s",$lts_seconds);
        $url = mysql_escape_string($copy_data['url']);
        $title = mysql_escape_string($copy_data['title']);
        $snippet = mysql_escape_string($copy_data['snippet']);
        array_push($values_array,"($userID,$projectID,'$url','$title','$snippet',$timestamp,'$date','$time',$localTimestamp,'$localDate','$localTime')");
    }

    $values_str = implode(',',$values_array);

    $cxn = Connection::getInstance();
    $query = "INSERT INTO copy_data (userID, projectID,`url`,`title`,`snippet`, `timestamp`, `date`, `time`, `localTimestamp`, `localDate`, `localTime`) VALUES $values_str";
    $cxn->commit($query);


    $action = new Action('clicksave',$cxn->getLastID());
    $action->setBase($base);
    $action->setLocalTimestamp($localTimestamp);
    $action->save();


}			
?>