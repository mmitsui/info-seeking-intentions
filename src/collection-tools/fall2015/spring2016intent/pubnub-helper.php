<?php
require_once('sidebar/pubnub-lib/autoloader.php');
use Pubnub\Pubnub;

function pubnubPublishToUser($msg){
    $pubnub = new Pubnub(array('publish_key'=>'pub-c-0ee3d3d2-e144-4fab-bb9c-82d9be5c13f1','subscribe_key'=>'sub-c-ac9b4e84-b567-11e4-bdc7-02ee2ddab7fe'));
    $base = Base::getInstance();
    $userID = $base->getUserID();
    $message = array('message'=> $msg);
    $res=$pubnub->publish("spr15-".$base->getStageID()."-".$base->getProjectID()."-checkStage".$userID,$message);
}
?>
