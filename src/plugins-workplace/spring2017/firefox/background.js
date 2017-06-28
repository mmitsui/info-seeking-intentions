var timerLock = false; // Prevent multiple options pages from opening.

function openOptions() {
    // If not, open up options page if it isn't open.
    var query = {
    url: chrome.runtime.getURL("http://coagmento.org/EOPN/index.php")
        //        url: chrome.runtime.getURL("/options.html")
    };
    browser.tabs.query(query, function(tabs) {
                      if (!timerLock && tabs.length == 0) {
                      timerLock = true;
                      setTimeout(function() {timerLock = false;}, 1000);
                      browser.tabs.create({'url': "http://coagmento.org/EOPN/index.php"} );
                      //            browser.tabs.create({'url': "/options.html"} );
                      }
                      });
}
function saveAction(data,now){
    data.localDate = now.getFullYear() + "-" + ("0" + (now.getMonth() + 1)).slice(-2) + "-" + ("0" + now.getDate()).slice(-2);
    data.localTime =  ("0" + now.getHours()).slice(-2) + ":" + ("0" + now.getMinutes()).slice(-2) + ":" + ("0" + now.getSeconds()).slice(-2);
    data.localTimestamp = now.getTime();
    
    
    
    $.ajax({
           url: "http://coagmento.org/EOPN/services/insertAction.php",
           method : "get",
           data : data,
           dataType: "text",
           success : function(resp){
           
           },
           error: function(resp){
           
           }
           });
    
}


browser.tabs.onActivated.addListener(function(activeInfo){
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
                                    
                                    
                                    
                                    
                                    browser.tabs.get(activeInfo.tabId, function(tab){
                                                    Url = tab.url;
                                                    });
                                    
                                    
                                    
                                    saveAction(data,now);
                                    
                                    });



browser.tabs.onAttached.addListener(function(tabId, attachInfo){
                                   
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
browser.tabs.onCreated.addListener(function(tab){
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
//browser.tabs.onDetached.addListener(function(){
//                                   var now = new Date();
//                                   var action = "tabs.onDetached";
//
//                                   var data = {
//                                   action:action,
//                                   value:value
//                                   };
//                                   saveAction(data,now);
//                                   });
//browser.tabs.onHighlighted.addListener(function(){
//                                      var now = new Date();
//                                      var action = "tabs.onHighlighted";
//
//                                      var data = {
//                                      action:action,
//                                      value:value
//                                      };
//                                      saveAction(data,now);
//                                      });
//browser.tabs.onMoved.addListener(function(){
//                                var now = new Date();
//                                var action = "tabs.onMoved";
//
//                                var data = {
//                                action:action,
//                                value:value
//                                };
//                                saveAction(data,now);
//                                });
//browser.tabs.onRemoved.addListener(function(){
//                                  var now = new Date();
//                                  var action = "tabs.onRemoved";
//
//                                  var data = {
//                                  action:action,
//                                  value:value
//                                  };
//                                  saveAction(data,now);
//                                  });
//browser.tabs.onReplaced.addListener(function(){
//                                   var now = new Date();
//                                   var action = "tabs.onReplaced";
//
//                                   var data = {
//                                   action:action,
//                                   value:value
//                                   };
//                                   saveAction(data,now);
//                                   });
//browser.tabs.onUpdated.addListener(function(){
//                                  var now = new Date();
//                                  var action = "tabs.onUpdated";
//
//                                  var data = {
//                                  action:action,
//                                  value:value
//                                  };
//                                  saveAction(data,now);
//                                  });
//
//
//
//
//chrome.windows.onCreated.addListener(function(){
//                                     var now = new Date();
//                                     var action = "windows.onCreated";
//
//                                     var data = {
//                                     action:action,
//                                     value:value
//                                     };
//                                     });
//chrome.windows.onRemoved.addListener(function(){
//                                     var now = new Date();
//                                     var action = "windows.onRemoved";
//
//                                     var data = {
//                                     action:action,
//                                     value:value
//                                     };
//                                     saveAction(data,now);
//                                     });
//chrome.windows.onFocusChanged.addListener(function(){
//                                          var now = new Date();
//                                          var action = "windows.onFocusChanged";
//
//                                          var data = {
//                                          action:action,
//                                          value:value
//                                          };
//                                          saveAction(data,now);
//                                          });
//
//
//
//chrome.webNavigation.onCommitted.addListener(function(){
//                                             var now = new Date();
//                                             var action = "webNavigation.onCommitted";
//
//
//                                             var data = {
//                                             action:action,
//                                             value:value
//                                             };
//                                             saveAction(data,now);
//
//                                             });
//chrome.omnibox.onInputEntered.addListener(function callback)







browser.history.onVisited.addListener(function(historyItem){
                                     // console.log("From extension", historyItem);
                                     // console.log(historyItem.url);
                                     // Check if credentials are set and verified in sync storage
                                     
                                     
                                     
                                     $.ajax({
                                            url: "http://coagmento.org/EOPN/services/getUsername.php",
                                            method : "get",
                                            data : {},
                                            dataType: "text",
                                            success : function(resp){
                                            
                                            username=resp;
                                            
                                            
                                            
                                            if (username == "") {
                                            if(historyItem.url != "http://coagmento.org/EOPN/index.php"){
                                            //                                       openOptions();
                                            }
                                            
                                            //        if (resp.username == "" || resp.password == "") {
                                            //            openOptions();
                                            } else {
                                            // Send ajax request
                                            
                                            $.ajax({
                                                   url: "http://coagmento.org/EOPN/services/savePQ.php",
                                                   //              url: "http://peopleanalytics.org/ExplorationStudy/api/record.php",
                                                   method : "get",
                                                   data : {
                                                   
                                                   //                password: resp.password,
                                                   URL: historyItem.url,
                                                   title: historyItem.title,
                                                   localTimestamp: new Date()
                                                   },
                                                   dataType: "text",
                                                   success : function(resp){
                                                   //                                              alert("SAVED!"+resp);
                                                   
                                                   },
                                                   error: function(resp){
                                                   //                                              alert("FAILED!"+resp);
                                                   }
                                                   });
                                            }
                                            
                                            
                                            
                                            },
                                            error: function(resp){
                                            callback.call(window, "Unknown error has occured", "error");
                                            }
                                            });
                                     
                                     
                                     
                                     
                                     });

browser.runtime.onInstalled.addListener(function(details){
                                       //    if(details.reason == "install"){
                                       //        browser.tabs.create({'url': "http://coagmento.org/EOPN/index.php"} );
                                       //                                       alert(chrome.runtime.id);
                                       ////        browser.tabs.create({'url': "/options.html"} );
                                       //    }
                                       });

