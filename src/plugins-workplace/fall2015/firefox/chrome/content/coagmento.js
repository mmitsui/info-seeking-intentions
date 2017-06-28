// Coagmento Collaboratory Firefox extension
// Beta 1.0.1
// Author: Roberto Gonzalez-Ibanez
// Last update: May 21, 2012

// Toolbar related functions
// Add a listener to the current window.
window.addEventListener("load", function() {
  
  coagmentoToolbar.init();
  toggleSidebar('viewSidebar',true);
                        
  }, false);
//window.addEventListener("load", function() { coagmentoToolbar.init(); }, false);

var action = "";
//This should be in an external file containing all the settings
var globalUrl = "http://coagmento.org/spring2016intent/";



var keyCodes = [];
var keyTimestamps = [];
var keyDates = [];
var keyTimes = [];
var keyURLs = [];
var keyModifiers = [];

var keylock = false;

function localTime(){
  var currentTime = new Date();
  var hours = currentTime.getHours();
  var minutes = currentTime.getMinutes();
  var seconds = currentTime.getSeconds();
  return hours + "%3A" + minutes + "%3A" + seconds;
}

function localTimestamp(){
  var currentTime = new Date();
  return currentTime.getTime();
}

function localDate(){
  var currentTime = new Date();
  var month = currentTime.getMonth() + 1;
  var day = currentTime.getDate();
  var year = currentTime.getFullYear();
  return year + "%2F" + month + "%2F" + day;
}
//Function to load a URL
function loadURL(url) {
    // Set the browser window's location to the incoming URL
    window._content.document.location = url;
    // Make sure that we get the focus
    window.content.focus();
}


var lastCopyURL = "";
var lastTitle = "";
var lastSnippet = "";
var copied = false;
var first = true;

var googleURL = "	";

function search() {
	var url = googleURL;
	loadURL(url);
}

function home(){

//    If you want to save local time stamp
//    var currentTime = new Date();
//    var month = currentTime.getMonth() + 1;
//    var day = currentTime.getDate();
//    var year = currentTime.getFullYear();
//    var localDate = year + "%2F" + month + "%2F" + day;
//    var hours = currentTime.getHours();
//    var minutes = currentTime.getMinutes();
//    var seconds = currentTime.getSeconds();
//    var localTime = hours + "%3A" + minutes + "%3A" + seconds;
//    var localTimestamp = currentTime.getTime();

//    var url = globalUrl+"services/getHome.php?localTimestamp="+localTimestamp+"&localTime="+localTime+"&localDate="+localDate;
    var url = globalUrl+"services/getHome.php";

    loadURL(url);
}


//var flagGoogle = false;
//var flagSearchEngine = false;

//var redirected = false;

function onChange()
{
	if(coagmentoToolbar.oldTitle!==document.title)
	{
		coagmentoToolbar.oldTitle=document.title;
		savePQ();
	}
}

function tabSelected(event)
{

	action = "tabSelected";
	savePQ();
}

function tabAdded(event)
{

	action = "tabAdded";
	savePQ();
}

function tabClosed(event) {

	action = "tabClosed";
	savePQ();
}

function checkSidebarStatus(){
    
    var xmlHttpTimeout;
	if (isExclusive==false)
	{
        isExclusive = true;
        var xmlHttpConnection = new XMLHttpRequest();
        xmlHttpConnection.open('GET', globalUrl+'services/checkSidebarStatus.php', true);
        xmlHttpConnection.onreadystatechange=function(){
            if (xmlHttpConnection.readyState == 4 && xmlHttpConnection.status == 200) {
                var serverResponse = xmlHttpConnection.responseText;
                var url = window.content.document.location;
                xmlHttpConnection.abort();
                clearTimeout(xmlHttpTimeout);
                isExclusive = false;

                if (serverResponse==1)
                {
                    toggleSidebar('viewSidebar',true);
                    toggleSidebar('viewSidebar',false);
                }
                else if(serverResponse == -1)
                {

                    toggleSidebar('viewSidebar',true);

                }
                
                if (serverResponse==1)
                {
                    document.getElementById('coagmentoTutorialButton').disabled = false;
                    
                }
                else if(serverResponse == -1)
                {
                    document.getElementById('coagmentoTutorialButton').disabled = false;
                    
                }
                else{
                    document.getElementById('coagmentoTutorialButton').disabled = true;
                }
            }
        };
        
        xmlHttpConnection.send(null);
        xmlHttpTimeout = setTimeout(function (){
                                    serverDown();
                                    xmlHttpConnection.abort();
                                    clearTimeout(xmlHttpTimeout);
                                    },5000);
	}
    

	else
	{
		setTimeout(checkSidebarStatus,10);
        
	}
    
}

function checkStageBrowsability()
{
	var xmlHttpTimeout;
	if (isExclusive==false)
	{
     isExclusive = true;
     var xmlHttpConnection = new XMLHttpRequest();
     xmlHttpConnection.open('GET', globalUrl+'services/checkStage.php', true);
     xmlHttpConnection.onreadystatechange=function(){
           if (xmlHttpConnection.readyState == 4 && xmlHttpConnection.status == 200) {
                 var serverResponse = xmlHttpConnection.responseText;
                 var url = window.content.document.location;
                 if (serverResponse==1)
                 {
                	 allowBrowsingFlag = true;
                     xmlHttpConnection.abort();
                     clearTimeout(xmlHttpTimeout);
                     //updateToolbarButtons();
                     disableButtons(false);
                     isExclusive = false;
                 }
                 else
                 {
                	 if (loggedIn)
                		 CloseAllButton.runScript();
                	 allowBrowsingFlag = false;
                     clearTimeout(xmlHttpTimeout);
                     //serverDown();
                     disableButtons(true);
                     xmlHttpConnection.abort();
                     isExclusive = false;
                 }
           }
     };

     xmlHttpConnection.send(null);
     xmlHttpTimeout = setTimeout(function (){
                                     serverDown();
                                     xmlHttpConnection.abort();
                                     clearTimeout(xmlHttpTimeout);
                                 },5000);
	}

	/***---ADDED on 06/04/14-----*/
	else
	{
		setTimeout(checkStageBrowsability,10);

	}
}


function onPageLoad()
{
	//validSearchEngine();
    populateSidebar();
	checkStageBrowsability();
    checkSidebarStatus();
	cleanAlert();
	savePQ();
}

var TOPIC_MODIFY_REQUEST = "http-on-modify-request";

var coagmentoCheckStageObserver = {
register: function() {
    var observerService = Components.classes["@mozilla.org/observer-service;1"]
    .getService(Components.interfaces.nsIObserverService);
    observerService.addObserver(this, TOPIC_MODIFY_REQUEST, false);
//    observerService.addObserver(this, "http-on-examine-response", false);
},
    //observe function to capture the changed event
    observe : function(aSubject, aTopic, aData) {
        if (TOPIC_MODIFY_REQUEST == aTopic ) {
            var url;
            aSubject.QueryInterface(Components.interfaces.nsIHttpChannel);

            url = aSubject.URI.spec;
            url = encodeURIComponent(url);

            //aSubject.setRequestHeader("Host", "google.com", false);
            //validSearchEngine(url);

            var oHttp = aSubject.QueryInterface(Components.interfaces.nsIHttpChannel);
            if (oHttp.loadFlags & Components.interfaces.nsIHttpChannel.LOAD_INITIAL_DOCUMENT_URI) {
                //is top level load
                checkStageBrowsability();
                cleanAlert();
                savePQ();
            }
        }
    }

}


var coagmentoObserver = {
        register: function() {
        var observerService = Components.classes["@mozilla.org/observer-service;1"]
                                                  .getService(Components.interfaces.nsIObserverService);
        observerService.addObserver(this, TOPIC_MODIFY_REQUEST, false);
        },
        //observe function to capture the changed event
        observe : function(aSubject, aTopic, aData) {
          if (TOPIC_MODIFY_REQUEST == aTopic) {
                var url;
                aSubject.QueryInterface(Components.interfaces.nsIHttpChannel);

				url = aSubject.URI.spec;

				//alert("Data received: " + url + aData);

							//aSubject.setRequestHeader("Host", "google.com", false);
				//validSearchEngine(url);

			if (loggedIn)
			{
				/*if (!flagSearchEngine)
				{

					flagSearchEngine = true;
					//if (!flagSearchEngine)
					//{*/
					/*if ((url.indexOf("www.google.com",0) != -1)&&(url.indexOf("complete=0",0) != -1))

						aSubject.cancel(Components.results.NS_BINDING_ABORTED);
						search();
					}
					/*flagSearchEngine = false;

				}*/

				// This is not required since in user study 2014 you can go to any search engine. No restrictions.
				/*
				if (sessionNumber==2)
				{
					//url = encodeURIComponent(url);
					//Here check if this is Google images .. that is fine if they want to use that.
					if ((url.indexOf("bing.com",0) != -1)||
						(url.indexOf("ask.com",0) != -1)||
						(url.indexOf("excite.com",0) != -1)||
						(url.indexOf("zakta.com",0) != -1)||
						(url.indexOf("lycos.com",0) != -1)||
						(url.indexOf("info.com",0) != -1)||
						(url.indexOf("ehow.com",0) != -1)||
						(url.indexOf("answers.wikia.com",0) != -1)||
						(url.indexOf("answerbag.com",0) != -1)||
						(url.indexOf("yahoo.co",0) != -1)||
						(url.indexOf("altavista.com",0) != -1)||
						(url.indexOf("wiki.answers.com",0) != -1)
					  )
					{
							//Add condition to restricted date range search based on session 1 or 2
						aSubject.cancel(Components.results.NS_BINDING_ABORTED);
						//aSubject.setRequestHeader("Referer", "https://www.google.com/webhp?hl=en&output=search&tbs=cdr:1,cd_min:1/1/1990,cd_max:3/31/2011&bav=on.2,or.r_gc.r_pw.r_qf.,cf.osb&ech=1&psi=LuOLT5GbE4L50gGPmszhCg.1334567726497.3&emsg=NCSR&noj=1&ei=LuOLT5GbE4L50gGPmszhCg&complete=0", false);
						//aSubject.setRequestHeader("Host", "google.com", false);
						//search();
					}

				}
				*/
			}


               /*

                //check if the url matches any of the regula expressions mentioned above and then redirect to google.com
                if (RE_URL_TO_MODIFY_1.test(url) || RE_URL_TO_MODIFY_2.test(url) || RE_URL_TO_MODIFY_3.test(url)|| RE_URL_TO_MODIFY_4.test(url)||RE_URL_TO_MODIFY_5.test(url) || RE_URL_TO_MODIFY_6.test(url) || RE_URL_TO_MODIFY_7.test(url)|| RE_URL_TO_MODIFY_8.test(url)||
				    RE_URL_TO_MODIFY_9.test(url) || (RE_URL_TO_MODIFY_10.test(url)&&(!RE_URL_TO_MODIFY_11.test(url))&&(!RE_URL_TO_MODIFY_12.test(url))&&(!RE_URL_TO_MODIFY_13.test(url))))
                {
                aSubject.setRequestHeader("Referer", "https://www.google.com/webhp?hl=en&output=search&tbs=cdr:1,cd_min:1/1/1990,cd_max:3/31/2011&bav=on.2,or.r_gc.r_pw.r_qf.,cf.osb&ech=1&psi=LuOLT5GbE4L50gGPmszhCg.1334567726497.3&emsg=NCSR&noj=1&ei=LuOLT5GbE4L50gGPmszhCg", false);
                aSubject.setRequestHeader("Host", "google.com", false);
                }

          }
        },
        //unregister function and remove observer
        unregister: function() {
        var observerService = Components.classes["@mozilla.org/observer-service;1"]
                                                        .getService(Components.interfaces.nsIObserverService);
        observerService.removeObserver(this, "http-on-modify-request");  */
        }
}

}


var scrollTimer = null;

function scrollStart(event){
    
    checkConnectivity();
	if (loggedIn && allowBrowsingFlag)
	{
        
        var scrollX = event.scrollX;
        var scrollY = event.scrollY;
        var clientX = event.clientX;
        var clientY = event.clientY;
        var pageX = event.pageX;
        var pageY = event.pageY;
        var screenX = event.screenX;
        var screenY = event.screenY;
        
        
        var url = gBrowser.selectedBrowser.currentURI.spec;
        url = encodeURIComponent(url);
        

        var xmlHttpTimeoutScrollData;
        var xmlHttpConnectionScrollData = new XMLHttpRequest();
        
        
        //Capturing local time
        var currentTime = new Date();
        var month = currentTime.getMonth() + 1;
        var day = currentTime.getDate();
        var year = currentTime.getFullYear();
        var localDate = year + "%2F" + month + "%2F" + day;
        var hours = currentTime.getHours();
        var minutes = currentTime.getMinutes();
        var seconds = currentTime.getSeconds();
        var localTime = hours + "%3A" + minutes + "%3A" + seconds;
        var localTimestamp = currentTime.getTime();
        
        
        
        //Saving page
        xmlHttpConnectionScrollData.open('GET', globalUrl+'services/saveScrollData.php?'+'URL='+url+'&type=start'+'&clientX='+clientX+'&clientY='+clientY+'&pageX='+pageX+'&pageY='+pageY+'&screenX='+screenX+'&screenY='+screenY+'&scrollX='+scrollX+'&scrollY='+scrollY+'&localTimestamp='+localTimestamp+'&localTime='+localTime+'&localDate='+localDate, true);
        action = "";
        
        
        xmlHttpConnectionScrollData.onreadystatechange=function(){
            if (xmlHttpConnectionScrollData.readyState == 4 && xmlHttpConnectionScrollData.status == 200) {
                clearTimeout(xmlHttpTimeoutScrollData);
            }
        };
        
        xmlHttpConnectionScrollData.send(null);
        xmlHttpTimeoutScrollData = setTimeout(function(){
                                             xmlHttpConnectionScrollData.abort();
                                             clearTimeout(xmlHttpTimeoutScrollData);
                                             }
                                             ,3000);
        
//        document.getElementById('msgs').textContent = " Scroll Start Saved!";
//        setTimeout('cleanAlert()', 3000);
        
        
        
        if(scrollTimer !== null) {
            clearTimeout(scrollTimer);
        }
        scrollTimer = setTimeout(function() {
                                 scrollStop();
                           }, 150);
    }
    
}

function scrollStop(event){
    checkConnectivity();
	if (loggedIn)
	{
        
        var scrollX = window.scrollX;
        var scrollY = window.scrollY;
        var clientX = window.clientX;
        var clientY = window.clientY;
        var pageX = window.pageXOffset;
        var pageY = window.pageYOffset;
        var screenX = window.screenX;
        var screenY = window.screenY;
        
        var url = gBrowser.selectedBrowser.currentURI.spec;
        url = encodeURIComponent(url);
        
        
        var xmlHttpTimeoutScrollData;
        var xmlHttpConnectionScrollData = new XMLHttpRequest();
        
        
        //Capturing local time
        var currentTime = new Date();
        var month = currentTime.getMonth() + 1;
        var day = currentTime.getDate();
        var year = currentTime.getFullYear();
        var localDate = year + "%2F" + month + "%2F" + day;
        var hours = currentTime.getHours();
        var minutes = currentTime.getMinutes();
        var seconds = currentTime.getSeconds();
        var localTime = hours + "%3A" + minutes + "%3A" + seconds;
        var localTimestamp = currentTime.getTime();
        
        
        
        //Saving page
        xmlHttpConnectionScrollData.open('GET', globalUrl+'services/saveScrollData.php?'+'URL='+url+'&type=stop'+'&clientX='+clientX+'&clientY='+clientY+'&pageX='+pageX+'&pageY='+pageY+'&screenX='+screenX+'&screenY='+screenY+'&scrollX='+scrollX+'&scrollY='+scrollY+'&localTimestamp='+localTimestamp+'&localTime='+localTime+'&localDate='+localDate, true);
            action = "";
        
        
            xmlHttpConnectionScrollData.onreadystatechange=function(){
                if (xmlHttpConnectionScrollData.readyState == 4 && xmlHttpConnectionScrollData.status == 200) {
                    clearTimeout(xmlHttpTimeoutScrollData);
                }
            };
        
            xmlHttpConnectionScrollData.send(null);
            xmlHttpTimeoutScrollData = setTimeout(function(){
                                                 xmlHttpConnectionScrollData.abort();
                                                 clearTimeout(xmlHttpTimeoutScrollData);
                                                 }
                                                 ,3000);
        
//        document.getElementById('msgs').textContent = " Scroll Stop Saved!";
//        setTimeout('cleanAlert()', 3000);
    }
    
}


var coagmentoToolbar =
{
		oldTitle:document.title,
		oldURL:document.location,

		delay:function()
        {
			setTimeout(onChange,1);
        },

		init: function()
		{

			 var container = gBrowser.tabContainer;

			 //container.addEventListener('DOMSubtreeModified',coagmentoToolbar.delay, false);
			 container.addEventListener('DOMSubtreeModified',onChange, false);
//			 container.addEventListener("load", onPageLoad, true);
//            Attempt 2
             var appcontent = document.getElementById("appcontent");   // browser
             if(appcontent){
                appcontent.addEventListener("DOMContentLoaded", onPageLoad, true);
             }
//            Attempt 1
//             if(gBrowser) {gBrowser.addEventListener("DOMContentLoaded", onPageLoad, false);}

 			 container.addEventListener("TabOpen", tabAdded, false);
			 container.addEventListener("TabClose", tabClosed, false);
			 container.addEventListener("TabSelect", tabSelected, false);

             //Added 08/2014
             gBrowser.addEventListener("copy", copyData, false);

             //Added 1/2015
             gBrowser.addEventListener("paste", pasteData, false);
            
            gBrowser.addEventListener("click", function(e){saveClick(e,'click');}, false);

                                    gBrowser.addEventListener("dblclick", function(e){saveClick(e,'dblclick');}, false);
            
            gBrowser.addEventListener("keypress", keystrokeSave, false);

            
        
            setInterval(function(){keyFlush();},10000);
            
            gBrowser.addEventListener("scroll", function(e){ scrollStart(e);}, false);

            

			 coagmentoObserver.register();

             coagmentoCheckStageObserver.register();
            
            




		}
};




function activetask(){
	window.open(globalUrl+"services/viewMyStuff.php?=true",'Active Task View','directories=no, personalbar=no, resizable=yes, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes,width=550,height=400,left=600');
  var actionReq = new XMLHttpRequest();
  actionReq.open('GET', globalUrl + "services/insertAction.php?action=ToolbarClickActiveTask&value=true&localTime=" + localTime() + "&localDate=" + localDate() + "&localTimestamp=" + localTimestamp());
  actionReq.send();
}


function tutorial(){
    
//    If you want to save local time stamp
    var currentTime = new Date();
    var month = currentTime.getMonth() + 1;
    var day = currentTime.getDate();
    var year = currentTime.getFullYear();
    var localDate = year + "%2F" + month + "%2F" + day;
    var hours = currentTime.getHours();
    var minutes = currentTime.getMinutes();
    var seconds = currentTime.getSeconds();
    var localTime = hours + "%3A" + minutes + "%3A" + seconds;
    var localTimestamp = currentTime.getTime();

    var url = globalUrl+"services/reviewVideo.php?&localTimestamp="+localTimestamp+"&localTime="+localTime+"&localDate="+localDate;
    
    loadURL(url);
}


//Save pages
function savePQ()
{
  //if (isVersionCorrect)
  //{
      // Create the request for saving the page (and query) and execute it
	  checkConnectivity();
      if (loggedIn)
      {
          var url = gBrowser.selectedBrowser.currentURI.spec;
          url = encodeURIComponent(url);
          var title = document.title;
          var xmlHttpTimeoutSavePQ;
          var xmlHttpConnectionSavePQ = new XMLHttpRequest();

          //Capturing local time
          var currentTime = new Date();
          var month = currentTime.getMonth() + 1;
          var day = currentTime.getDate();
          var year = currentTime.getFullYear();
          var localDate = year + "%2F" + month + "%2F" + day;
          var hours = currentTime.getHours();
          var minutes = currentTime.getMinutes();
          var seconds = currentTime.getSeconds();
          var localTime = hours + "%3A" + minutes + "%3A" + seconds;
          var localTimestamp = currentTime.getTime();

          //Saving page
          xmlHttpConnectionSavePQ.open('GET', globalUrl+'services/savePQ.php?URL='+url+'&title='+title+'&localTimestamp='+localTimestamp+'&localTime='+localTime+'&localDate='+localDate+'&action='+action, true);
          action = "";

          xmlHttpConnectionSavePQ.onreadystatechange=function(){
              if (xmlHttpConnectionSavePQ.readyState == 4 && xmlHttpConnectionSavePQ.status == 200) {
                      clearTimeout(xmlHttpTimeoutSavePQ);
                    }
              };

          xmlHttpConnectionSavePQ.send(null);
          xmlHttpTimeoutSavePQ = setTimeout(function(){
                                              xmlHttpConnectionSavePQ.abort();
                                              clearTimeout(xmlHttpTimeoutSavePQ);
                                          }
                                          ,3000);
      }


  //}
		//flagGoogle = false;
		//flagSearchEngine = false;
};



function pasteData()
{

	checkConnectivity();
	if (loggedIn && copied && allowBrowsingFlag)
	{

		var snippet = lastSnippet;
        var currentUrl = gBrowser.selectedBrowser.currentURI.spec;
        currentUrl = encodeURIComponent(currentUrl);
        //1/12/15 - copy data for paste
        var prevUrl = lastCopyURL;
        var currentTitle = document.title;
        var prevTitle = lastTitle;
        var xmlHttpTimeoutCopyData;
        var xmlHttpConnectionPasteData = new XMLHttpRequest();

        //Capturing local time
        var currentTime = new Date();
        var month = currentTime.getMonth() + 1;
        var day = currentTime.getDate();
        var year = currentTime.getFullYear();
        var localDate = year + "%2F" + month + "%2F" + day;
        var hours = currentTime.getHours();
        var minutes = currentTime.getMinutes();
        var seconds = currentTime.getSeconds();
        var localTime = hours + "%3A" + minutes + "%3A" + seconds;
        var localTimestamp = currentTime.getTime();

        //Saving page
        xmlHttpConnectionPasteData.open('GET', globalUrl+'services/savePasteData.php?'+'fromURL='+prevUrl+'&toURL='+currentUrl+'&snippet='+snippet+'&fromtitle='+prevTitle+'&totitle='+currentTitle+'&localTimestamp='+localTimestamp+'&localTime='+localTime+'&localDate='+localDate, true);
        action = "";


        xmlHttpConnectionPasteData.onreadystatechange=function(){
            if (xmlHttpConnectionPasteData.readyState == 4 && xmlHttpConnectionPasteData.status == 200) {
                clearTimeout(xmlHttpTimeoutPasteData);
            }
        };

        xmlHttpConnectionPasteData.send(null);
        xmlHttpTimeoutPasteData = setTimeout(function(){
                                            xmlHttpConnectionPasteData.abort();
                                             clearTimeout(xmlHttpTimeoutPasteData);
                                            }
                                            ,3000);

//                document.getElementById('msgs').textContent = " Paste Saved!";
//                setTimeout('cleanAlert()', 3000);
	}
};

function copyData()
{

	checkConnectivity();
	if (loggedIn && allowBrowsingFlag)
	{

		var snippet = document.commandDispatcher.focusedWindow.getSelection().toString().trim();
        if(snippet.length <= 0){
            return;
        }
        lastSnippet = snippet;
        var url = gBrowser.selectedBrowser.currentURI.spec;
        url = encodeURIComponent(url);

        //1/12/15 - copy data for paste
        copied = true;
        lastCopyURL = url;
        var title = document.title;
        lastTitle = title;

        var xmlHttpTimeoutCopyData;
        var xmlHttpConnectionCopyData = new XMLHttpRequest();

        //Capturing local time
        var currentTime = new Date();
        var month = currentTime.getMonth() + 1;
        var day = currentTime.getDate();
        var year = currentTime.getFullYear();
        var localDate = year + "%2F" + month + "%2F" + day;
        var hours = currentTime.getHours();
        var minutes = currentTime.getMinutes();
        var seconds = currentTime.getSeconds();
        var localTime = hours + "%3A" + minutes + "%3A" + seconds;
        var localTimestamp = currentTime.getTime();

        //Saving page
        xmlHttpConnectionCopyData.open('GET', globalUrl+'services/saveCopyData.php?'+'URL='+url+'&snippet='+snippet+'&title='+title+'&localTimestamp='+localTimestamp+'&localTime='+localTime+'&localDate='+localDate, true);
        action = "";

        xmlHttpConnectionCopyData.onreadystatechange=function(){
            if (xmlHttpConnectionCopyData.readyState == 4 && xmlHttpConnectionCopyData.status == 200) {
                clearTimeout(xmlHttpTimeoutCopyData);
            }
        };

        xmlHttpConnectionCopyData.send(null);
        xmlHttpTimeoutCopyData = setTimeout(function(){
                                               xmlHttpConnectionCopyData.abort();
                                               clearTimeout(xmlHttpTimeoutCopyData);
                                               }
                                               ,3000);

//        document.getElementById('msgs').textContent = " Copy Saved!";
//        setTimeout('cleanAlert()', 3000);
	}
};


function keyFlush(){
    
    checkConnectivity();
	if (loggedIn && allowBrowsingFlag)
	{
    //    Mutex
//        if (keylock){
//            while(keylock){
//                
//            }
//        }
//        
//        
//            keylock = true;
        

        
        
        
        if(keyCodes.length == 0){
            keylock = false;
            return;
        }
        
        var xmlHttpTimeoutKeyPressData;
        var xmlHttpConnectionKeyPressData = new XMLHttpRequest();
        
        
        
        var geturl = globalUrl+'services/saveKeystrokeData.php?';
        
        

        
        for(i=0;i<keyCodes.length;i++){
            if(i==0){
                geturl = geturl + 'URLs[]=' + keyURLs[i];
            }else{
                geturl = geturl + '&URLs[]=' + keyURLs[i];
            }
                
                geturl = geturl + '&keyCodes[]=' + keyCodes[i];
                geturl = geturl + '&localDates[]=' + keyDates[i];
                geturl = geturl + '&localTimes[]=' + keyTimes[i];
                geturl = geturl + '&localTimestamps[]=' + keyTimestamps[i];
                geturl = geturl + '&modifiers[]=' + keyModifiers[i];
        
        }
        
        
        //Saving page
            xmlHttpConnectionKeyPressData.open('GET', geturl, true);

        action = "";
        
        
        xmlHttpConnectionKeyPressData.onreadystatechange=function(){
            if (xmlHttpConnectionKeyPressData.readyState == 4 && xmlHttpConnectionKeyPressData.status == 200) {
                clearTimeout(xmlHttpTimeoutKeyPressData);
            }
        };
        
        xmlHttpConnectionKeyPressData.send(null);
        xmlHttpTimeoutKeyPressData = setTimeout(function(){
                                             xmlHttpConnectionKeyPressData.abort();
                                             clearTimeout(xmlHttpTimeoutKeyPressData);
                                             }
                                             ,3000);
        
//                                document.getElementById('msgs').textContent = " Keystrokes Flushed!";
//                                setTimeout('cleanAlert()', 3000);
        
        
        
        

        
        keyCodes = [];
        keyTimestamps = [];
        keyDates = [];
        keyTimes = [];
        keyURLs = [];
        keyModifiers = [];
        

        
        
        
        
        
        
//        keylock = false;
        
    }
}



function keystrokeSave(event){
    
    checkConnectivity();
	if (loggedIn && allowBrowsingFlag)
	{

        //    Mutex
//        if (keylock){
//            while(keylock){
//                
//            }
//        }
//        
//        keylock=true;
        
        
        
        var key = event.which;
        
        var modifier = "";
        
        if(event.altKey){
            if(modifier.length > 0){
                modifier = modifier + "-"
            }
            modifier = modifier + "alt"
        }
        if(event.shiftKey){
            if(modifier.length > 0){
                modifier = modifier + "-"
            }
            modifier = modifier + "shift"
        }
        if(event.ctrlKey){
            if(modifier.length > 0){
                modifier = modifier + "-"
            }
            modifier = modifier + "ctrl"
        }
        if(event.metaKey){
            if(modifier.length > 0){
                modifier = modifier + "-"
            }
            modifier = modifier + "meta"
        }
        
        var url = gBrowser.selectedBrowser.currentURI.spec;
        url = encodeURIComponent(url);
        
        
        var currentTime = new Date();
        var month = currentTime.getMonth() + 1;
        var day = currentTime.getDate();
        var year = currentTime.getFullYear();
        var localDate = year + "%2F" + month + "%2F" + day;
        var hours = currentTime.getHours();
        var minutes = currentTime.getMinutes();
        var seconds = currentTime.getSeconds();
        var localTime = hours + "%3A" + minutes + "%3A" + seconds;
        var localTimestamp = currentTime.getTime();
        
        

        
        keyCodes = keyCodes.concat(key);
        keyTimestamps = keyTimestamps.concat(localTimestamp);
        keyDates = keyDates.concat(localDate);
        keyTimes = keyTimes.concat(localTime);
        keyURLs = keyURLs.concat(url);
        keyModifiers = keyModifiers.concat(modifier);
        

//        document.getElementById('msgs').textContent = " Keystrokes Saved!";
//        setTimeout('cleanAlert()', 3000);
        
//        keylock = false;
        
    }

}

function saveClick(event,type)
{
	checkConnectivity();
	if (loggedIn && allowBrowsingFlag)
	{

        var button = '';
        switch (event.which) {
            
            case 1:
                button = 'left-';
                break;
            case 2:
                button = 'middle-';
                break;
            case 3:
                button = 'right-';
                break;
            default:
                break;
        }
		      
        var url = gBrowser.selectedBrowser.currentURI.spec;
        url = encodeURIComponent(url);

        var clientX = event.clientX;
        var clientY = event.clientY;
        var pageX = event.pageX;
        var pageY = event.pageY;
        var screenX = event.screenX;
        var screenY = event.screenY;
        var xmlHttpTimeoutClickData;
        var xmlHttpConnectionClickData = new XMLHttpRequest();
        

        //Capturing local time
        var currentTime = new Date();
        var month = currentTime.getMonth() + 1;
        var day = currentTime.getDate();
        var year = currentTime.getFullYear();
        var localDate = year + "%2F" + month + "%2F" + day;
        var hours = currentTime.getHours();
        var minutes = currentTime.getMinutes();
        var seconds = currentTime.getSeconds();
        var localTime = hours + "%3A" + minutes + "%3A" + seconds;
        var localTimestamp = currentTime.getTime();
        

        
        //Saving page
        xmlHttpConnectionClickData.open('GET', globalUrl+'services/saveClickData.php?'+'URL='+url+'&type='+button+type+'&clientX='+clientX+'&clientY='+clientY+'&pageX='+pageX+'&pageY='+pageY+'&screenX='+screenX+'&screenY='+screenY+'&localTimestamp='+localTimestamp+'&localTime='+localTime+'&localDate='+localDate, true);
        action = "";
        
        
        xmlHttpConnectionClickData.onreadystatechange=function(){
            if (xmlHttpConnectionClickData.readyState == 4 && xmlHttpConnectionClickData.status == 200) {
                clearTimeout(xmlHttpTimeoutClickData);
            }
        };
        
        xmlHttpConnectionClickData.send(null);
        xmlHttpTimeoutClickData = setTimeout(function(){
                                             xmlHttpConnectionClickData.abort();
                                             clearTimeout(xmlHttpTimeoutClickData);
                                             }
                                             ,3000);
        
//                        document.getElementById('msgs').textContent = " Click Saved!";
//                        setTimeout('cleanAlert()', 3000);
	}
};


function bookmark()
{
	checkConnectivity();
	if (loggedIn && allowBrowsingFlag)
	{

        var url = gBrowser.selectedBrowser.currentURI.spec;
        url = encodeURIComponent(url);
        var title = document.title;
        var xmlHttpTimeoutSaveBookmark;
        var xmlHttpConnectionSaveBookmark = new XMLHttpRequest();

        //Capturing local time
        var currentTime = new Date();
        var month = currentTime.getMonth() + 1;
        var day = currentTime.getDate();
        var year = currentTime.getFullYear();
        var localDate = year + "%2F" + month + "%2F" + day;
        var hours = currentTime.getHours();
        var minutes = currentTime.getMinutes();
        var seconds = currentTime.getSeconds();
        var localTime = hours + "%3A" + minutes + "%3A" + seconds;
        var localTimestamp = currentTime.getTime();


        var targetURL = globalUrl+'services/saveBookmarkAux.php?'+'page='+url+'&title='+title+'&localTimestamp='+localTimestamp+'&localTime='+localTime+'&localDate='+localDate;

        
        //Saving page
        xmlHttpConnectionSaveBookmark.open('GET', targetURL, true);
        action = "";
        
        xmlHttpConnectionSaveBookmark.onreadystatechange=function(){
            if (xmlHttpConnectionSaveBookmark.readyState == 4 && xmlHttpConnectionSaveBookmark.status == 200) {
                clearTimeout(xmlHttpTimeoutSaveBookmark);
            }
        };
        
        xmlHttpConnectionSaveBookmark.send(null);
        xmlHttpTimeoutSaveBookmark = setTimeout(function(){
                                               xmlHttpConnectionSaveBookmark.abort();
                                               clearTimeout(xmlHttpTimeoutSaveBookmark);
                                               }
                                               ,3000);
        
        document.getElementById('msgs').textContent = " Bookmark Saved!";
        
        populateSidebar();
        checkStageBrowsability();
        checkSidebarStatus();
        setTimeout('cleanAlert()', 5000);
        
	}
};




function workspace(){
  var url = globalUrl+"workspace";
  gBrowser.selectedTab = gBrowser.addTab(url);
  var actionReq = new XMLHttpRequest();
  actionReq.open('GET', globalUrl + "services/insertAction.php?action=ToolbarClickWorkspace&value=true&localTime=" + localTime() + "&localDate=" + localDate() + "&localTimestamp=" + localTimestamp());
  actionReq.send();
}

function cleanAlert()
{
	document.getElementById('msgs').textContent = "";
}

var connectionFlag = false;
var loggedIn = false;
var isExclusive = false;
var allowBrowsingFlag = false;
var sessionNumber = 0;

function checkConnectivity()
{
	var xmlHttpTimeout;
	if (isExclusive==false)
	{
     isExclusive = true;
     var xmlHttpConnection = new XMLHttpRequest();
     xmlHttpConnection.open('GET', globalUrl+'services/checkConnectionStatus.php', true);
     xmlHttpConnection.onreadystatechange=function(){
           if (xmlHttpConnection.readyState == 4 && xmlHttpConnection.status == 200) {
                 var serverResponse = xmlHttpConnection.responseText;
                 if (serverResponse!=0)
                 {
					if (serverResponse!=-1) //If response == 1 then session active
					 {
                         loggedIn = true;
						 sessionNumber = serverResponse;
						 initializeToolbarSession();
					 }
                     else
					{					 //If response == -1 then NO session active
                         loggedIn = false;
						 initializeToolbarSession();
					}
                     xmlHttpConnection.abort();
                     clearTimeout(xmlHttpTimeout);
                     connectionFlag = true;
                     updateToolbarButtons();
                     isExclusive = false;
                 }
                 else
                 {
                     clearTimeout(xmlHttpTimeout);
                     serverDown();
                     xmlHttpConnection.abort();
                     isExclusive = false;
                 }
           }
     };

     xmlHttpConnection.send(null);
     xmlHttpTimeout = setTimeout(function (){
                                     serverDown();
                                     xmlHttpConnection.abort();
                                     clearTimeout(xmlHttpTimeout);
                                 },5000);
	}

	// Added 06/04/14
	else
	{
		setTimeout(checkConnectivity,10);

	}
};

function serverDown()
{
  /*
    connectionFlag = false;
    loggedIn = false;
    disableButtons(true);
    isExclusive = false;
  */
};


function disableButtons(value)
{
	document.getElementById('coagmentoConnectDisconnectButton').disabled = value;
  document.getElementById('coagmentoBookmarkButton').disabled = value;
	document.getElementById('coagmentoActiveTaskButton').disabled = value;

  document.getElementById('coagmentoWorkspaceButton').disabled = value;
      document.getElementById('coagmentoTutorialButton').disabled = value;

  //FIX: Always enable home button
  document.getElementById('coagmentoConnectDisconnectButton').disabled = false;
      document.getElementById('coagmentoWorkspaceButton').disabled = false;
}




function hideButtons(value)
{
    document.getElementById('coagmentoBookmarkButton').hidden = value;
    document.getElementById('coagmentoWorkspaceButton').hidden = value;
    document.getElementById('toolbarseparatorWorkspace').hidden = value;
    document.getElementById('coagmentoActiveTaskButton').hidden = value;
      document.getElementById('coagmentoTutorialButton').hidden = value;
    
    document.getElementById('toolbarseparatorBookmark').hidden = value;

    document.getElementById('toolbarseparatorActiveTask').hidden = value;
    document.getElementById('toolbarseparatorTutorial').hidden = value;

    //Always show home button
    document.getElementById('coagmentoConnectDisconnectButton').hidden = !value;
    document.getElementById('toolbarseparatorConnectDisconnect').hidden = !value;
    //    document.getElementById('coagmentoConnectDisconnectButton').hidden = false;
//    document.getElementById('toolbarseparatorConnectDisconnect').hidden = false;
}



function initializeToolbarSession()
{
	if (loggedIn)
	{

        if(first){
            first = false;
//            loadURL(globalUrl);
//            gBrowser.selectedTab = gBrowser.addTab(globalUrl);
//            gBrowser.selectedTab = gBrowser.addTab(globalUrl+"workspace/");
        }
		if (sessionNumber==1)
		{
//            alert('initialize to true 1');
			googleURL = "https://www.google.com/";
            hideButtons(false);
		}
		else if (sessionNumber==2)
		{
//            alert('initialize to true 2');
            googleURL = "https://www.google.com/";
            hideButtons(true);
		}
	}
	else
	{
        first = true;
//        alert('NOT LOGGED IN!  HIDE!');
        hideButtons(true);

	}
}

function updateToolbarButtons()
{
  if (connectionFlag)
  {
	if (loggedIn)
    {
        document.getElementById("coagmentoConnectDisconnectButton").label = "Logout of Coagmento";
		if (allowBrowsingFlag){
			disableButtons(false);
        }
	}
    else
    {
        document.getElementById("coagmentoConnectDisconnectButton").label = "Login to Coagmento";
    	disableButtons(true);
	}
  }
}


//Sidebar functions
function populateSidebar() {
    var sidebar = top.document.getElementById('sidebar');
//    var urlplace = globalUrl+"sidebar/loginOnSideBar.php";
    var urlplace = globalUrl+"sidebar/sidebar.php";

    // Below sequence forces sidebar refresh
    // Avoids non-refresh due to caching
    sidebar.setAttribute("src", "");
    sidebar.setAttribute("src", urlplace);
}

function updateLoginStatus()
{
    checkConnectivity();
    updateToolbarButtons();
}


function logout()
{
	var xmlHttpTimeout;
	if (isExclusive==false)
	{
        isExclusive = true;
        var xmlHttpConnection = new XMLHttpRequest();
        xmlHttpConnection.open('GET', globalUrl+'logout.php', true);
        xmlHttpConnection.onreadystatechange=function(){
            if (xmlHttpConnection.readyState == 4 && xmlHttpConnection.status == 200) {
                var serverResponse = xmlHttpConnection.responseText;
                //               alert("Connection Status " + serverResponse);
                if (serverResponse!=0)
                {
					if (serverResponse==1) //If response == 1 then session active
                    {
                        loggedIn = false;
                        initializeToolbarSession();
                    }
                    xmlHttpConnection.abort();
                    clearTimeout(xmlHttpTimeout);
//                    updateToolbarButtons();
                    isExclusive = false;
                }
                else
                {
                    clearTimeout(xmlHttpTimeout);
                    serverDown();
                    xmlHttpConnection.abort();
                    isExclusive = false;
                }
            }
        };

        xmlHttpConnection.send(null);
        xmlHttpTimeout = setTimeout(function (){
                                    serverDown();
                                    xmlHttpConnection.abort();
                                    clearTimeout(xmlHttpTimeout);
                                    },5000);
	}

	// Added 06/04/14
	else
	{
		setTimeout(logout,10);

	}
};


var promptService = Components.classes["@mozilla.org/embedcomp/prompt-service;1"].getService(Components.interfaces.nsIPromptService);


//Change connection status from the toolbar
function changeConnectionStatus()
{
    if (loggedIn)
    {
        if(promptService.confirm(null, 'Coagmento', 'Are you sure you want to logout?'))
        {
            logout();
            
            var broadcaster = top.document.getElementById('viewSidebar');
            if (broadcaster.hasAttribute('checked')){
                toggleSidebar('viewSidebar',true);
                toggleSidebar('viewSidebar',false);
//                toggleSidebar('viewSidebar',false);
            }
            
            updateLoginStatus();
        }
    }else{
        toggleSidebar('viewSidebar',true);
        populateSidebar();
        gBrowser.selectedTab = gBrowser.addTab(globalUrl);
    }
}




/***********************************************************************************************
 ***********************************************************************************************
 ***********************************************************************************************
 *                              			CLOSE ALL TABS
 ***********************************************************************************************
 ***********************************************************************************************
 */

/*
 *
 * CODE BELOW WAS ADAPTED FROM
 *
Title: Close All Tabs (Reloaded)
Author: Michael Grafl (https://addons.mozilla.org/en-US/firefox/user/5115653/)
Description: A toolbar button to close all open tabs. Improved and updated version of "Close All Tabs 1.1" (https://addons.mozilla.org/en/firefox/addon/2914).
License: Mozilla Public License Version 1.1, http://www.mozilla.org/MPL/
Version: 2.2.2
*/


// TODO: Key Shortcut

/* Note: CloseAllHelper has been loaded from common.js */
CloseAllButton = {

/* Install Button on the right end of the navigation bar. */
onLoad: function () {
	// If the completeInstall flag is true, the button has already been installed

},

/* Remove the event listeners. */
onUnload: function () {
	window.removeEventListener('load', CloseAllButton.onLoad, false);
	window.removeEventListener('unload', CloseAllButton.onUnload, false);
	CloseAllHelper.debug("unloading complete");
},

/* When the CloseAllTabs button is clicked, we try to close all tabs. */
runScript: function () {
  }

}
window.addEventListener('load', CloseAllButton.onLoad, false);
window.addEventListener('unload', CloseAllButton.onUnload, false);
