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

$l = OC_L10N::get('collaboration');

if(isset($_POST['member']) && isset($_POST['start']) && isset($_POST['count']))
{
	if(strcasecmp($_POST['member'], OC_User::getUser()) != 0)
	{
		\OCP\Util::writeLog('collaboration', OC_User::getUser() . ' is trying to access projects of ' . $_POST['member'], \OCP\Util::WARN);
		OC_JSON::error();
		exit();
	}
	
	$projects = OC_Collaboration_Project::getProjectDetails(OC_User::getUser(), NULL, $_POST['start'], $_POST['count']);
	
	$text = '';
	
	foreach($projects as $each)
	{
		$datetime = explode(' ', $each['starting_date']); 
		$datetime1 = explode(' ', $each['ending_date']);
	
		if(!isset($each['title']) || $each['title'] == '')
		{
			break;
		}
		
		$datetime = explode(' ', $each['time']);
		
		$text .= 
		'<div class="unit">
					<div class="project_title">'		
						. $each['title'] .
					'</div>

					<div class="contents"><div class="description" >'
							. $each['description'] .
							'</div>' .
							((!OC_Collaboration_Project::isAdmin())? '':
							'<br />
							<br />
							<div class="edit" >
								<button class="btn_edit" id="btn_edit_' . $each['pid'] . '" >'
										. $l->t('Edit') . 
								'</button>
							</div>') .
					'</div>
					
					<div class="details">

						<div class="creation_details">'
							
								. $l->t('On %s at %s', array($l->l('date', $datetime[0]), $l->l('time', $datetime[1]))) . 
						'</div>
						
						<div class="deadline_details">'
								 
								. $l->t('On %s at %s', array($l->l('date', $datetime1[0]), $l->l('time', $datetime1[1]))) . 
						'</div>
					</div>
				</div>';
	}
	
	OCP\JSON::success(array('projects' => $text));
	exit();
}
OC_JSON::error();
exit();
