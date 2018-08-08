# Information Seeking Intentions Study

This README contains documentation about the code for the *Information Seeking Intentions* study.  It begins with an outline of the study, the requirements, and the structure and flow of conducting the study. It then discusses the folder structure of the code.  It concludes with an overview of the MySQL data collected in this study.

# Table of Contents
1. [Study Overview](#study-overview)
2. [Requirements](#requirements)
3. [Study Design Outline](#study-design-outline)
	1. [Registration](#registration)
	2. [Conducting The Study](#conducting-the-study)
4. [Code Structure](#code-structure)
	1. [Server Code Structure](#server-code-structure)
	2. [Extension Code Structure](#extension-code-structure)
5. [MySQL Structure](#mysql-structure)
6. [Contact Us](#contact-us)

# Study Overview

In this study, participants conducted two searches in an experimental setting for information relating to different kinds of information search tasks related to journalism assignments. Each experimental session lasted on average about two hours, and the study was held in a university laboratory setting. Participants completed a background questionnaire about each search task and assignment, and then conducted searches for information relating to the assignment. After each search session, participants evaluated the information that they found and explain their search intentions at selected points. Various aspects of their searching behavior were recorded for subsequent analysis.

# Requirements

Two separate computers are required to conduct this study.  They must be remote (e.g. in separate rooms) and accessible to each other remotely (e.g. via Ethernet).

* Participant Machine
	* GazePoint GP3 + all accompanying software
	* Morae Recorder
	* Firefox (+ installed extension)
* Researcher Machine
	* Morae Observer
	* Morae Manage
	* A web browser (for managing the participant's progress)

# Study Design Outline

## Registration

A user's participation in the study begins with registration at `signup_intro.php`. Then, participants register for a date for arriving in the lab and conducting the study.  They receive a confirmation e-mail regarding the time of their study.

## Conducting The Study

When participants arrive to the site of the study, the study facilitator must set `arrived` in the `users` table to 1 to indicate that the participant has arrived and will conduct the study. This is done through `editUsers.php`. The participant completes handwritten consent forms. The participant then signs into the study through `index.php`. The participant then conducts the study by proceeding through the following stages (located in the `instruments` folder):

* Welcome (`welcome.php`) - Welcomes the participant to the study and presents an overview of the study.
* Background Questionnaire (`pretask_q.php`) - The participant completes a basic demographic questionnaire regarding things such as gender, age, search expertise, and the participant's first language.
* System Tutorial (`system_tutorial.php`) - The participant views a tutorial on how to use the Firefox extension toolbar and sidebar to complete the proceeding search task.  The participant then confirms that (s)he viewed the video.
* Pre-Search Questionnaire (`presearch_q.php`) - The participant is shown the task description and answers questions regarding the task (e.g. their familiarity with the task/topic). **THE USER MUST NOT WORK ON THE TASK DURING THIS STAGE! AT THIS TIME, THE RESEARCHER MUST ALSO CALIBRATE AND BEGIN RECORDING OF THE GAZEPOINT EYE TRACKER.**
* Main Task (`maintask.php`) - The participant is shown the task prompt. **NOW THE USER MAY WORK ON THE TASK.**  The Firefox extension should now display a timer which starts at 20 minutes.  The extensionsidebar  will also show the participant's bookmarks, pages, and queries.  The user may end early by clicking a button in the sidebar.
	* NOTE: During this time, the researcher will use Morae on a second machine to monitor the searcher's activity.  The researcher will use the Markers in Morae to record: the start of query segments, bookmark actions, unsave actions.
* Post-Search Warning (`maintask_postwarning.php`) - This is a warning given to participants. This warning informs participants to not click anything.  At this time, the researcher must save the Morae recording, which will be saved and stored on the participant's machine. 
* **AT THIS TIME, PLEASE SAVE ANY RECORDINGS FROM MORAE - CSV AND VIDEO -  ONTO A USB AND TRANSFER THEM TO THE RESEARCHER MACHINE. EDIT ANY INCORRECT MORAE MARKERS. UPLOAD ALL FILES `editUser.php`. THE RESEARCHER MUST ALSO STOP RECORDING FROM THE GAZEPOINT EYE TRACKER.**  .
* Post-Search Questionnaire (`postsearch_q.php`) - While transferring, editing, and uploading files as in the previous step, show users this stage.  They will complete a post-task questionnaire regarding the previous task (e.g. difficulty).
* Intention Tutorial (`intent_tutorial.php`) - While transferring, show users this stage.  This is a tutorial video on how to annotate the intentions of query segments and to mark the usefulness of bookmarks, their reasons for reformulation, and their reasons for unsaving pages.
* Intention Transition (`transition_intent.php`) - Users will stop at this stage while waiting for the researcher to finish upload and transfer.  When the researcher is finished. Please confirm that the participant understood the previous instructions before allowing them to proceed to the next step.
* Intention Annotation (`intent.php`) - The main intention interface.  Participants will annotate the intentions of query segments, the usefulness of bookmarks, their reasons for reformulation, and their reasons for unsaving.  Researchers may monitor the participant's progress remotely through `intentVisualization.php`.
* Main Task Transition (`transition_maintask.php`) - Users will stop at this page after completing the intention annotation. The researcher must allow them to continue to the next task through `editUser.php`.
* **Repeat System Tutorial to Intention Annotation** - These stages are repeated for the second task.
* Finish Session (`finish_session.php`) - The user is told that they have completed the study.  At this point, the researcher should compensate the participant with appropriate payment.  The user is also automatically logged out of the system.
* Exit System (`index.php`) - (A necessary stage put at the end of the stages table.)

**NOTE**: It is encouraged that study facilitators clear cookies and history from the browser on which the user conducted the study, so that future participants will not receive previous participants' search suggestions or page suggestions.

# Code Structure

## Server Code Structure

The server code is organized into several folders.  Below is a brief description of each folder.
  * `core` - Core classes used to track a user's session while conducting the study.  `Base.class.php`, for instance, tracks the user's ID, and `Stage.class.php` manages the flow between stages.
  * `data` - Contains video and data files presented to users during the study - for instance tutorial videos.
  * `img` - Images (unused in this study).
  * `instruments` - Contains code for different stages in the study. Each PHP file is a different stage.
  * `lib` - External library code imported throughout the study (e.g. Bootstrap, jQuery).
  * `old_images` - Images (unused in this study).
  * `services` - Code used to interact with the database and push/pull data.  Typically accessed through the tool's web interface or Firefox extension.
  * `sidebar` - Code called from the Firefox extension sidebar to push/pull data.
  * `study_styles` - More external library code imported throughout the study.
  * `webPages` - a folder used to store the HTML and JSON for web pages visited and search queries issued by users during the study.
  * `./*.php` - General frontend functions.  Some of these are administrator web pages used to view users' progress through the study and to annotate user tasks.

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
* `participant_id_to_task` - Maps participant ID's to the order in which tasks are given to participants.  When a participant first signs into the study, they are given the next available participant ID from this table. (See participantID column in `users` table.)
* `paste_data` - Copy activity of users - e.g., any time they pasted text through Ctrl+V.
* `queries` - A timestamped log of queries issued to search engines by users.
* `questionnaire_answers` - Unused in this study..
* `questionnaire_demographic` - Answers to an introductory demographic questionnaire asked to users.
* `questionnaire_postsearch` - Answers to an post-search questionnaire asked after each search task.
* `questionnaire_pretask` - Answers to an post-search questionnaire asked before each search task.
* `questionnaire_questions` - Contains configuration data regarding different questions asked to users during the study (e.g. question type, question options).
* `questionnaire_recruitment` - Answers to the recruitment questionnaire at registration.  Duplicates information in the recruits table.
* `questions_progress` - Indicates the start and completion time of questions for each user during the study.
* `questions_study` - The task prompts given to users during the study and their data (e.g. question prompt and task types)
* `recruits` - Registration information of participants (names, e-mails, requested date to conduct the study, etc.)
* `scroll_data` - Scroll activity of users on pages.
* `session_progress` - A timestamped log of the progress users made in the study - the times they entered and exited each stage of the study
* `session_stages` - A list of the different stages in the study.  Stages were visited by users in increasing order.  The `stageID` column in other tables refers to which of these stages the user was in.
* `tag_assignments` - Unused in this study.
* `timeline_display_data` - Unused in this study.
* `users` - Login and metadata information about the users
* `video_intent_assignments` - Users' annotations of intentions for each query.
* `video_reformulation_history` - Users' annotations of reformulations for each query.
* `video_save_history` - Users' annotations of bookmark usefulness.
* `video_segments` - Metadata regarding segments (userID, stageID, etc.)
* `video_unsave_history` - Users' annotations of their reasons for unsaving bookmarks.

# Contact Us

For any questions, please Soumik Mandal soumik.mandal@rutgers.edu at or Matthew Mitsui at mmitsui@scarletmail.rutgers.edu.

