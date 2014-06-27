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

OCP\Util::addScript('collaboration', 'task_details');

OCP\Util::addStyle('collaboration', 'tabs');
OCP\Util::addStyle('collaboration', 'task_details');

if(isset($_POST['tid']))
{
	$tpl = new OCP\Template("collaboration", "task_details", "user");
	
	$details = OC_Collaboration_Task::readTask($_POST['tid']);
	
	if(isset($details['tid']))
	{
		$tpl->assign('task_details', $details);
		$tpl->assign('status_details', OC_Collaboration_Task::readHistory($_POST['tid'])); 
		
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
	exit();
}
?>
