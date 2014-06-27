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

if(isset($_POST['content']) && isset($_POST['post_id']))
{
	if(!OC_Collaboration_Post::isPostAccessibleToMember($_POST['post_id'], OC_User::getUser()))
	{	
		\OCP\Util::writeLog('collaboration', OC_User::getUser() . ' is trying to write comment on post with ID ' . $_POST['post_id'], \OCP\Util::WARN);
		OC_JSON::error();
		exit();
	}
	
	$comment_id = OC_Collaboration_Comment::createComment($_POST['content'], OC_User::getUser(), $_POST['post_id']);
	
	if($comment_id == false)
	{
		OC_JSON::error();
		exit();
	}
	
	$comment = OC_Collaboration_Comment::readComment($comment_id);
	
	$datetime = explode(' ', $comment['time']);
	
	$text = '<div class="comment" id="comment_' . $comment['comment_id'] . '" >
			<span class="comment_creator" >' . $comment['creator'] . ': </span>
			<span id="comment_content_' . $comment['comment_id'] . '" >' . $comment['content'] . '</span>
			<hr />
			<div class="comment_details" >
			<span class="updated_time" >' .
			$l->t('On %s at %s', array($l->l('date', $datetime[0]), $l->l('time', $datetime[1]))) .
			'</span>
			<div class="edit_delete_comment" >
			<a class="edit" data-collaboration-type="comment" data-collaboration-id="' . $comment['comment_id'] . '" >' .
			$l->t('Edit') .
			'</a>
			 | 
			 <a class="delete" data-collaboration-type="comment" data-collaboration-id="' . $comment['comment_id'] . '">' .
			 $l->t('Delete') .
			 '</a>
			 </div>
			 </div>
			 </div>';
			
	OCP\JSON::success(array('creation_string' => $text));
	exit();
}
OC_JSON::error();
exit();
