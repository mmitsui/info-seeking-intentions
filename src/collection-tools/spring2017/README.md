# Search Intentions in Natural Settings

This README contains documentation about the code for the *Search Intentions in Natural Settings* study.  It begins with an outline of the study, the requirements, and the structure and flow of conducting the study. It then discusses the folder structure of the code.  It concludes with an overview of the MySQL data collected in this study.

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

In this study, participants conducted searches for their work in a naturalistic setting - i.e., their work environment - for information relating to different kinds of information search tasks related to their employment. Participants first conducted an initial interview in which they were asked for demographic information, introduced to the study’s software, and asked about regular search tasks. This initial interview lasted about one hour. This was followed by an experimental session. Participants were asked to record their searching activity over the course of five days and to annotate the tasks that they conduct. They were aslo asked to explain their search intentions at self-selected points during their tasks. This annotation took on average about one hour each day.  Various aspects of their searching behavior were recorded for subsequent analysis. The study concluded with an exit interview, in which participants were asked to analyze their search experiences and to give characterizations of the tasks they performed during the five days. This final exit interview lasted about one hour.

# Requirements

At least two machines are required to conduct this study.  There are no other restrictions on the machines.

* Participant Machine
	* Chrome browser (+ extension)
* Researcher Machine
	* A web browser (for managing the participant's progress)
	* Video conferencing tools (at least Skype, Zoom, and Google Hangouts)

# Study Design Outline

## Registration

A user's participation in the study begins with registration at `signup_intro.php`. Then, participants provide their consent. Then, participants register for an entry interview date on a Friday, as well as an exit interview required to take place on a Monday 10 days after the entry interview.  They receive a confirmation e-mail regarding the time of their study.

## Conducting The Study

The study takes place entirely removely in several steps.  These steps are as follows:

### Hello

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
