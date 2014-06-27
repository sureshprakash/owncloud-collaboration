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
OCP\Util::addStyle('collaboration', 'update_project');

OCP\Util::addScript('collaboration', 'update_project');

$l = OC_L10N::get('collaboration');

$bol = OC_Collaboration_Project::isAdmin();

if($bol == true)
{
	$tpl = new OCP\Template('collaboration', 'update_project', 'user');
	
	if(isset($_POST['pid']))
	{
		$tpl->assign('title', $l->t('Update Project'));
		$tpl->assign('submit_btn_name', $l->t('Update'));
		
		$tpl->assign('pid', $_POST['pid']);
		$tpl->assign('project_details', OC_Collaboration_Project::readProject($_POST['pid']));
	}
	else
	{
		$tpl->assign('title', $l->t('Create Project'));
		$tpl->assign('submit_btn_name', $l->t('Create'));
	}
	
	$tpl->printPage();
}
else
{
/*	
	Use this if you don't want to redirect
	OCP\Util::addScript('collaboration', 'display_message');
	
	$tpl = new OCP\Template('collaboration', 'display_message', 'user');
	$tpl->assign('title', 'Permission denied');
	$tpl->assign('msg', 'Sorry, you must have admin rights, to create a project.');
	$tpl->printPage(); 
*/
	
	header('Location: ' . \OCP\Util::linkToRoute('collaboration_route', array('rel_path'=>'dashboard')));
	\OCP\Util::writeLog('collaboration', 'Permission denied for ' . OC_User::getUser() . ' to create project.', \OCP\Util::WARN);
	exit();
}
?>
