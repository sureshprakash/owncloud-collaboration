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

if(isset($_POST['comment_id']))
{
	if(!OC_Collaboration_Comment::isCommentWrittenByMember($_POST['comment_id'], OC_User::getUser()))
	{	
		\OCP\Util::writeLog('collaboration', OC_User::getUser() . ' is trying to edit comment with ID ' . $_POST['comment_id'], \OCP\Util::WARN);
		OC_JSON::error();
		exit();
	}
	
	$time = OC_Collaboration_Comment::editComment($_POST['comment_id'], $_POST['content']);
	$updated_time = NULL;
	$success = false;
	
	if(!is_null($time))
	{
		$success = true;
		$datetime = explode(' ', $time);
		$updated_time = $l->t('On %s at %s', array($l->l('date', $datetime[0]), $l->l('time', $datetime[1])));
	}
	
	OCP\JSON::success(array('edit_succeeded' => $success, 'updated_time' => $updated_time));
	exit();
}
OC_JSON::error();
exit();
