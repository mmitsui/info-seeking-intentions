var device = "chrome";
var domain = 'http://coagmento.org/EOPN';
var homeUrl = domain + '/index.php';
var actionSaveUrl = domain + '/instruments/insertAction.php';

var contactUrl = "mailto:mmitsui@scarletmail.rutgers.edu?Subject=Intent%20Study%20Inquiry";


function sendContactEmail(){
    alert("yes");
    chrome.tabs.create({url:contactUrl}, function(tab){
                       setTimeout(function(){
                                  chrome.tabs.remove(tab.id);
                                  },500);
                       },
                       );
    
}

//var serp_storage_url = domain + '/saveserp';
//var check_userid_url = domain + '/users/checkid';



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
function saveAction(data,now){
    data.localDate = now.getFullYear() + "-" + ("0" + (now.getMonth() + 1)).slice(-2) + "-" + ("0" + now.getDate()).slice(-2);
    data.localTime =  ("0" + now.getHours()).slice(-2) + ":" + ("0" + now.getMinutes()).slice(-2) + ":" + ("0" + now.getSeconds()).slice(-2);
    data.localTimestamp = now.getTime();

    
    
    $.ajax({
           url: actionSaveUrl,
           method : "get",
           data : data,
           dataType: "text",
           success : function(resp){
           
           },
           error: function(resp){
           
           }
           });
    
}


chrome.tabs.onActivated.addListener(function(activeInfo){
                                    var now = new Date();
                                    var action = "tabs.onActivated";
                                    var Url = "";
                                    var tabId = activeInfo.tabId;
                                    var windowId = activeInfo.windowId;
                                    var value = tabId;
                                    
                                    
                                    var data = {
                                        action:action,
                                        value:tabId
                                    };
                                    
                                
                                    
                                    
                                    chrome.tabs.get(activeInfo.tabId, function(tab){
                                                    Url = tab.url;
                                                    });
                                    
                                    
                                    
                                    saveAction(data,now);
                                    
                                    });



chrome.tabs.onAttached.addListener(function(tabId, attachInfo){
                                   
                                   var now = new Date();
                                   var action = "tabs.onAttached";
                                   var value = tabId;
                                   var newWindowId = attachInfo.newWindowId;
                                   var newPosition = attachInfo.newPosition;
                                   
                                   
                                   var data = {
                                    action:action,
                                    value:value
                                   };
                                   
                                   saveAction(data,now);
                                   });
chrome.tabs.onCreated.addListener(function(tab){
                                  var now = new Date();
                                  var action = "tabs.onCreated";
                                  var value = tab.id;
                                  var windowId = tab.windowId;
                                  var index = tab.index;
                                  
                                  var data = {
                                    action:action,
                                    value:value
                                  };
                                  saveAction(data,now);
                                  });
chrome.tabs.onDetached.addListener(function(tabId, detachInfo){
                                   var now = new Date();
                                   var action = "tabs.onDetached";
                                   var value = tabId;
                                   var oldWindowId = detachInfo.oldWindowId;
                                   var oldPosition = detachInfo.oldPosition;
                                   
                                   var data = {
                                    action:action,
                                    value:value
                                   };
                                   saveAction(data,now);
                                   });
chrome.tabs.onHighlighted.addListener(function(highlightInfo){
                                      var now = new Date();
                                      var action = "tabs.onHighlighted";
                                      var windowId = highlightInfo.windowId;
                                      var tabIds = highlightInfo.tabIds;
                                      var value = tabIds.join();
                                      
                                      
                                      
                                      var data = {
                                        action:action,
                                        value:value
                                      };
                                      saveAction(data,now);
                                      });
chrome.tabs.onMoved.addListener(function(tabId, moveInfo){
                                var now = new Date();
                                var action = "tabs.onMoved";
                                var windowId = moveInfo.windowId;
                                var fromIndex = moveInfo.fromIndex;
                                var toIndex = moveInfo.toIndex;
                                var value = tabId;
                                
                                var data = {
                                    action:action,
                                    value:value
                                };
                                saveAction(data,now);
                                });

chrome.tabs.onRemoved.addListener(function(tabId, removeInfo){
                                  var now = new Date();
                                  var action = "tabs.onRemoved";
                                  var value = tabId;
                                  var windowId = removeInfo.windowId;
                                  var isWindowClosing = removeInfo.isWindowClosing;
                                  
                                  var data = {
                                    action:action,
                                    value:value
                                  };
                                  saveAction(data,now);
                                  });
chrome.tabs.onReplaced.addListener(function(addedTabId, removedTabId){
                                   var now = new Date();
                                   var action = "tabs.onReplaced";
                                   var value = addedTabId;
                                   var removedTab = removedTabId;
                                   
                                   var data = {
                                    action:action,
                                    value:value
                                   };
                                   saveAction(data,now);
                                   });
chrome.tabs.onUpdated.addListener(function(tabId, changeInfo, tab){
                                  var now = new Date();
                                  var action = "tabs.onUpdated";
                                  var value = tabId;
                                  var status = changeInfo.status;
                                  var url = changeInfo.url;
                                  var pinned = changeInfo.pinned;
                                  var audible = changeInfo.audible;
                                  var discarded = changeInfo.discarded;
                                  var autoDiscardable = changeInfo.autoDiscardable;
                                  var mutedInfo = changeInfo.mutedInfo;
                                  var favIconUrl = changeInfo.favIconUrl;
                                  var title = changeInfo.title;
                                  var tab = tab;
                                  
                                  
                                  var data = {
                                    action:action,
                                    value:value
                                  };
                                  saveAction(data,now);
                                  });

chrome.tabs.onZoomChange.addListener(function(ZoomChangeInfo){
                                  var now = new Date();
                                  var action = "tabs.onZoomChange";
                                  var tabId = ZoomChangeInfo.tabId
                                  var value = ZoomChangeInfo.oldZoomFactor + "," + ZoomChangeInfo.newZoomFactor;
                                  var zoomSettings = ZoomChangeInfo.zoomSettings;
                                  
                                  
                                  
                                  var data = {
                                  action:action,
                                  value:value
                                  };
                                  saveAction(data,now);
                                  });




chrome.windows.onCreated.addListener(function(window){
                                     var now = new Date();
                                     var action = "windows.onCreated";
                                     
                                     var value = window.id;
                                     
                                     
                                     var data = {
                                     action:action,
                                     value:value
                                     };
                                     saveAction(data,now);
                                     
                                     });
chrome.windows.onRemoved.addListener(function(windowId){
                                     var now = new Date();
                                     var action = "windows.onRemoved";
                                     var value = windowId;
                                     
                                     var data = {
                                     action:action,
                                     value:value
                                     };
                                     saveAction(data,now);
                                     });
chrome.windows.onFocusChanged.addListener(function(windowId){
                                          var now = new Date();
                                          var action = "windows.onFocusChanged";
                                          var value = windowId;
                                          
                                          var data = {
                                          action:action,
                                          value:value
                                          };
                                          saveAction(data,now);
                                          });



chrome.webNavigation.onCommitted.addListener(function(details){
                                             var now = new Date();
                                             var action = "webNavigation.onCommitted";
                                             var value = details.tabId;
                                             var timeStamp = details.timeStamp;
                                             var url = details.url;
//                                             alert(url);
                                             
                                             
                                             var data = {
                                             action:action,
                                             value:value
                                             };
                                             saveAction(data,now);
                                             
                                             });

//chrome.omnibox.onInputEntered.addListener(function(text, disposition){
//                                          var now = new Date();
//                                          var action = "onmibox.onInputEntered";
//                                          var value = text;
////                                          alert(text);
//                                          
//                                          
//                                          var data = {
//                                          action:action,
//                                          value:value
//                                          };
//                                          saveAction(data,now);
//                                          
//                                          });



chrome.history.onVisited.addListener(function(historyItem){
  $.ajax({
    url: "http://coagmento.org/workintent/services/savePQ.php",
    method : "get",
    data : {
      URL: historyItem.url,
      title: historyItem.title,
      localTimestamp: new Date().getTime()
    },
    dataType: "text",
    success : function(resp){

    },
    error: function(resp){

    }
  });
});




//chrome.history.onVisited.addListener(function(historyItem){
//    // console.log("From extension", historyItem);
//    // console.log(historyItem.url);
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

chrome.runtime.onInstalled.addListener(function(details){
//    if(details.reason == "install"){
//        chrome.tabs.create({'url': "http://coagmento.org/EOPN/index.php"} );
//                                       alert(chrome.runtime.id);
////        chrome.tabs.create({'url': "/options.html"} );
//    }
});

