<?php
function extractQuery($referrer)
{
	$ref = $referrer;
	$queryString = false;

	$se_stuff = array();
	$se_stuff[] = array("google.com", "q", "Google");
	$se_stuff[] = array("google.co.uk", "q", "Google");
	$se_stuff[] = array("ask.com", "q", "Ask.com");
	$se_stuff[] = array("ask.co.uk", "ask", "Ask.co.uk");
	$se_stuff[] = array("comcast.net", "?cat=Web&con=betaa&q", "Comcast");
	$se_stuff[] = array("yahoo", "p", "Yahoo");
	$se_stuff[] = array("yahoo.co.uk", "p", "Yahoo");
	$se_stuff[] = array("aol.com", "query", "AOL");
	$se_stuff[] = array("msn.com", "q", "MSN");
	$se_stuff[] = array("live.com", "q", "Live");
	$se_stuff[] = array("bing.com", "q", "Bing");
	$se_stuff[] = array("netscape.com", "query", "Netscape");
	$se_stuff[] = array("netzero.net", "query", "NetZero");
	$se_stuff[] = array("altavista.com", "q", "Altavista");
	$se_stuff[] = array("mywebsearch.com", "searchfor", "Mywebsearch");
	$se_stuff[] = array("alltheweb.com", "q", "Alltheweb");
	$se_stuff[] = array("cnn.com", "query", "CNN");
	$se_stuff[] = array("myspace.com", "q", "MySpace");
	$se_stuff[] = array("wikipedia.org", "search", "Wikipedia");
	$se_stuff[] = array("en.wikipedia.org", "search", "Wikipedia");
	$se_stuff[] = array("searchme.com", "q", "Searchme");
	$se_stuff[] = array("duckduckgo.com", "q", "Duckduckgo");

	for($i=0, $size = sizeof($se_stuff); $i < $size; $i++)
	{
		if (stristr($ref,$se_stuff[$i][0]) )
		{
			// Additional check for google to make sure the revised query strings get properly captured
			if(strcmp($se_stuff[$i][0],"google.com")==0 || strcmp($se_stuff[$i][0],"google.co.uk")==0)
			{
				$symbol = $se_stuff[$i][1];
				//Reformulations
				if(stristr($ref,"#$symbol=")){
					$temp1 = explode("#$symbol=", $ref, strlen($ref));
					$temp2 = explode("&", $temp1[count($temp1)-1]);
					$string = $temp2[0];
					$queryString = urldecode($string);
					break;
				}
				if(stristr($ref,"?$symbol=") && !stristr($ref,"&$symbol=")){
					$temp1 = explode("?$symbol=", $ref, strlen($ref));
					$temp2 = explode("&", $temp1[count($temp1)-1]);
					$string = $temp2[0];
					$queryString = urldecode($string);
					break;
				}
				if(stristr($ref,"&$symbol=")){
					$temp1 = explode("&$symbol=", $ref, strlen($ref));
					$temp2 = explode("&", $temp1[count($temp1)-1]);
					$string = $temp2[0];
					$queryString = urldecode($string);
					break;
				}
			}


			// general guery string
			$symbol = $se_stuff[$i][1];
			if(stristr($ref,"?$symbol=") && !stristr($ref,"&$symbol=")){
				$temp1 = explode("?$symbol=", $ref, strlen($ref));
				$temp2 = explode("&", $temp1[count($temp1)-1]);
				$string = $temp2[0];
				$queryString = urldecode($string);
				break;
			}
			if(stristr($ref,"&$symbol=")){
				$temp1 = explode("&$symbol=", $ref, strlen($ref));
				$temp2 = explode("&", $temp1[count($temp1)-1]);
				$string = $temp2[0];
				$queryString = urldecode($string);
				break;
			}

			/*
			$symbol = $se_stuff[$i][1];
			$temp1 = explode("$symbol=", $ref, 2);
			$temp2 = explode("&", $temp1[1]);
			$string = $temp2[0];
			$queryString = urldecode($string);
			break;*/
		}
	}

	return $queryString;
} // end extractQuery
?>
