
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
    $mouse_buffer = $_POST['mouse_actions'];
    $values_array = array();
    foreach($mouse_buffer as $lTs=>$mouse_data){
        $localTimestamp	= $lTs;
        $lts_seconds = $lTs/1000.0;
        $localDate = date("Y-m-d", $lts_seconds);
        $localTime = date("h:i:s",$lts_seconds);

        foreach($mouse_data as $index=>$datum){
            $scrollX = $datum['scrollX'];
            $scrollY = $datum['scrollY'];
            $screenX = $datum['screenX'];
            $screenY = $datum['screenY'];

            $type = $datum['type'];
            $clientX = $datum['clientX'];
            $clientY = $datum['clientY'];
            $pageX = $datum['pageX'];
            $pageY = $datum['pageY'];


            array_push($values_array,"($userID,$projectID,'$type','$clientX','$clientY','$pageX','$pageY','$screenX','$screenY','$scrollX','$scrollY',$timestamp,'$date','$time',$localTimestamp,'$localDate','$localTime')");
        }


    }

    $values_str = implode(',',$values_array);

    $cxn = Connection::getInstance();
    $query = "INSERT INTO mouse_data (userID, projectID,`type`,clientX,clientY,pageX,pageY,screenX,screenY,scrollX,scrollY, `timestamp`, `date`, `time`, `localTimestamp`, `localDate`, `localTime`) VALUES $values_str";
    $cxn->commit($query);


    $action = new Action('clicksave',$cxn->getLastID());
    $action->setBase($base);
    $action->setLocalTimestamp($localTimestamp);
    $action->save();


}
?>
