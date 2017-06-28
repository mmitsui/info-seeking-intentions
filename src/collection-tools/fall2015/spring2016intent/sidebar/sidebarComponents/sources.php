<?php
if (session_id() == "")
      session_start();

require_once('../../core/Base.class.php');
require_once('../../core/Util.class.php');

if(isset($_GET['clicktab'])){
  $base = Base::getInstance();
  Util::getInstance()->saveAction("Clicked Sidebar Tab: sources",0, $base);
}

?>
  <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
         "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
  <html>
    <head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
      <title>Sources</title>
	<style type="text/css">
		.cursorType{
		cursor:pointer;
		cursor:hand;
		}
	</style>
    </head>
    <body>
<?php
//		//$height = "700px"; //Added on October 21st, 2010
//		if ($_SESSION['condition']==6)
//			$height = "330px";
//			else
//				if ($_SESSION['condition']==5)
//					$height = "350px";
//				else
					$height = "250px";
?>
<div id="sourcesBox" style="height:<?php echo $height?>;overflow:auto;">
<?php
	require_once("sourcesAux.php");
?>
</div>
</body>
</html>
