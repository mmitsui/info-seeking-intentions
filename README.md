# Characterizing and Evaluating Whole Session Interactive Information Retrieval
The following repository contains code for the data collection tools of the project "Characterizing and Evaluating Whole Session Interactive Information Retrieval" (http://inforetrieval.org/iir/).  This repository contains the client-server data collection code for user studies, which collects and stores browsing behavior of users during web search.  For a description of the analysis code, see [here](https://github.com/mmitsui/information-seeking-intentions).

# Table of Contents
1. [About the Project](#about-the-project)
2. [User Studies](#user-studies)
	1. [Information Seeking Intentions](#information-seeking-intentions)
	2. [Search Intentions in Natural Settings](#search-intentions-in-natural-settings)
	3. [Study 3](#study-3)
3. [Project Components](#project-components)
4. [Setting Up The Software](#setting-up-the-software)
	1. [Requirements](#requirements)
	2. [Server-side Code](#server-side-code)
	3. [Client-side Code](#client-side-code)
5. [Data Analysis Code](#data-analysis-code)
6. [Papers from this Project](#papers-from-this-project)


# About the Project
This research addresses a newly important issue in contemporary life. As people become more accustomed to using the Web for finding information, they are increasingly using it for addressing ever more complex and personally important information problems. However, current Web search engines have been developed and specifically tuned to helping people find simple, mostly factual information, usually as a single response list to a single, simple query. But when they try to address the new types of problems, people need to engage in longer information seeking episodes than the one query-one response paradigm assumes. They may also need to engage in many activities other than just clicking on a search result, such as reading, evaluating, comparing and using information. Current Web search engines do not sufficiently support this model of information seeking and use. This research addresses this problem by studying why people engage in such complex information seeking (that is, the reasons that motivate them to do this), and what they try to accomplish during the course of an information seeking episode (their search intentions). The end-goal of this research is to design and evaluate new types of search engines for supporting people in accomplishing the goals that have led them to engage in information seeking. This means, in essence, being able to personalize system support to the individual, and the individual's goals and context. Specifically, this research will establish relationships between people's behaviors during an information seeking episode, the motivating goals that led them to engage in information seeking, and their specific intentions at any point in an information seeking episode. This will enable development of systems that will be able to predict how best to support the individual person in addressing their information problem. For example, the findings from this project could help build a system that automatically identifies that a searcher is shopping for a car, and help him/her compare cost-benefits of new vs. used cars, buying vs. leasing, and eventually making an informed decision. Research will be integrated with educational activities via developing modules to supplement courses in iSchools and library/information science programs, etc. This is important, since a broad range of students would learn about new methods of searching and related user studies and evaluation.

# User Studies
The project is broken up into 3 user studies.

## Information Seeking Intentions

In this study, we collected data about individuals' information-seeking behavior on journalism-based tasks.  Participants were brought to a laboratory and asked to conduct 2 20-minute information seeking tasks on a laboratory computer.  After each task, participants were asked to review their activity (shown a video for review) and to comment on their intentions.  What were they trying to accomplish at each query?  Were they trying to identify specific information, find a specific link, or evaluate the best item among a set of items? The accompanying browser extension recorded participants' search activity while they browsed through web pages and conducted each part of the study.

More details about this study can be found [here](https://github.com/mmitsui/info-seeking-intentions/tree/master/src/collection-tools/fall2015)

## Search Intentions in Natural Settings

In this study, we collected data about individuals' information-seeking behavior in the workplace.  In contrast with the previous study, this was a live study.  Participants were not brought to a laboratory; they installed a plugin on their workplace machine computer.  The extension passively collected data throughout several days, and participants used the accompanying interface to annotate their search activity. Participants identified sessions in their search activity, assigned tasks to each session, indicated the query segments within each session, the search intentions of each segment, and whether the sessions were successful or useful.

More details about this study can be found [here](https://github.com/mmitsui/info-seeking-intentions/tree/master/src/collection-tools/spring2017).

## Study 3

TBD.

# Project Components

The code is broken into the following sections:
* [Collection Tools - Server Code](https://github.com/mmitsui/info-seeking-intentions/tree/master/src/collection-tools) - Server-side code for all user studies run for this project.  Code is mainly written in PHP.
	* [Fall 2015 Study](https://github.com/mmitsui/info-seeking-intentions/tree/master/src/collection-tools/fall2015/spring2016intent) - Server-side code for the [Fall 2015 study](#fall-2015-study).
	* [Spring 2017 Study](https://github.com/mmitsui/info-seeking-intentions/tree/master/src/collection-tools/spring2017/workintent) - Server-side code for the [Spring 2017 study](#spring-2017-study).
* [Plugins - Client Code](https://github.com/mmitsui/info-seeking-intentions/tree/master/src/plugins) - Client-side code installed on participants' machines for each user study.
	* [Fall 2015 Study - Firefox](https://github.com/mmitsui/info-seeking-intentions/tree/master/src/plugins/fall2015/firefox) - Browser extension installed on the lab machine for the [Fall 2015 study](#fall-2015-study). This is a Firefox extension.
	* [Spring 2017 Study - Chrome](https://github.com/mmitsui/info-seeking-intentions/tree/master/src/plugins/spring2017/chrome) - Browser extension installed on the participants' remote machines for the [Spring 2017 study](#spring-2017-study). This is a Chrome extension.
* [Analysis Code] - TBD.  This will contain all analysis code from the project.

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

Currently, this repository only contains code for [here](https://github.com/mmitsui/information-seeking-intentions).

# Papers from this Project

Several papers have been written based on the studies conducted with this software! You can check them out at http://inforetrieval.org/iir/.

# Contact Us

For any questions, please Soumik Mandal soumik.mandal@rutgers.edu at or Matthew Mitsui at mmitsui@scarletmail.rutgers.edu.
