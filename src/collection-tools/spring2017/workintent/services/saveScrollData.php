
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
    $scroll_buffer = $_POST['scrolls'];
    $values_array = array();
    foreach($scroll_buffer as $lTs=>$scroll_data){
        $localTimestamp	= $lTs;
        $lts_seconds = $lTs/1000.0;
        $localDate = date("Y-m-d", $lts_seconds);
        $localTime = date("h:i:s",$lts_seconds);

        foreach($scroll_data as $index=>$datum){
            $scrollX = $datum['scrollX'];
            $scrollY = $datum['scrollY'];
            $screenX = $datum['screenX'];
            $screenY = $datum['screenY'];
            array_push($values_array,"($userID,$projectID,'$screenX','$screenY','$scrollX','$scrollY',$timestamp,'$date','$time',$localTimestamp,'$localDate','$localTime')");
        }



    }

    $values_str = implode(',',$values_array);

    $cxn = Connection::getInstance();
    $query = "INSERT INTO scroll_data (userID, projectID,screenX,screenY,scrollX,scrollY, `timestamp`, `date`, `time`, `localTimestamp`, `localDate`, `localTime`) VALUES $values_str";
    $cxn->commit($query);


    $action = new Action('clicksave',$cxn->getLastID());
    $action->setBase($base);
    $action->setLocalTimestamp($localTimestamp);
    $action->save();


}
?>
