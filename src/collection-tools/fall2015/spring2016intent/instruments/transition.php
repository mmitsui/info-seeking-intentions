<?php
	session_start();
	require_once('../core/Base.class.php');
	require_once('../core/Settings.class.php');
	require_once('../core/Action.class.php');
	require_once('../core/Util.class.php');
	
	Util::getInstance()->checkSession();
	$base = Base::getInstance();
	$rightPage = true;
	$settings = Settings::getInstance();
	$homeURL = $settings->getHomeURL();
	$stageID = $base->getStageID();
	$synched = Util::getInstance()->isSynchronized();
	$rightPage = Util::getInstance()->isRightPage(basename( __FILE__ ));
	$base = new Base();
	$studyID = $base->getStudyID();
	
	//NO IN CURRENT STAGE AND WRONG PAGE
	if (($synched==-1)&&(!$rightPage))
		Util::getInstance()->checkCurrentPage(basename( __FILE__ ));

	if ($rightPage)
	{		
		if ($studyID==1)
		{
			Util::getInstance()->saveAction(basename( __FILE__ ),$stageID,$base);
			//Next stage
			Util::getInstance()->moveToNextStage();						
		}
	}
	
	$synched = Util::getInstance()->isSynchronized();
	
   	if ($synched==1)
  	{
		$base = new Base();
		$stageID = $base->getStageID();
		Util::getInstance()->saveAction(basename( __FILE__ ),$stageID,$base);
		//Next stage
		Util::getInstance()->moveToNextStage();
	}
	else 
	{
?>
<html>
	<head>
		<title>
			<?php echo "Stage: ".Base::getInstance()->getStageID();?>
		</title>
		<script type="text/javascript" src="js/util.js"></script>
		<script type="text/javascript" src="js/jquery.min.js"></script>
		<script type="text/javascript">

		var homeURL = "<?php echo $homeURL;?>"
			
		var InfiniteAjaxRequest = function (uri) {
    	    $.ajax({
    	        url: uri,
    	        success: function(data) {
    	            // do something with "data"
    	            if (data!=0)
        	        {
            	        //alert("Hi 123");
    	            	window.location = homeURL+'instruments/transition.php'; // Jump to next page!
    	            	return;
    	            }
    	            else
    	            	InfiniteAjaxRequest (uri);
    	        },
    	        error: function(xhr, ajaxOptions, thrownError) {
    	        }
    	    });
    	};

    	InfiniteAjaxRequest (homeURL+"synchronize.php");
		
		</script>
	</head>
	<body class="body">
		<center>
  				<br />
   		  		<img src="images/loading.gif"></img>
  				<div style="display: block; width:65%; background: Yellow; text-align:center; font-size:18px; padding:25px;" id="information">
					<?php
						$teammateStr = "teammate";
						if ($studyID>2)
							$teammateStr = "teammates";
							
						$completeStr = "completes";
						if ($studyID>2)
							$completeStr = "complete";
							
						$isStr = "is";
						if ($studyID>2)
							$isStr = "are";
							
							//echo "Hi ".$synched." - ".$rightPage." - ".$studyID;
					?>
  					<strong>
  							Please wait a moment while your <?php echo $teammateStr; ?> <?php echo $completeStr;?> the previous stage.
  							<br />
  							Once your <?php echo $teammateStr; ?> <?php echo $isStr;?> ready, you will be automatically redirected to the next stage. 
  					</strong>
  				</div>	
		</center>
	</body>
</html>
<?php 
	}	
?>