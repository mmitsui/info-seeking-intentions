$(document).ready(function(){

    
    var signedinYesID = '#signedin_yes';
    var signedinNoID = '#signedin_no';
    var firstNameID = '#first_name';
    var lastNameID = '#last_name';
    var loginErrorTextID = '#login_error_text';
    var usernameInputID = '#username';
    var passwordInputID = '#password';
    var homeDir = "http://coagmento.org/workintent";
    
    var registerUrl = homeDir+"/signup_intro.php";
    var checkLoggedInUrl = homeDir + "/getLoggedIn.php";
    var loginUrl = homeDir + "/login.php";
    var logoutUrl = homeDir + "/logout.php";
    var sendCredentialsUrl = homeDir + "/sendCredentials.php";
    var homeUrl = homeDir + "/instruments/getHome.php";
    var contactUrl = "mailto:mmitsui@scarletmail.rutgers.edu?Subject=Intent%20Study%20Inquiry";

    function sendContactEmail(){
        chrome.tabs.create({url:contactUrl}, function(tab){
            setTimeout(function(){
                chrome.tabs.remove(tab.id);
            },500);
        },
        );
    }

    function goToRegistration(){
        chrome.tabs.create({url:registerUrl}, function(tab){
        },
        );
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

    function handleLoggedIn(msg){
        msg = JSON.parse(msg);
        if(msg.loggedin){
            renderLoggedIn(msg.loggedin);
            $(signedinYesID).show();
            $(signedinNoID).hide();
        }else{
            $(signedinNoID).show();
            $(signedinYesID).hide();
        }
    }

    $.ajax({
        type: "POST",
        url: checkLoggedInUrl,
        data : {},
        dataType: "text",
        success: handleLoggedIn,
        error: function(msg){
            $(signedinNoID).show();
            $(signedinYesID).hide();
            renderLoggedIn(false);
        }
    });

    $( "#login_button" ).click(function() {
    	renderLoggedIn(true);
        $.ajax({
            type: "POST",
            url: loginUrl,
            data:{username:$(usernameInputID).val(),password:$(passwordInputID).val()},
            success: function(msg){
                msg = JSON.parse(msg);
                if(msg.success){
                    var firstName = msg.firstName;
                    var lastName = msg.lastName;
                    $(firstNameID).text(firstName);
                    $(lastNameID).text(lastName);
                    $(signedinNoID).hide();
                    $(signedinYesID).show();
                    $(loginErrorTextID).text('');
                    renderLoggedIn(true);
                }else{
                	$(signedinNoID).show();
                    $(signedinYesID).hide();
                	$(loginErrorTextID).text(msg.errortext);
                	renderLoggedIn(false);
                }
            },
        });
    });

    $( "#logout_button" ).click(function() {
        $.ajax({
            type: "POST",
            url: logoutUrl,
            data:{},
            success: function(msg){
                msg = JSON.parse(msg);
                if(msg.success){
                    $(signedinNoID).show();
                    $(signedinYesID).hide();
                    renderLoggedIn(false);
                }

            },
        });
    });


    $( "#credentials_button" ).click(function() {
        $.ajax({
            type: "POST",
            url: sendCredentialsUrl,
            data:{username:$(usernameInputID).val()},
            success: function(msg){

                msg = JSON.parse(msg);
                if(msg.success){
                    $(loginErrorTextID).text('E-mail sent!  Please check your inbox.');
                }else{
                	$(loginErrorTextID).text(msg.errortext);
                }
            },
        });
    });


    $( "#contact_us_signedin_link" ).click(function() {
        sendContactEmail();
    });

    $( "#contact_us_signedout_link" ).click(function() {
        sendContactEmail();
    });

    $( "#register_link" ).click(function() {
        goToRegistration();
    });

    function goHome(){
        chrome.tabs.create({url:homeUrl}, function(tab){
        },
        );
    }

    $( "#homepage_button" ).click(function() {
        goHome();
    });

});
