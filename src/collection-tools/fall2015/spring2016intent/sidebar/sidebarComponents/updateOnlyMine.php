<?php
  require_once('../../core/Connection.class.php');
  require_once('../../core/Base.class.php');
  require_once('../../core/Util.class.php');
  session_start();
  $base = Base::getInstance();

  if ((isset($_SESSION['CSpace_userID']))) {
    $only_mine = $_GET['only_mine'] == 'true' ? true : false;
    $webPage = isset($_GET['webPage']) ? $_GET['webPage'] : false;
    $_SESSION['only_mine'] = $only_mine;
    echo $_GET['only_mine'];
    $table  = $_GET['table'];
    $userID = $base->getUserID();
    $projectID = $_SESSION['CSpace_projectID'];
    $timestamp = $base->getTimestamp();
    $date = $base->getDate();
    $time = $base->getTime();
    $ip=$_SERVER['REMOTE_ADDR'];

    Util::getInstance()->saveAction("updateOnlyMine_$table","$only_mine",$base);

    // $aquery = "INSERT INTO actions (userID, projectID, timestamp, date, time, action, value, ip) VALUES ('$userID', '$projectID', '$timestamp', '$date', '$time', 'updateOnlyMine_$table', '$only_mine','$ip')";

    // $connection = Connection::getInstance();
    // $result = $connection->commit($aquery);
    if ($webPage){
      require_once($webPage);
    }
  }
?>
