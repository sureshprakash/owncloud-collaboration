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

$l=OC_L10N::get('collaboration');

if(isset($_POST['member']) && isset($_POST['start']) && isset($_POST['count']) && isset($_POST['project']))
{
	if(strcasecmp($_POST['member'], OC_User::getUser()) != 0)
	{
		\OCP\Util::writeLog('collaboration', OC_User::getUser() . ' is trying to access posts of ' . $_POST['member'], \OCP\Util::WARN);
		OC_JSON::error();
		exit();
	}
	
	if($_POST['project'] != '' && !OC_Collaboration_Project::isMemberWorkingOnProjectByTitle(OC_User::getUser(), $_POST['project']))
	{	
		\OCP\Util::writeLog('collaboration', OC_User::getUser() . ' is trying to access project ' . $_POST['project'], \OCP\Util::WARN);
		OC_JSON::error();
		exit();
	}
	
	$posts = OC_Collaboration_Post::readPosts($_POST['member'], $_POST['project'], $_POST['start'], $_POST['count']);
	
	$text = '';
	
	foreach($posts as $each)
	{
		if(!isset($each['title']) || $each['title'] == '')
		{
			break;
		}
		
		$datetime = explode(' ', $each['time']);
		
		$text .= 
		'<div class="unit">
		<div class="post_title">'
			. $each['title'] .
		'</div>
		
		<div class="contents">'
			. $each['content'] .
			'<br />
			<br />
			<div class="comment" >
				<button class="btn_comment" id="btn_comment_' . $each['post_id'] . '" >'
					. ($l->t('Comments') . ' (' . OC_Collaboration_Comment::getCommentCount($each['post_id']) . ')') .
				'</button>
			</div>
		</div>
		
		<div class="details">
			<div class="proj_title">'
				. ((isset($each['proj_title']) && !is_null($each['proj_title']))? ($l->t('Project: %s', array($each['proj_title']))): '') .
			'</div>
			
			<div class="creation_details">'
				. ($l->t('On %s at %s by %s', array($l->l('date', $datetime[0]), $l->l('time', $datetime[1]), $each['creator']))) .
			'</div>
		</div>
		</div>';
		
	}
	
	OCP\JSON::success(array('posts' => $text));
	exit();
}
OC_JSON::error();
exit();
