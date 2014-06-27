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

OCP\Util::addScript('collaboration', 'project_details');

OCP\Util::addStyle('collaboration', 'tabs');
OCP\Util::addStyle('collaboration', 'project_details');

if(isset($_POST['pid']) && OC_Collaboration_Project::isMemberWorkingOnProject(OC_User::getUser(), $_POST['pid']))
{
	$tpl = new OCP\Template("collaboration", "project_details", "user");
	
	$details = OC_Collaboration_Project::readProject($_POST['pid']);
	
	if(isset($details['pid']))
	{
		$tpl->assign('project_details', $details);
		
		if(isset($_POST['msg']))
		{
			$tpl->assign('msg', $_POST['msg']);
		}
		
		$tpl->printPage();
	}
	else
	{
		goToDashboard();
	}
}
else
{
	goToDashboard();
}

function goToDashboard()
{
	header('Location: ' . \OCP\Util::linkToRoute('collaboration_route', array('rel_path'=>'dashboard')));
	\OCP\Util::writeLog('collaboration', OC_User::getUser() . ' is trying to access project ' . $_POST['project'], \OCP\Util::WARN);
	exit();
}
?>
