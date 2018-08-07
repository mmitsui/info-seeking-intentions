# Information Seeking Intentions Study
This README contains documentation about the code for the *Information Seeking Intentions* study.  It begins with an outline of the design of the study tools - the structure and flow of conducting the study. It then discusses the folder structure of the code.  It concludes with an overview of the MySQL data collected in this study.

# Table of Contents
1. [Study Design Outline](#study-design-outline)
2. [Code Structure](#code-structure)
  1. [Server Code Structure](#server-code-structure)
  2. [Extension Code Structure](#extension-code-structure)
3. [MySQL Structure](#mysql-structure)

# Study Design Outline

* What is a stage?  What are the stages?

# Code Structure


  

## Server Code Structure

The code is organized into several folders.  Below is a brief description of each folder.
  * `core` - Core classes used to track a user's session while conducting the study.  `Base.class.php`, for instance, tracks the user's ID, and `Stage.class.php` manages the flow between stages
  * `data` - Contains video and data files presented to users during the study - for instance tutorial videos.
  * `img` - Images (unused in this study).
  * `instruments` - Contains code for different stages in the study. Each PHP file is a different stage.
  * `lib` - External library code imported throughout the study (e.g. Bootstrap, jQuery).
  * `old_images` - Images (unused in this study).
  * `services` - Code used to interact with the database and push/pull data.  Typically accessed through the tool's web interface or Firefox extension.
  * `sidebar` - Code called from the Firefox extension sidebar to push/pull data.
  * `study_styles` - More external library code imported throughout the study.
  * `webPages` - a folder used to store the HTML and JSON for web pages visited and search queries issued by users during the study.
  * `./*.php` - General frontend functions.  Some of these are administrator web pages used to view users'
  
  


## Extension Code Structure

The following is a description of the oganization of the `chrome` folder of the Firefox extension:

* `chrome/coagmento.js` - Collects live interaction data and pushes it to the server (e.g. page visits, copy/paste behavior).
* `chrome/coagmento.xul` - Structures the toolbar of the extension.
* `chrome/sidebar.xul` - Structures the sidebar of the extension.
* `locale/en-US/sidebar.dtd` - Hot keys for the extension.
* `skin/*` - Images and CSS for visualizing the buttons of the extension toolbar.


# MySQL Structure

All data from the study was stored in MySQL tables.  Data takes the form of user login data, information about the tasks given to users, and interaction data.  Below is a brief overview of the organization of the MySQL tables:
* `actions` - A general log of actions conducted throughout the study.  The type of action is indicated in the `action` column.
* `bookmarks` - Bookmarks recorded by users when searching for information to complete their task.  Bookmarks were recorded through the extension.
* `click_data` - Click activity of users in the web browser.
* `copy_data` - Copy activity of users - e.g., any time they copied text through Ctrl+C.
* `keystroke_data` - A keystroke log.
* `page_viewtimes` - Unused in this study.
* `pages` - A timestamped log of pages visited by users.
* `participant_id_to_task` - 
* `paste_data` - Copy activity of users - e.g., any time they pasted text through Ctrl+V.
* `queries` - A timestamped log of queries issued to search engines by users
* `questionnaire_answers` - Unused in this study.
* `questionnaire_demographic` - Answers to an introductory demographic questionnaire asked to users
* `questionnaire_postsearch` - 
* `questionnaire_pretask` - 
* `questionnaire_questions` - Contains
* `questionnaire_recruitment` - 
* `questions_progress` - 
* `questions_study` - 
* `recruits` - 
* `scroll_data` - 
* `session_progress` - A timestamped log of the progress users made in the study - the times they entered and exited each stage of the study
* `session_stages` - A list of the different stages in the study.  Stages were visited by users in increasing order.  The `stageID` column in other tables refers to which of these stages the user was in.
* `tag_assignments` - Unused in this study.
* `timeline_display_data` - Unused in this study.
* `users` - Login and metadata information about the users
* `video_intent_assignments` - Users' annotations of intentions for each query
* `video_reformulation_history` - 
* `video_save_history` - 
* `video_segments` - 
* `video_unsave_history` - 

# Project Components

The code is broken into the following sections:

* [Collection Tools - Server Code](https://github.com/mmitsui/info-seeking-intentions/tree/master/src/collection-tools) - Server-side code for all user studies run for this project.  Code is written in PHP, HTML, CSS, Bootstrap, and MYSQL.
	* [Information Seeking Intentions](https://github.com/mmitsui/info-seeking-intentions/tree/master/src/collection-tools/fall2015/spring2016intent) - Server-side code for the [Information Seeking Intentions study](#information-seeking-intentions).
	* [Search Intentions in Natural Settings](https://github.com/mmitsui/info-seeking-intentions/tree/master/src/collection-tools/spring2017/workintent) - Server-side code for the [Search Intentions in Natural Settings study](#search-intentions-in-natural-settings).
	* Study 3 - TBD
* [Plugins - Client Code](https://github.com/mmitsui/info-seeking-intentions/tree/master/src/plugins) - Client-side code installed on participants' machines for each user study.
	* [Information Seeking Intentions - Firefox](https://github.com/mmitsui/info-seeking-intentions/tree/master/src/plugins/fall2015/firefox) - Browser extension installed on the lab machine for the [Information Seeking Intentions study](#information-seeking-intentions). This is a Firefox extension.
	* [Search Intentions in Natural Settings - Chrome](https://github.com/mmitsui/info-seeking-intentions/tree/master/src/plugins/spring2017/chrome) - Browser extension installed on the participants' work machines for the [Search Intentions in Natural Settings study](#search-intentions-in-natural-settings). This is a Chrome extension.
	* Study 3 - TBD

# Setting Up The Software

## Requirements

You will need the following software on your client and server to get started:

* Information Seeking Intentions
	* Server
		* PHP (ver. 5.3.3)
		* MySQL
	* Client
		* Firefox (42.0 or lower)
* Search Intentions in Natural Settings
	* Server
		* PHP (ver. 5.3.3)
		* MySQL
	* Client
		* Chrome Browser

* Study 3
	* TBD

## Server-side Code

We do not provide instructions here for configuring your own HTTP server.  For instructions on that, please consult your local IT professional.

Once you have configured your HTTP server, to install this software simply put upload it to server. You must then search for references to `coagmento.org` (e.g. `http://coagmento.org/workintent/signup_intro.php.`) and replace them with your server (e.g. `http://yourserver.com/workintent/signup_intro.php`). Each folder also contains the structure of the MySQL databases as a SQL dump in the `mysql-skeleton` folder.

WARNING: At the time running this software, our version of PHP was PHP 5.3.3. Future versions of PHP have several modifications to the core API.  Cepending on your version of PHP update to this code may be necessary.  

Connection.class.php is also missing in each project - it is a wrapper to MySQL calls and will contain your MySQL server credentials, but it is easy to recreate.

## Client-side Code

For Firefox extensions:
	* Select the `chrome` folder, `chrome.manifest` and `install.rdf` and compress them into a ZIP.  
	* Rename your `*.zip` archive to a `*.xpi` archive.  This will convert it into a .xpi archive.
	* Drag it into Firefox.  Your Firefox browser will prompt you to install the extension and restart.  
	* WARNING: Firefox extensions were created before Firefox version 42.  This version of the extension API has since become deprecated and will not work on future versions of Firefox.  You'll need to downgrade your Firefox version to 42.0 and prevent automatic updates.  If you don't want to do this, we suggest converting the extension to use the most recent API (currently the [WebExtensions API](https://developer.mozilla.org/en-US/docs/Mozilla/Add-ons/WebExtensions)).  
	
For Chrome Extensions:
	* In your Chrome browser, navigate to `chrome://extensions/`.  
	* Select "LOAD UNPACKED" near the top of your window.
	* When prompted to select a file, navigate to and select the `chrome` folder of your extension.  
	* Note: Any updates/modifications you make to this folder can be tested by reloading the extension through the `chrome://extensions` tab.

Configuring Extensions:
	* Change all references to `coagmento.org` to your own server.  
	* Firefox extensions: these references are in `chrome/content/coagmento.js` and `chrome/content/coagmento.xul`. 
	* Chrome extensions: these references are in `manifest.json`,`background.js`,`payload.js`, and `popup.js`.


# Data Analysis Code

Currently, this repository only contains code for collecting behavioral data and storing it in a MySQL server. To view code we used to analyze the data, you may look [here](https://github.com/mmitsui/information-seeking-intentions).

# Papers from this Project

Several papers have been written based on the studies conducted with this software! You can check them out at http://inforetrieval.org/iir/.

# Contact Us

For any questions, please Soumik Mandal soumik.mandal@rutgers.edu at or Matthew Mitsui at mmitsui@scarletmail.rutgers.edu.
