
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



    $click_buffer = $_POST['clicks'];
    $values_array = array();
    foreach($click_buffer as $lTs=>$click_data){
        $localTimestamp	= $lTs;
        $lts_seconds = $lTs/1000.0;
        $localDate = date("Y-m-d", $lts_seconds);
        $localTime = date("h:i:s",$lts_seconds);
        $clientX = $click_data['clientX'];
        $clientY = $click_data['clientY'];
        $pageX = $click_data['pageX'];
        $pageY = $click_data['pageY'];
        $scrollX = $click_data['scrollX'];
        $scrollY = $click_data['scrollY'];
        $screenX = $click_data['screenX'];
        $screenY = $click_data['screenY'];
        $type = $click_data['type'];

        array_push($values_array,"($userID,$projectID,'$type','$clientX','$clientY','$pageX','$pageY','$screenX','$screenY','$scrollX','$scrollY',$timestamp,'$date','$time',$localTimestamp,'$localDate','$localTime')");
    }

    $values_str = implode(',',$values_array);

    $cxn = Connection::getInstance();
    $query = "INSERT INTO click_data (userID, projectID, `type`,clientX,clientY,pageX,pageY,screenX,screenY,scrollX,scrollY, `timestamp`, `date`, `time`, `localTimestamp`, `localDate`, `localTime`) VALUES $values_str";
    $cxn->commit($query);


    $action = new Action('clicksave',$cxn->getLastID());
    $action->setBase($base);
    $action->setLocalTimestamp($localTimestamp);
    $action->save();


}
?>
