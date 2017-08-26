var loggedIn = false;
var device = "chrome";
var domain = 'http://coagmento.org/workintent';
var homeUrl = domain + '/index.php';
var actionSaveUrl = domain + '/services/insertAction.php';
var savePQUrl = domain+"/services/savePQ.php";

var checkLoggedInUrl = domain + "/getLoggedIn.php";
var previousTabAction = '';
var previousWindowAction = '';
var previousWebNavAction = '';
var previousAction = '';

var previousTabActionData = null;
var previousWindowActionData = null;
var previousWebNavActionData = null;
var previousActionData = null;
//var serp_storage_url = domain + '/saveserp';
//var check_userid_url = domain + '/users/checkid';



function toggleLoggedIn(logged){
  loggedIn = logged;
}

function renderLoggedIn(loggedIn){
  var red = [255,0,0,255];
  // var green = [0,255,0,255];
  var green = [34,139,34,255];
  if(loggedIn){
    chrome.browserAction.setBadgeText({text:' '});
    chrome.browserAction.setBadgeBackgroundColor({color:green});
  }else{
    chrome.browserAction.setBadgeText({text:' '});
    chrome.browserAction.setBadgeBackgroundColor({color:red});
  }
}

$.ajax({
  url: checkLoggedInUrl,
  method : "post",
  data : {},
  dataType: "text",
  success : function(msg){
    toggleLoggedIn(JSON.parse(msg).loggedin);
    renderLoggedIn(JSON.parse(msg).loggedin);
  },
  error: function(msg){
    toggleLoggedIn(false);
    renderLoggedIn(false);
  }
});


var timerLock = false; // Prevent multiple options pages from opening.

function openOptions() {
    // If not, open up options page if it isn't open.
    var query = {
      url: chrome.runtime.getURL(homeUrl)
//        url: chrome.runtime.getURL("/options.html")
};
chrome.tabs.query(query, function(tabs) {
  if (!timerLock && tabs.length == 0) {
    timerLock = true;
    setTimeout(function() {timerLock = false;}, 1000);
    chrome.tabs.create({'url': url} );
//            chrome.tabs.create({'url': "/options.html"} );    
}
});
}


function savePQ(url,title,active,tabId,windowId,now){
  if(loggedIn){

    var data = {
    url:url,
    title:title,
    active:active,
    tabId:tabId,
    windowId:windowId
    // TODO: action, and other columns
    }

    

    data.localDate = now.getFullYear() + "-" + ("0" + (now.getMonth() + 1)).slice(-2) + "-" + ("0" + now.getDate()).slice(-2);
    data.localTime =  ("0" + now.getHours()).slice(-2) + ":" + ("0" + now.getMinutes()).slice(-2) + ":" + ("0" + now.getSeconds()).slice(-2);
    data.localTimestamp = now.getTime();

    

    $.ajax({
      url: savePQUrl,
      method : "post",
      data : data,
      dataType: "text",
      success : function(resp){



      },
      error: function(resp){

      }
    });
  }

}

function saveAction(action,value,actionJSON,now){
  if(loggedIn){
    var data = {
    action:action,
    value:value,
    actionJSON:JSON.stringify(actionJSON)
    }

    if(action.indexOf("tabs.")!==-1){
      previousTabAction = action;
      previousTabActionData = data;
    }else if(action.indexOf("windows.")!==-1){
      previousWindowAction = action;
      previousWindowActionData = data;
    }else if(action.indexOf("webNavigation.")!==-1){
      previousWebNavAction = action;
      previousWebNavActionData = data;
    }
    previousAction = action;
    previousActionData = data;
    
    data.localDate = now.getFullYear() + "-" + ("0" + (now.getMonth() + 1)).slice(-2) + "-" + ("0" + now.getDate()).slice(-2);
    data.localTime =  ("0" + now.getHours()).slice(-2) + ":" + ("0" + now.getMinutes()).slice(-2) + ":" + ("0" + now.getSeconds()).slice(-2);
    data.localTimestamp = now.getTime();
    $.ajax({
     url: actionSaveUrl,
     method : "post",
     data : data,
     dataType: "text",
     success : function(resp){

     },
     error: function(resp){

     }
   });  
  }
      
}


// TODO ACTIONS
// tab change: onactivated+onhighlighted - use onActivated
// close current tab: onRemoved+onactivated+onHighlighted - use onActivated
// close different tab: onRemoved
// Remove tab from window: onactivated+ondetached+onhighlighted - use onActivated (assumes most recent onActivated is the currently viewed tab.  If activated tab is detached, there's 2 onactivated events.  If an inactive tab is dragged, only one onActivated event)
// Attach tab to window: onAttached, onDetached, onHighlighted,onActivated - use onActivated
// Move tab in current window: onMoved
// "click to open in new tab": onCreated.  onActivated depends on whether immediately set to new tab.  there's an active boolean in the onCreated action. usually active=false
// "open in new window": onCreated.  onActivated depends on whether immediately set to new tab.  there's an active boolean in the onCreated action. usually active=true
// does onCommitted or onVisited happen when there is a click to open in new tab?

// TODO: Window switching is important, not just onActivated.  onActivated doesn't capture that.
// TODO: get windowId for most tab actions?
// TODO: may need an active_tab (boolean) column in pages/queries
// TODO: "click to open Google search result in new tab": a bunch of loading occurs.  This isn't captured by onCreated.  Is this captured by something else?



// Get URL, insert action as savePQ
chrome.tabs.onActivated.addListener(function(activeInfo){
  var now = new Date();

  chrome.tabs.get(activeInfo.tabId, function(tab){
    Url = (tab.hasOwnProperty('url')?tab.url:"");
    title = (tab.hasOwnProperty('title')?tab.title:"");
    active = tab.active;
    tabId = (tab.hasOwnProperty('id')?tab.id:-1);
    windowId = tab.windowId;
    activeInfo.tab = tab;

    chrome.tabs.executeScript(
        tabId,
        { code: "document.referrer;" },
        function(result) {
          activeInfo.referrerInfo = result;

          saveAction("tabs.onActivated",activeInfo.tabId,activeInfo,now);
          savePQ(Url,title,active,tabId,windowId,now);
        }
      );

    
  });


});



chrome.tabs.onAttached.addListener(function(tabId, attachInfo){
 var now = new Date();
 attachInfo.tabId = tabId;
 saveAction("tabs.onAttached",tabId,attachInfo,now);
});


// IMPORTANT
// // TODO: No PQ action here? see if another action accompanies a "open link in new tab" URL
chrome.tabs.onCreated.addListener(function(tab){
  var now = new Date();
  var tabId = tab.id;

  var currentTab = null;

  chrome.tabs.query({active: true, currentWindow: true}, function(arrayOfTabs) {

    currentTab = arrayOfTabs;
  
  

    chrome.tabs.executeScript(
        tabId,
        { code: "document.referrer;" },
        function(result) {
          tab.referrerInfo = result;


          saveAction("tabs.onCreated",tab.id,{currentTab:currentTab,newTab:tab},now);
        }
      );

  });

  chrome.tabs.getCurrent(function (result){
    
  })
  
  

});



// TODO: No PQ action here? see if highlight also changes.
chrome.tabs.onDetached.addListener(function(tabId, detachInfo){
 var now = new Date();
 detachInfo.tabId = tabId;
 saveAction("tabs.onDetached",tabId,detachInfo,now);
});



chrome.tabs.onHighlighted.addListener(function(highlightInfo){
  var now = new Date();
  saveAction("tabs.onHighlighted",highlightInfo.tabIds.join(),highlightInfo,now);
});



// TODO: 2) When move action is executed on an inactive, is there any other action that fires? Such as onHighlighted or onActivated?
chrome.tabs.onMoved.addListener(function(tabId, moveInfo){
  var now = new Date();
  moveInfo.tabId = tabId;
  saveAction("tabs.onMoved",tabId,moveInfo,now);
});


// TODO: Other highlighted/activated actions when an active tab is closed?
chrome.tabs.onRemoved.addListener(function(tabId, removeInfo){
  var now = new Date();
  removeInfo.tabId = tabId;
  saveAction("tabs.onRemoved",tabId,removeInfo,now);
});


// IMPORTANT
chrome.tabs.onReplaced.addListener(function(addedTabId, removedTabId){
 var now = new Date();
 var tabId = addedTabId;

 chrome.tabs.executeScript(
        tabId,
        { code: "document.referrer;" },
        function(result) {
          saveAction("tabs.onReplaced",addedTabId,{addedTabId:addedTabId,removedTabId:removedTabId,referrerInfo:result},now);
        }
      );
 
});



// Status types: either "loading" or "complete"
// Note: only use onCommitted
// TODO: Use only onCommitted?  Or this too?
chrome.tabs.onUpdated.addListener(function(tabId, changeInfo, tab){
  var now = new Date();
  var action = "tabs.onUpdated";
  var value = tabId;
  changeInfo.tabId = tabId;
  changeInfo.tab = tab;

  // 
  if(('status' in changeInfo && changeInfo.status == 'complete')&& !('url' in changeInfo)){

    chrome.tabs.get(changeInfo.tabId, function(tab){
      Url = (tab.hasOwnProperty('url')?tab.url:"");
      title = (tab.hasOwnProperty('title')?tab.title:"");
      active = tab.active;
      tabId = (tab.hasOwnProperty('id')?tab.id:-1);
      windowId = tab.windowId;

      chrome.tabs.executeScript(
        tabId,
        { code: "document.referrer;" },
        function(result) {
          changeInfo.referrerInfo = result;

          saveAction("tabs.onUpdated",value,changeInfo,now);
          // savePQ(Url,title,active,tabId,windowId,now);
        }
      );

      
    });

  }

  
  
});

// chrome.tabs.onZoomChange.addListener(function(ZoomChangeInfo){
//   var now = new Date();
//   chrome.tabs.get(ZoomChangeInfo.tabId, function(tab){
//     ZoomChangeInfo.windowId = windowId;
//     saveAction("tabs.onZoomChange",ZoomChangeInfo.oldZoomFactor + "," + ZoomChangeInfo.newZoomFactor,ZoomChangeInfo,now);
//   });
  
// });




// TODO: Any tab IDs I should record here?
// TODO: Any highlighted/change actions in addition that are typically fired?
chrome.windows.onCreated.addListener(function(windowInfo){
 var now = new Date();
 saveAction("windows.onCreated",windowInfo.id,windowInfo,now);              
});



// TODO: Any tab IDs I should record here?
// TODO: Any highlighted/change actions in addition that are typically fired?
chrome.windows.onRemoved.addListener(function(windowId){
 var now = new Date();
 saveAction("windows.onRemoved",windowId,{windowId:windowId},now);
});



// TODO: get currently active tab ID
// TODO: Any highlighted/change actions in addition that are typically fired?
// TODO: Why is the windowID sometimes -1? Is that when focus is going away from Chrome?  Might be useful...
chrome.windows.onFocusChanged.addListener(function(windowId){
  var now = new Date();
  saveAction("windows.onFocusChanged",windowId,{windowId:windowId},now);
});


// TODO: Multiple calls per page sometimes?
chrome.webNavigation.onCommitted.addListener(function(details){
  var now = new Date();
  if (details.transitionType.indexOf('auto') == -1){
    chrome.tabs.get(details.tabId, function(tab){
    Url = (tab.hasOwnProperty('url')?tab.url:"");
    title = (tab.hasOwnProperty('title')?tab.title:"");
    active = tab.active;
    tabId = (tab.hasOwnProperty('id')?tab.id:-1);
    windowId = tab.windowId;
    details.tab = tab;

    chrome.tabs.executeScript(
        tabId,
        { code: "document.referrer;" },
        function(result) {
          details.referrerInfo = result;
          saveAction("webNavigation.onCommitted",details.tabId,details,now);
          savePQ(Url,title,active,tabId,windowId,now);
        }
      );
    
  });
  }
  
});

// TODO: Can't use?
// //chrome.omnibox.onInputEntered.addListener(function(text, disposition){
// //                                          var now = new Date();
// //                                          var action = "onmibox.onInputEntered";
// //                                          var value = text;
// ////                                          alert(text);
// //                                          
// //                                          
// //                                          var data = {
// //                                          action:action,
// //                                          value:value
// //                                          };
// //                                          saveAction(data,now);
// //                                          
// //                                          });



// TODO: Any highlighted/change actions in addition that are typically fired?
// TODO: Record in tandem with webNavigation.onCommitted
// TODO: On Facebook, onVisited updates itself several times.
// TODO: Shows stats like visit count.  Not given in other items...
// chrome.history.onVisited.addListener(function(historyItem){
//   var now = new Date();
//   var action = "history.onVisited";
//   var value = historyItem.id;
//   var data = {
//     action:action,
//     value:value,
//     actionJSON:JSON.stringify(historyItem)
//   };
//   alert(JSON.stringify(historyItem));
//   saveAction(data,now);
//   // data = {
//   //   URL: historyItem.url,
//   //   title: historyItem.title
//   // }
//   // now = new Date();
//   // savePQ(data,now);
// });




//chrome.history.onVisited.addListener(function(historyItem){
//    // Check if credentials are set and verified in sync storage
//    
//                                     $.ajax({
//                                            url: "http://coagmento.org/EOPN/services/savePQ.php",
//                                            //              url: "http://peopleanalytics.org/ExplorationStudy/api/record.php",
//                                            method : "get",
//                                            data : {
//                                            
//                                            //                password: resp.password,
//                                            URL: historyItem.url,
//                                            title: historyItem.title,
//                                            localTimestamp: new Date().getTime()
//                                            },
//                                            dataType: "text",
//                                            success : function(resp){
//                                            //                                              alert("SAVED!"+resp);
//                                            
//                                            },
//                                            error: function(resp){
//                                            //                                              alert("FAILED!"+resp);
//                                            }
//                                            });
//                                     }
//                                     
//    
//                                $.ajax({
//                                            url: "http://coagmento.org/EOPN/services/getUsername.php",
//                                            method : "get",
//                                            data : {},
//                                            dataType: "text",
//                                            success : function(resp){
//                                       
//                                       username=resp;
//                                       
//                                       
//                                       
//                                       if (username == "") {
//                                       if(historyItem.url != "http://coagmento.org/EOPN/index.php"){
////                                       openOptions();
//                                       }
//                                       
//                                       //        if (resp.username == "" || resp.password == "") {
//                                       //            openOptions();
//                                       } else {
//                                       // Send ajax request
//                                       
//                                       $.ajax({
//                                              url: "http://coagmento.org/EOPN/services/savePQ.php",
//                                              //              url: "http://peopleanalytics.org/ExplorationStudy/api/record.php",
//                                              method : "get",
//                                              data : {
//                                              
//                                              //                password: resp.password,
//                                              URL: historyItem.url,
//                                              title: historyItem.title,
//                                              localTimestamp: new Date().getTime()
//                                              },
//                                              dataType: "text",
//                                              success : function(resp){
////                                              alert("SAVED!"+resp);
//                                              
//                                              },
//                                              error: function(resp){
////                                              alert("FAILED!"+resp);
//                                              }
//                                              });
//                                       }
//                                       
//                                       
//                                       
//                                            },
//                                            error: function(resp){
//                                            callback.call(window, "Unknown error has occured", "error");
//                                            }
//                                            });
//                                     
//
//                                     
//                                     
//                                     });

