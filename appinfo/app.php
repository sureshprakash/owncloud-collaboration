<?php

/**
* This file is licensed under the Affero General Public License version 3 or
* later.
* See the COPYING-README file.
*/

$l = new OC_l10n('collaboration');

OC::$CLASSPATH['OC_Collaboration_Project'] = 'collaboration/lib/projects.php';
OC::$CLASSPATH['OC_Collaboration_Post'] = 'collaboration/lib/posts.php';
OC::$CLASSPATH['OC_Collaboration_Time'] = 'collaboration/lib/time.php';
OC::$CLASSPATH['OC_Collaboration_Comment'] = 'collaboration/lib/comments.php';
OC::$CLASSPATH['OC_Collaboration_Task'] = 'collaboration/lib/tasks.php';
OC::$CLASSPATH['OC_Collaboration_Mail'] = 'collaboration/lib/mail_templates.php';
OC::$CLASSPATH['OC_Collaboration_Hooks'] = 'collaboration/lib/hooks.php';
OC::$CLASSPATH['OC_Collaboration_Skillset'] = 'collaboration/lib/skillset.php';
OC::$CLASSPATH['OC_Collaboration_Report'] = 'collaboration/lib/reports.php';
OC::$CLASSPATH['OC_Collaboration_Calendar'] = 'collaboration/lib/calendar_support.php';

OC_Hook::connect('OC_User', 'post_deleteUser', 'OC_Collaboration_Hooks', 'notifyUserDeletion');
OC_Hook::connect('OCP\Share', 'post_shared', 'OC_Collaboration_Hooks', 'notifyFileShare');

\OCP\App::addNavigationEntry(array(
	'id' => 'collaboration',
	'order' => 0,
	'href' => \OCP\Util::linkToRoute('collaboration_route', array('rel_path' => '')),
	'icon' => \OCP\Util::imagePath('collaboration','collaboration.png'),
	'name' => $l->t('Collaboration')
	));
	
?>
