<?php
  require_once('../../core/Connection.class.php');
  require_once('../../core/Base.class.php');
  require_once('../../core/Util.class.php');
  session_start();
  $base = Base::getInstance();

  if ((isset($_SESSION['CSpace_userID']))) {
    $tag = isset($_GET['val'])? $_GET['val'] : '';
    $userID = $base->getUserID();
    $projectID = $_SESSION['CSpace_projectID'];
    $timestamp = $base->getTimestamp();
    $date = $base->getDate();
    $time = $base->getTime();
    $ip=$_SERVER['REMOTE_ADDR'];
    Util::getInstance()->saveAction("filterBy_bookmark", "$tag",$base);
    // $aquery = "INSERT INTO actions (userID, projectID, timestamp, date, time, action, value, ip) VALUES ('$userID', '$projectID', '$timestamp', '$date', '$time', 'filterBy_bookmark', '$tag','$ip')";

    // $connection = Connection::getInstance();
    // $result = $connection->commit($aquery);
  }
?>
