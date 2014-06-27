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

$l = new OC_l10n('collaboration');

if(isset($_POST['start']) && isset($_POST['count']) && isset($_POST['project']) && isset($_POST['status']) && isset($_POST['assigned_to']) && isset($_POST['assigned_by']))
{
	if($_POST['project'] != '' && !OC_Collaboration_Project::isMemberWorkingOnProjectByTitle(OC_User::getUser(), $_POST['project']))
	{	
		throw new Exception(OC_User::getUser() . ' is trying to access project ' . $_POST['project']);
		OC_JSON::error();
		exit();
	}
	
	$args = array(
			'assigned_to' => (($_POST['assigned_to'] == true)? OC_User::getUser(): NULL), 
			'assigned_by' => (($_POST['assigned_by'] == true)? OC_User::getUser(): NULL),
			'project' => (($_POST['project'] == '')? NULL: $_POST['project']), 
			'status' => (($_POST['status'] == '')? NULL: $_POST['status']));
	 
	$tasks = OC_Collaboration_Task::readTasks($args, $_POST['start'], $_POST['count']);

	$text = '';
	
	foreach($tasks as $each)
	{
		if(!isset($each['tid']) || $each['tid'] == '')
		{
			break;
		}
		
		$datetime = OC_Collaboration_Time::convertDBTimeToUITime($each['ending_time']);
		
		$text .= '<div class="unit">
					<div class="task_title">'		
							. $each['title'] .
					'</div>

					<div class="contents">	
						<div class="description">'	
							. $each['description'] .
						'</div>							
							<br />
							<form class="view_details" action="' . \OCP\Util::linkToRoute('collaboration_route', array('rel_path'=>'task_details')) . '" method="post" >
								<input type="hidden" name="tid" value="' . $each['tid']. '" />
								<input type="submit" value="' . $l->t('View details') . '" />
						</form>'
						. ((strcasecmp(OC_Collaboration_Task::getTaskCreator($each['tid']), OC_User::getUser()) == 0)? 
						'<div class="edit" >
							<button class="btn_edit" id="btn_edit_' . $each['tid'] . '" >'
										. $l->t('Edit') .
								'</button>
							</div>': '') .
					'</div>

					<div class="details">
						<div class="task_status">'
								. $l->t('Status: %s', array(OC_Collaboration_Task::getStatusInFormat($each['status'], $each['member'], $each['creator']))) .
						'</div>
						
						<div class="deadline_details">' .
								$l->t('Deadline: %s', array($l->l('datetime', $datetime))) .
						'</div>
					</div>
				</div>';
	}
	
	OCP\JSON::success(array('tasks' => $text));
	exit();
}
OC_JSON::error();
exit();
