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

OCP\Util::addScript('collaboration', 'projects');

OCP\Util::addStyle('collaboration', 'tabs');
OCP\Util::addStyle('collaboration', 'projects');


$tpl = new OCP\Template("collaboration", "projects", "user");

if(isset($_GET['project']) && $_GET['project'] != 'ALL')
{
	if(!OC_Collaboration_Project::isMemberWorkingOnProjectByTitle(OC_User::getUser(), $_GET['project']))
	{	
		header('Location: ' . \OCP\Util::linkToRoute('collaboration_route', array('rel_path'=>'dashboard')));
		throw new Exception(OC_User::getUser() . ' is trying to access project ' . $_GET['project']);
		exit();
	}
	else
	{
		$tpl->assign('project', $_GET['project']);
		$tpl->assign('projects', OC_Collaboration_Project::getProjectDetails(OC_User::getUser(), $_GET['project']));
	}
}
else
{
	$tpl->assign('projects', OC_Collaboration_Project::getProjectDetails(OC_User::getUser()));
}

$tpl->printPage();
?>
