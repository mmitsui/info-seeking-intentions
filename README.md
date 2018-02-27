# Characterizing and Evaluating Whole Session Interactive Information Retrieval
The following repository is code from the Information Seeking Intentions study (http://inforetrieval.org/iir/)

# Table of Contents
1. [About the Project](#about-the-project)
2. [User Studies](#user-studies)
	1. [Fall 2015 Study](#fall-2015-study)
	2. [Spring 2017 Study](#spring-2017-study)
3. [Project Components](#project-components)
4. [Setting Up The Software](#setting-up-the-software)
	1. [Server-side Code](#server-side-code)
	2. [Client-side Code](#client-side-code)
	2. [Downloading and Initializing Data](#toc_6)
		1. [Insert Demo Data](#toc_7)
		2. [Insert Full Data](#toc_8)
3. [Project Products](#project-products)
4. [Testing the Front End](#toc_10)
5. [TODO](#toc_11)

# About the Project
This research addresses a newly important issue in contemporary life. As people become more accustomed to using the Web for finding information, they are increasingly using it for addressing ever more complex and personally important information problems. However, current Web search engines have been developed and specifically tuned to helping people find simple, mostly factual information, usually as a single response list to a single, simple query. But when they try to address the new types of problems, people need to engage in longer information seeking episodes than the one query-one response paradigm assumes. They may also need to engage in many activities other than just clicking on a search result, such as reading, evaluating, comparing and using information. Current Web search engines do not sufficiently support this model of information seeking and use. This research addresses this problem by studying why people engage in such complex information seeking (that is, the reasons that motivate them to do this), and what they try to accomplish during the course of an information seeking episode (their search intentions). The end-goal of this research is to design and evaluate new types of search engines for supporting people in accomplishing the goals that have led them to engage in information seeking. This means, in essence, being able to personalize system support to the individual, and the individual's goals and context. Specifically, this research will establish relationships between people's behaviors during an information seeking episode, the motivating goals that led them to engage in information seeking, and their specific intentions at any point in an information seeking episode. This will enable development of systems that will be able to predict how best to support the individual person in addressing their information problem. For example, the findings from this project could help build a system that automatically identifies that a searcher is shopping for a car, and help him/her compare cost-benefits of new vs. used cars, buying vs. leasing, and eventually making an informed decision. Research will be integrated with educational activities via developing modules to supplement courses in iSchools and library/information science programs, etc. This is important, since a broad range of students would learn about new methods of searching and related user studies and evaluation.

# User Studies
The project is broken up into 2 user studies.

## Fall 2015 Study

In this study, we collected data about individuals' information-seeking behavior around journalism-based tasks.  Participants were brought to the lab and asked to conduct 2 information seeking tasks, 20 minutes each.  After each task, participants were asked to look back at their activity (shown a video for review) and to comment on their intentions.  What were they trying to accomplish at each query?  Were they trying to identify specific information, find a specific link, or evaluate the best item among a set of items? The accompanying browser extension recorded participants' search activity while they browsed through web pages and conducted each part of the study.

## Spring 2017 Study

In this study, we collected data about individuals' information-seeking behavior in the workplace.  In contrast with the previous study, this was a live study.  Participants were not brought to the lab but were rather provided a plugin to install at their work place.  The plugin passively collected data throughout several days, and participants used the accompanying interface to annotate their search activity. Participants annotated things such as the sessions of their log, the underlying tasks of each session, and what they were trying to accomplish in each search segment (their intentions).

# Project Components

The code is broken into the following sections:
* [Collection Tools - Server Code](https://github.com/mmitsui/info-seeking-intentions/tree/master/src/collection-tools) - Server-side code for all user studies run for this project.  Code is mainly written in PHP.
	* [Fall 2015 Study](https://github.com/mmitsui/info-seeking-intentions/tree/master/src/collection-tools/fall2015/spring2016intent) - Server-side code for the [Fall 2015 study](#fall-2015-study).
	* [ Spring 2017 Study](https://github.com/mmitsui/info-seeking-intentions/tree/master/src/collection-tools/spring2017/workintent) - Server-side code for the [Spring 2017 study](#spring-2017-study).
* [Plugins - Client Code](https://github.com/mmitsui/info-seeking-intentions/tree/master/src/plugins) - Client-side code installed on participants' machines for each user study.
	* [Fall 2015 Study - Firefox](https://github.com/mmitsui/info-seeking-intentions/tree/master/src/plugins/fall2015/firefox) - Browser extension installed on the lab machine for the [Fall 2015 study](#fall-2015-study). This is a Firefox extension.
	* [Spring 2017 Study - Chrome](https://github.com/mmitsui/info-seeking-intentions/tree/master/src/plugins/spring2017/chrome) - Browser extension installed on the participants' remote machines for the [Spring 2017 study](#spring-2017-study). This is a Chrome extension.
* [Analysis Code] - TBD.  This will contain all analysis code from the project.

# Setting Up The Software
## Server-side Code

To install. PHP 5.3.3

To configure
## Client-side Code
To install.

To configure.

# Project Products

Several papers have been written based on the studies conducted with this software! You can check them out at http://inforetrieval.org/iir/.
