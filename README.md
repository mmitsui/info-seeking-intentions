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

In this study, we collected data about individuals' information-seeking behavior around journalism-based tasks.  Participants were brought to the lab and asked to conduct 2 information seeking tasks, 20 minutes each.  After each task, participants were asked to look back at their activity (shown a video for review) and to comment on their intentions.  What were they trying to accomplish at each query?  Were they trying to identify specific information, find a specific link, or evaluate the best item among a set of items? The accompanying browser extension recorded participants' search activity while they browsed through web pages and conducted each part of the study.

## Search Intentions in Natural Settings

In this study, we collected data about individuals' information-seeking behavior in the workplace.  In contrast with the previous study, this was a live study.  Participants were not brought to the lab but were rather provided a plugin to install at their work place.  The plugin passively collected data throughout several days, and participants used the accompanying interface to annotate their search activity. Participants annotated things such as the sessions of their log, the underlying tasks of each session, and what they were trying to accomplish in each search segment (their intentions).

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

To install this software, simply put upload it to server. WARNING: At the time running this software, our version of PHP was PHP 5.3.3. Future versions of PHP have several modifications to the core API.  Proceed with caution - depending on your version of PHP, some updates may be necessary.  Connection.class.php is also missing in each project - it is a wrapper to MySQL calls and is easy to reverse engineer from the API calls.

Configuring the code may be the most time-consuming part.  You will need to search for references to our `coagmento.org` (e.g. `...coagmento.org/workintent/signup_intro.php...`) and change them to yours (e.g. `...yourserver.com/workintent/signup_intro.php`). Each folder also contains the structure of the MySQL databases as a SQL dump in the `mysql-skeleton` folder.

## Client-side Code

Installing these plugins is a drag-and-drop affair.  For the Firefox extension, select the `chrome` folder, `chrome.manifest` and `install.rdf` and compress them into a ZIP.  Rename your `archive.zip` to `archive.xpi`.  Then drag it into Firefox, and you'll be prompted to install and restart.  WARNING: This was created before Firefox version 42.  Afterwards, this version of the extension API became deprecated in favor of several other APIs (and eventually the Web Extensions API).  To get this software to work out of the box, you'll need to downgrade your Firefox version to 42.0 and prevent automatic updates.  Otherwise, we suggest using the Web Extensions API to create an updated version of the software.  For the Chrome extension, enter `chrome://extensions/` into your browser.  Click "Load unpacked extension...".  Then navigate to and select the `chrome` folder.  Any updates/modifications you make to this folder can be tested by reloading the extension in the `chrome://extensions` tab.

To configure the software, change all references to `coagmento.org` to your own server.  In the Firefox extension, you can find these references in `chrome/content/coagmento.js`,`chrome/content/coagmento.xul`. In the Chrome extension, you can find these references in `manifest.json`,`background.js`,`payload.js`, and `popup.js`.


# Data Analysis Code

Currently, this repository only contains code for [here](https://github.com/mmitsui/information-seeking-intentions).

# Papers from this Project

Several papers have been written based on the studies conducted with this software! You can check them out at http://inforetrieval.org/iir/.

# Contact Us

For any questions, please contact us at TBD.
