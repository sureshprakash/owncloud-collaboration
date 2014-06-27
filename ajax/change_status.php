<?php

/**
* ownCloud - bookmarks plugin
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
* You should have received a copy of the GNU Lesser General Public 
* License along with this library.  If not, see <http://www.gnu.org/licenses/>.
* 
*/

OCP\JSON::checkLoggedIn();
OCP\JSON::callCheck();

OCP\JSON::checkAppEnabled('collaboration');

if(isset($_POST['tid']) && isset($_POST['event']) && isset($_POST['reason']))
{
	if(strcasecmp(OC_Collaboration_Task::getWorkingMember($_POST['tid']), OC_User::getUser()) != 0)
	{	
		throw new Exception(OC_User::getUser() . ' is trying to change status of task ' . $_POST['tid']);
		OC_JSON::error();
		exit();
	}
	
	$tid = $_POST['tid'];
	$title = OC_Collaboration_Task::getTaskTitle($tid);
	$status = OC_Collaboration_Task::getStatusFromPerformerEvent($_POST['event']);
	$creator = OC_User::getUser();
	
	OC_Collaboration_Task::changeStatus($tid, $title, $status, $creator, NULL, $_POST['reason'], false);
	
	OCP\JSON::success(array());
	exit();
}
OC_Log::write('collaboration', $_POST['tid'] . ' ' . $_POST['event'] . ' ' . $_POST['reason'], OCP\Util::DEBUG);
OC_JSON::error();
exit();
