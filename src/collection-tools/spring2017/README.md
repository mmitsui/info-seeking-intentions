# Search Intentions in Natural Settings

This README contains documentation about the code for the *Search Intentions in Natural Settings* study.  It begins with an outline of the study, the requirements, and the structure and flow of conducting the study. It then discusses the folder structure of the code.  It concludes with an overview of the MySQL data collected in this study.

# Table of Contents
1. [Study Overview](#study-overview)
2. [Requirements](#requirements)
3. [Study Design Outline](#study-design-outline)
	1. [Registration](#registration)
	2. [Conducting The Study](#conducting-the-study)
		1.[Entry Interview](#entry-interview)
		2.[Five Day Annotation](#five-day-annotation)
		3.[Exit Interview](#exit-interview)
4. [Code Structure](#code-structure)
	1. [Server Code Structure](#server-code-structure)
	2. [Extension Code Structure](#extension-code-structure)
5. [MySQL Structure](#mysql-structure)
6. [Contact Us](#contact-us)

# Study Overview

In this study, participants conducted searches for their work in a naturalistic setting - i.e., their work environment - for information relating to different kinds of information search tasks related to their employment. Participants first conducted an initial interview in which they were asked for demographic information, introduced to the study’s software, and asked about regular search tasks. This initial interview lasted about one hour. This was followed by an experimental session. Participants were asked to record their searching activity over the course of five days and to annotate the tasks that they conducted. They were also asked to explain their search intentions at self-selected points during their tasks. This annotation took on average about one hour each day.  Various aspects of their searching behavior were recorded for subsequent analysis. The study concluded with an exit interview, in which participants were asked to analyze their search experiences and to give characterizations of the tasks they performed during the five days. This final exit interview lasted about one hour.

# Requirements

At least two machines are required to conduct this study.  There are no restrictions on the machines other than what is listed below.

* Participant Machine
	* Chrome browser (+ extension)
	* Access to video conferencing tools (e.g. Skype, Zoom, Google Hangouts)
* Researcher Machine
	* A web browser (for managing the participant's progress)
	* Video conferencing tools (at least Skype, Zoom, and Google Hangouts)
	* Audio recording software, to record the interview audio

# Study Design Outline

## Registration

A user's participation in the study begins with registration at `signup_intro.php`. Then, participants provide their consent. Then,  participants register for an entry interview date (e.g. on a Friday), as well as an exit interview required to take place 10 days after the entry interview (e.g. on the Monday 10 days after).  They receive a confirmation e-mail regarding the time of their interviews.

The participant also provides the preferred medium for interviews (e.g. Skype) as well as their username for the interview medium (e.g. Skype username).

## Conducting The Study

The study takes place entirely removely in several steps.  These steps are as follows:

### Entry Interview

The participant and researcher conduct a remote interview at the time specified during registration. At the time of the entry interview, the researcher contacts the participant to conduct the remote interview.

The researcher will proceed with the entry interview, accessible through `userDataEntry.php`.  The researcher begins with the demographic interview (`demographicSurvey.php`). The facilitator then asks the participant to provide as many work tasks as possible and to answer questions about each task.  The questions for each task are given in `tasksSurvey.php`.

When the user has provided all of their tasks or cannot think of any more tasks to add, the user the proceeds with a tutorial.  The tutorial should be a screenshared video walkthrough of the tool.  The researcher must show the following:

* Login/logout - How to log in/out of the extension.  The researcher must emphasize that a participant's log activity is recorded if and only if they are logged in.
* The tutorial page - This page is accessible through a button in the extension and leads to `getTutorial.php`.  This is a detailed reference manual of each step of the annotation.
* The researcher must indicate that users are to annotate work-related search activity over the 5 days of their study participation during the work week (e.g., in our running example, Monday-Friday).
* The daily annotation is composed of 5 ordered steps:
	* Step 1: Mark Private Items (`instruments/getHome.php`) - Here, the user deletes any items in their log they do not wish to share.  They have the ability to do the following:
		* Search for activity using a built-in search function.
		* Select and send multiple items to a trash bin.
		* Undo deletions from the trash bin.
		* Permanently delete items in the trash bin.
		* **TO TRULY DELETE AN ITEM SO THAT IT IS NOT VISIBLE AND NOT SHARED WITH THE RESEARCHERS, THE PARTICIPANT MUST PERMANENTLY DELETE ITEMS IN THE TRASH BIN**
	* Step 2: Mark Sessions (`instruments/markSessions.php`) - Here, the user marks the beginning and end of search segments.  Users must be shown the following:
		* The definition of a session (see the 'Help' section of the interface).
		* **The importance of identifying sessions properly.** - Later, users will be asked to identify the tasks of sessions and search segments within sessions, so proper session identification is crucial.
		* How to mark the beginning and end of (potentially interleaved) sessions with Begin/End buttons.
		* How to cancel a selection with the 'Cancel' button.
		* How to finalize a selection with the 'Identify Sessions' button.
	* Step 3: Assign Tasks to Sessions (`instruments/markTasks.php`) - Here, the user assigns tasks to sessions.  The user must be shown the following:
		* The tasks on the right hand side of the interface.  Initially, these tasks correspond to the tasks provided in the entry interview.
		* How to add new tasks.
		* How to assign tasks to the sessions.
	* Step 4: Mark Search Segments and Intentions (`instruments/markIntentions.php`) - Here, the user identifies search segments (if necessary) and the intentions of those search segments.  The researcher must show the user the following:
		* The definition of a search segment (see the 'Help' section of the interface).
		* How to identify a new search segment with 'Begin/End' buttons.
		* How search segments through Google are automatically identified but may need correction.
		* How to identify intentions: by clicking the 'Mark Intentions' button and select any combination of intentions in the right sidebar (incl. other).
	* Step 5: Mark Success and Usefulness (of Sessions) (`instruments/searchSessionQuestionnaire.php`) - Here, the user identifies the success and usefulness of each session, with respect to task completion.  The researcher must show the user the following:
	* How to mark the success and usefulness of each individual search session.

This completes the entry interview. Participants are then told that they will be asked to complete this annotation 1x/day, every day (if possible) on Monday-Friday (a total of 5 days). Participants will then return for the exit interview on the following Monday.

### Five Day Annotation

The participant will then complete the five-days of search activity and annotation, as instructed above.

### Exit Interview

This interview is also conducted remotely through the video conferencing software.

Before the exit interview, the researcher must identify user search activity for which to conduct the exit interview.  Preferably, this identification will be conducted over the weekend.  The researcher will identify the following activity for the interview:
* Any tasks with search activity.
* Any tasks that have been added.
* Any sessions with peculiar session activity (e.g. no search segments, odd intentions, failed intentions, sessions without activity, 'Other' intentions).

Identification of this activity will be conducted through `userDataEntry.php`.

Per-task and per-session interviews are conducted through `taskAndSessionExitInterview.php`.  The interview is conducted in the following order:
* Tool interview (`userDataEntry.php`) - The researcher asks the participant general questions about the usability of the tool and enters the provided answers to the database.
* Task interviews (`taskAndSessionExitInterview.php`) - The researcher chooses the selected tasks (as per above) and asks the questions in the task interview ('Conduct Task Interview' button).  The researcher must also share the task information ('Copy Task URL to Clipboard' button) with the participant so they may review the task activity and provide more accurate answers.
* Session interviews (`taskAndSessionExitInterview.php`) - The researcher chooses the selected sessions (as per above) and asks questions in the session itnerview ('Conduct Session Interview' button).  The researcher must also share the session information ('Copy Session URL to Clipboard' button) with the participant so they may review the session activity and provide more accurate answers. 

This concludes the exit interview and concludes the entirety of the study.  The researcher must then organize prompt payment to the participant.

# Code Structure

## Server Code Structure

The server code is organized into several folders.  Below is a brief description of each folder.
  * `core` - Core classes used to track a user's session while conducting the study.  `Base.class.php`, for instance, tracks the user's ID, and `Stage.class.php` manages the flow between stages.
  * `data` - Contains video and data files presented to users during the study - for instance tutorial videos.
  * `img` - Images (unused in this study).
  * `instruments` - (Unused in this study) Contains code for different stages in the study. Each PHP file is a different stage.
  * `lib` - External library code imported throughout the study (e.g. Bootstrap, jQuery).
  * `old_image` - Images (unused in this study).
  * `services` - Code used to interact with the database and push/pull data.
  * `study_styles` - More external library code imported throughout the study.
  * `webPages` - a folder used to store the HTML and JSON for web pages visited and search queries issued by users during the study.
  * `./*.php` - General frontend functions.  Some of these are the pages a user accesses to annotate their daily activity.  Some of these are administrator web pages used to view users' progress through the study and to annotate user tasks.

## Extension Code Structure

The following is a description of the oganization of the `chrome` folder of the Firefox extension:

* `background.js` - Background scripts run when the extension starts up.
* `external` - Local copies of external libraries and scripts.
* `icons` - Icons used in the extension.
* `manifest.json` - Metadata JSON file used in Chrome extension.  To learn how to configure this, [see this page](https://developer.chrome.com/apps/manifest).
* `payload.js` - A script injected into each page.  Records live interactive data such as clicks and scrolls.
* `popup.*` - Code for managing the popup page.  Manages login, logout, and displaying the interactive tool.


# MySQL Structure

All data from the study was stored in MySQL tables.  Data takes the form of user login credentials, information about users' tasks, and interaction data.  Below is a brief overview of the organization of the MySQL tables:

* `actions` - A general log of actions conducted throughout the study.  The type of action is indicated in the `action` column.
* `click_data` - Click activity of users in the web browser.
* `copy_data` - Copy activity of users - e.g., any time they copied text through Ctrl+C.
* `intent_assignments` - Users' annotations of intentions for each query.
* `intent_permutations` - The per-user order in which the intention questionnaire questions were permuted.  Users were given a random permutation of intention assignments to prevent ordering effects.
* `keystroke_data` - A keystroke log.
* `mouse_data` - A log of mouse movements.
* `pages` - A timestamped log of pages visited by users.
* `paste_data` - Copy activity of users - e.g., any time they pasted text through Ctrl+V.
* `queries` - A timestamped log of queries issued to search engines by users.
* `querysegment_labels_user` - Labels assigned to query segments. `id` represents the `querySegmentID` column found in other tables. `querySegmentLabel` represents a label specific to each combination of user+date.
* `questionnaire_entry_demographic` - Answers to an introductory demographic questionnaire asked to users.
* `questionnaire_entry_tasks` - Answers to an introductory task questionnaire asked to users.  Users were asked about tasks they regularly conduct.
* `questionnaire_exit_tasks` - Answers to an exit task questionnaire asked to users.  Users were asked about things like task difficulty and task goal.
* `questionnaire_exit_sessions` - Answers to an exit session questionnaire asked to users.  Users were asked about search intentions during the sessions.
* `questionnaire_exit_tool` - Answers to an exit tool questionnaire asked to users.  Users were asked about the general usability of the tool.
* `questionnaire_questions` - Contains configuration data regarding different questions asked to users during the study (e.g. question type, question options).
* `questionnaire_recruitment` - Answers to the recruitment questionnaire at registration.  Duplicates information in the recruits table.
* `questions_progress` - Unused in this study.
* `questions_study` - Unused in this study.
* `recruits` - Registration information of participants (names, e-mails, requested date to conduct the study, etc.)
* `scroll_data` - Scroll activity of users on pages.
* `session_labels_user` - Labels assigned to query segments. `id` represents the `querySegmentID` column found in other tables. `querySegmentLabel` represents a label specific to each combination of user+date.
* `session_progress` - Unused in this study.
* `session_stages` - Unused in this study.
* `study_progress` - Contains metadata about the general progress of the study (e.g. number of completed participants).
* `session_labels_user` - Labels assigned to query segments. `id` represents the `sessionID` column found in other tables. `sessionLabel` represents a label specific to each combination of user+date.
* `timeline_display_data` - Unused in this study.
* `task_labels_user` - Labels assigned to tasks. `id` represents the `task_idcolumn` column found in other tables. `taskID` represents a label specific to each combination of user+date.
* `users` - Login and metadata information about the users

# Contact Us

For any questions, please Soumik Mandal soumik.mandal@rutgers.edu at or Matthew Mitsui at mmitsui@scarletmail.rutgers.edu.
