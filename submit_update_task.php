<?php
/**
 * ownCloud - collaboration plugin
 *
 * @authors Dr.J.Akilandeswari, R.Ramki, R.Sasidharan, P.Suresh
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

\OCP\User::checkLoggedIn();
\OCP\App::checkAppEnabled('collaboration');

OCP\App::setActiveNavigationEntry( 'collaboration' );

OCP\Util::addStyle('collaboration', 'tabs');

OCP\Util::addScript('collaboration', 'display_message');
OCP\Util::addScript('collaboration', 'create_event');

$l = OC_L10N::get('collaboration');

$eve = new OCP\Template('collaboration', 'event_form', 'user');

if(!OC_Collaboration_Project::isMemberWorkingOnProject(OC_User::getUser(), $_POST['pid']))
{	
	\OCP\Util::writeLog('collaboration', OC_User::getUser() . ' is trying to create task on project with Project ID ' . $_POST['pid'], \OCP\Util::WARN);
	
	$eve->assign('permission_granted', 'false');
	$eve->assign('title', $_POST['title']);
	
	$eve->printPage();
}
else
{
	if(!isset($_POST['tid']))
	{
		$tid = NULL;
		
		if(isset($_POST['status']))
		{
			$tid = OC_Collaboration_Task::createTask($_POST['title'], $_POST['description'], OC_User::getUser(), $_POST['pid'], $_POST['priority'], $_POST['deadline_time'], 'In Progress', $_POST['member']);
		}
		else
		{
			$tid = OC_Collaboration_Task::createTask($_POST['title'], $_POST['description'], OC_User::getUser(), $_POST['pid'], $_POST['priority'], $_POST['deadline_time'], 'Unassigned', NULL);
		}
	
		if($tid != false && isset($_POST['send_mail']))
		{
			OC_Collaboration_Mail::sendTaskCreationMail($_POST['title'], $_POST['description'], $_POST['pid'], $_POST['member'], $_POST['deadline_time']);
		}
		
		$eve->assign('title', $l->t('Loading...'));
		$eve->assign('permission_granted', 'true');
		$eve->assign('task', $tid);

		$eve->printPage();
	
	}
	else
	{
		if(!isset($_POST['status']))
		{
			$_POST['status'] = NULL;
		}
		if(!isset($_POST['member']))
		{
			$_POST['member'] = NULL;
		}
		if(!isset($_POST['reason']))
		{
			$_POST['reason'] = NULL;
		}
		
		$status = OC_Collaboration_Task::updateTask($_POST['tid'], $_POST['title'], $_POST['description'], OC_User::getUser(), $_POST['pid'], $_POST['priority'], $_POST['deadline_time'], $_POST['status'], $_POST['member'], $_POST['reason']);
		
		if($status != false && isset($_POST['send_mail']))
		{
			OC_Collaboration_Mail::sendTaskCreationMail($_POST['title'], $_POST['description'], $_POST['pid'], $_POST['member'], $_POST['deadline_time']);
		}
	
		$eve = new OCP\Template('collaboration', 'event_edit_form', 'user');
	
		$eve->assign('title', $l->t('Loading...'));
		$eve->assign('permission_granted', 'true');
		$eve->assign('task', $_POST['tid']);

		$eve->printPage();
		
//		print_unescaped('<META HTTP-EQUIV="Refresh" Content="0; URL=' . \OCP\Util::linkToRoute('collaboration_route', array('rel_path' => 'submit_change_task')) . '?task=' . $_POST['tid'] . '&title=' . $_POST['title'] . '">');
	}
}

?>
