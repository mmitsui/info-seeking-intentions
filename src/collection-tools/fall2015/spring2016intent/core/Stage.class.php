<?php
require_once('Connection.class.php');
require_once('Base.class.php');
require_once('Action.class.php');

class Stage 
{
	protected $stageID;
	protected $currentPage;
	protected $maxTime;
	protected $maxQuestion;
	protected $maxLoops;
	protected $currentLoops;
	protected $allowBrowsing;
	protected $previousStartTimestamp;
	protected $previousMaxTime;
    
    //STAGE ID CONSTANTS
    const TASK_END = 110;
//    const SESSION_ONE_WARMUP = 15;
//    const SESSION_ONE_MAIN_TASK = 25;
//    const SESSION_TWO_START = 45;
//    const SESSION_TWO_END = 75;
				
	public function __construct() 
	{
		$connection = Connection::getInstance();
		$base = Base::getInstance();
		
					

		//THIS ONE WORKS IN TERMS OF PROGRESS ID; THIS TO SOLVE PROBLEM WITH LOOP TASK -> SAM
		$query = "SELECT stageID, page, maxTime, maxTimeQuestion, maxLoops, allowBrowsing, allowCommunication, (SELECT count(*) FROM session_progress a WHERE a.stageID = b.stageID and projectID = '".$base->getProjectID()."' and userID = '".$base->getUserID()."' ) currentLoops
				  FROM session_stages b
				  WHERE status = '1'
				    AND stageID = (SELECT stageID
				  				   FROM session_progress a 
				  				   WHERE projectID = '".$base->getProjectID()."' 
				  				   	 AND userID = '".$base->getUserID()."'
				  				   	 AND progressID = (SELECT max(progressID)
                                                         FROM session_progress a 
                                                        WHERE projectID = '".$base->getProjectID()."' 
                                                          AND userID = '".$base->getUserID()."'
                                                       )   			  				   
				  				   )";

		$results = $connection->commit($query);
		$line = mysql_fetch_array($results, MYSQL_ASSOC);
					

		
		if ($line['stageID']<>'')
		{
					

			$this->stageID = $line['stageID'];
			$this->currentPage = $line['page'];
			$this->maxTime = $line['maxTime'];
			$this->maxTimeQuestion = $line['maxTimeQuestion'];
			$this->maxLoops = $line['maxLoops'];
			$this->currentLoops = $line['currentLoops'];
			$this->allowBrowsing = $line['allowBrowsing'];
			$this->allowCommunication = $line['allowCommunication'];
			$this->previousStartTimestamp = $this->getPreviousStartTimestamp();
			$this->previousMaxTime = $this->getPreviousMaxTime();
		}
		else 
		{
					

			$this->stageID = -1;
			$this->currentPage = 'index.php';	
			$this->maxTime = 0;						
			$this->maxTimeQuestion = 0;
			$this->maxLoops = 0;	
			$this->currentLoops = 0;
			$this->allowBrowsing = 0;
			$this->allowCommunication = 0;
			$this->previousStartTimestamp = 0;
			$this->previousMaxTime = 0;		
		}
		
					

		$base->setStageID($this->stageID);	
		$base->setPage($this->currentPage);
							

		$base->setMaxTime($this->maxTime);
		$base->setMaxTimeQuestion($this->maxTimeQuestion);
		$base->setMaxLoops($this->maxLoops);

		$base->setCurrentLoops($this->currentLoops);
							
		$base->setAllowBrowsing($this->allowBrowsing);
		$base->setAllowCommunication($this->allowCommunication);	
									
		
		$base->setPreviousStartTimestamp($this->previousStartTimestamp);
		$base->setPreviousMaxTime($this->previousMaxTime);
				

	}
  	
	public function moveToNextStage()
	{
		$data = $this->getNextStageData();
		$this->currentPage = $data['page'];	//First get Page, then stageID
		$this->stageID = $data['stageID'];	  //Then get ID next page
		$this->maxTime = $data['maxTime']; //Get Max Time
		$this->maxTimeQuestion = $data['maxTimeQuestion']; //Get Max Time Question
		$this->maxLoops = $data['maxLoops']; //Get Max Loops
		$this->currentLoops = $data['currentLoops']; //Get Current Loops
		$this->allowBrowsing = $data['allowBrowsing'];		
		$this->allowCommunication = $data['allowCommunication'];
		$this->previousStartTimestamp = $this->getPreviousStartTimestamp();
		$this->previousMaxTime = $this->getPreviousMaxTime();
											
		if ($this->currentPage<>'')
		{
			//Create action before setting the session variable to preserve the previous stage in the log
			$action = new Action('Next Stage: '.$this->currentPage,$this->stageID);
			
			//SAVING THE NEW STAGE IN SESSION VARIABLE
			$base = Base::getInstance();
			$base->setStageID($this->stageID);	
			$base->setPage($this->currentPage);
			$base->setMaxTime($this->maxTime);	
			$base->setMaxTimeQuestion($this->maxTimeQuestion);
			$base->setMaxLoops($this->maxLoops);
			$base->setCurrentLoops($this->currentLoops);
			$base->setAllowBrowsing($this->allowBrowsing);
			$base->setAllowCommunication($this->allowCommunication);
			$base->setPreviousStartTimestamp($this->previousStartTimestamp);
			$base->setPreviousMaxTime($this->previousMaxTime);
			
			$projectID = $action->getProjectID();
			$userID = $action->getUserID();
			$time = $action->getTime();
			$date = $action->getDate();
			$timestamp = $action->getTimestamp();
			$stageID = $action->getStageID();	//It keeps the previous stage ID		
			$connection = Connection::getInstance();
			$query = "INSERT INTO session_progress(projectID, userID, stageID, date, time, timestamp) 
			                               VALUES ('$projectID','$userID','$this->stageID','$date','$time','$timestamp')";	
		
			$results = $connection->commit($query);	
			$action->save();
			
			return true;
		}
		
		return false;
	}
	
	public function moveToPreviousStage()
	{
		$data = $this->getPreviousStageData();
		$this->currentPage = $data['page'];	//First get Page, then stageID
		$this->stageID = $data['stageID'];	  //Then get ID next page
		$this->maxTime = $data['maxTime']; //Get Max Time
		$this->maxTimeQuestion = $data['maxTimeQuestion']; //Get Max Time Question
		$this->maxLoops = $data['maxLoops']; //Get Max Loops
		$this->currentLoops = $data['currentLoops']; //Get Current Loops
		$this->allowBrowsing = $data['allowBrowsing'];
		$this->allowCommunication = $data['allowCommunication'];
										
		if ($this->currentPage<>'')
		{
			//Create action before setting the session variable to preserve the previous stage in the log
			$action = new Action('Previous Stage: '.$this->currentPage,$this->stageID);
			
			//SAVING THE NEW STAGE IN SESSION VARIABLE
			$base = Base::getInstance();
			$base->setStageID($this->stageID);	
			$base->setPage($this->currentPage);
			$base->setMaxTime($this->maxTime);	
			$base->setMaxTimeQuestion($this->maxTimeQuestion);
			$base->setMaxLoops($this->maxLoops);
			$base->setAllowBrowsing($this->allowBrowsing);
			$base->setAllowCommunication($this->allowCommunication);
					
			$projectID = $action->getProjectID();
			$userID = $action->getUserID();
			$time = $action->getTime();
			$date = $action->getDate();
			$timestamp = $action->getTimestamp();
			$stageID = $action->getStageID();	//It keeps the previous stage ID		
			$connection = Connection::getInstance();
			$query = "INSERT INTO session_progress(projectID, userID, stageID, date, time, timestamp) 
			                               VALUES ('$projectID','$userID','$this->stageID','$date','$time','$timestamp')";	
		
			$results = $connection->commit($query);	
			$action->save();
			
			return true;
		}
		
		return false;
	}
	
	//GETTERS NEXT STAGE
	public function getNextStageData()
	{
		$base = Base::getInstance();
				
		$connection = Connection::getInstance();
		$query = "SELECT stageID, page, maxTime, maxTimeQuestion, maxLoops, allowBrowsing, allowCommunication, (SELECT count(*) FROM session_progress a WHERE a.stageID = b.stageID  AND userID = '".$base->getUserID()."' AND projectID = '".$base->getProjectID()."') currentLoops 
				    FROM session_stages b
				    WHERE stageID>$this->stageID AND status = '1' order by stageID LIMIT 1";
		$results = $connection->commit($query);
		$line = mysql_fetch_array($results, MYSQL_ASSOC);
		return $line;
	}
	
	public function getNextStage()
	{
		$data = $this->getNextStageData();
		return $data['stageID'];
	}
	
	public function getNextPage()
	{
		$data = $this->getNextStageData();
		return $data['page'];
	}
	
	public function getNextMaxTime()
	{
		$data = $this->getNextStageData();
		return $data['maxTime'];
	}
	
	public function getNextMaxTimeQuestion()
	{
		$data = $this->getNextStageData();
		return $data['maxTimeQuestion'];
	}
	
	public function getNextMaxLoops()
	{
		$data = $this->getNextStageData();
		return $data['maxLoops'];
	}
	
	public function getNextAllowCommunication()
	{
		$data = $this->getNextStageData();
		return $data['allowCommunication'];
	}	
	
	public function getNextCurrentLoops()
	{
		$data = $this->getNextStageData();
		return $data['currentLoops'];
	}
	
	public function getNextAllowBrowsing()
	{
		$data = $this->getNextStageData();
		return $data['allowBrowsing'];
	}
	
	//GETTERS PREVIOUS STAGE
	public function getPreviousStageData()
	{
		$base = Base::getInstance();
		
		$connection = Connection::getInstance();
		$query = "SELECT stageID, page, maxTime, maxTimeQuestion, maxLoops, allowBrowsing, allowCommunication, (SELECT count(*) FROM session_progress a WHERE a.stageID = b.stageID AND userID = '".$base->getUserID()."' AND projectID = '".$base->getProjectID()."') currentLoops,
																			(SELECT min(timestamp) FROM session_progress c WHERE c.stageID = b.stageID AND userID = '".$base->getUserID()."' AND projectID = '".$base->getProjectID()."') timestamp 
					FROM (SELECT * FROM session_stages WHERE stageID<$this->stageID AND status = '1' order by stageID DESC) b 
					LIMIT 1";
		//echo $query;
		$results = $connection->commit($query);
		$line = mysql_fetch_array($results, MYSQL_ASSOC);
		return $line;
	}
		
	public function getPreviousStage()
	{
		$data = $this->getPreviousStageData();
		return $data['stageID'];
	}
	
	public function getPreviousPage()
	{
		$data = $this->getPreviousStageData();
		return $data['page'];
	}
	
	public function getPreviousMaxTime()
	{
		$data = $this->getPreviousStageData();
		return $data['maxTime'];
	}
	
	public function getPreviousMaxTimeQuestion()
	{
		$data = $this->getPreviousStageData();
		return $data['maxTimeQuestion'];
	}
	
	public function getPreviousMaxLoops()
	{
		$data = $this->getPreviousStageData();
		return $data['maxLoops'];
	}

	public function getPreviousCurrentLoops()
	{
		$data = $this->getPreviousStageData();
		return $data['currentLoops'];
	}
	
	public function getPreviousAllowBrowsing()
	{
		$data = $this->getPreviousStageData();
		return $data['allowBrowsing'];
	}	
	
	public function getPreviousAllowCommunication()
	{
		$data = $this->getPreviousStageData();
		return $data['allowCommunication'];
	}	
	
	public function getPreviousStartTimestamp()
	{
		$data = $this->getPreviousStageData();
		return $data['timestamp'];
	}
	
	//GETTERS
	
	public function getCurrentStage()
	{	
		return $this->stageID;
	}
	
	public function getCurrentPage()
	{	
		return $this->currentPage;
	}
	
	public function getMaxTime()
	{	
		return $this->maxTime;
	}
	
	public function getMaxTimeQuestion()
	{	
		return $this->maxTimeQuestion;
	}
	
	public function getAllowBrowsing()
	{	
		return $this->allowBrowsing;
	}
	
	public function getAllowCommunication()
	{	
		return $this->allowCommunication;
	}
}
?>