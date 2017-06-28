<?php

require_once('Connection.class.php');
require_once('Base.class.php');

class Queries extends Base {

  public function __construct(){
    parent::__construct();
  }


  public static function retrieveFromUser($userID, $projectID=FALSE){

  }

  public static function retrieveFromProject($projectID, $sorting="timestamp DESC"){
    $cxn=Connection::getInstance();
    $query = sprintf("SELECT queries.*, users.username FROM queries, users WHERE queries.projectID=%d AND queries.userID=users.userID ORDER BY %s", $projectID, $cxn->esc($sorting));
    echo $query;
    $queries = array();
    $results = $cxn->commit($query);
    while($record = mysql_fetch_assoc($results)){
      array_push($queries, $record);
    }
    return $queries;
  }

}
?>
