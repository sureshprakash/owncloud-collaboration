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
OCP\Util::addStyle('collaboration', 'update_task');

OCP\Util::addStyle('collaboration/3rdparty', 'jquery-ui-timepicker-addon');

OCP\Util::addScript('collaboration', 'update_task');

OCP\Util::addScript('collaboration/3rdparty', 'jquery-ui-sliderAccess');
OCP\Util::addScript('collaboration/3rdparty', 'jquery-ui-timepicker-addon');

$l = OC_L10N::get('collaboration');

$tpl = new OCP\Template('collaboration', 'update_task', 'user');

$bol = OC_Collaboration_Project::isAdmin();

if($bol == true)
{
	if(isset($_POST['tid']))
	{
		$tpl->assign('title', $l->t('Update Task'));
		$tpl->assign('submit_btn_name', $l->t('Update'));
	
		$tpl->assign('tid', $_POST['tid']);
		$tpl->assign('task_details', OC_Collaboration_Task::readTask($_POST['tid']));
	}
	else
	{
		$tpl->assign('title', $l->t('Create Task'));
		$tpl->assign('submit_btn_name', $l->t('Create'));
		$tpl->assign('projects', OC_Collaboration_Project::getProjects(OC_User::getUser()));
	}
	
	$tpl->printPage();
}
else
{
	header('Location: ' . \OCP\Util::linkToRoute('collaboration_route', array('rel_path'=>'dashboard')));
	\OCP\Util::writeLog('collaboration', 'Permission denied for ' . OC_User::getUser() . ' to create task.', \OCP\Util::WARN);
	exit();
}
?>
