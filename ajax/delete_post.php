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

if(isset($_POST['post_id']))
{
	if(!OC_Collaboration_Post::isPostAccessibleToMember($_POST['post_id'], OC_User::getUser()))
	{	
		\OCP\Util::writeLog('collaboration', OC_User::getUser() . ' is trying to delete post with ID ' . $_POST['post_id'], \OCP\Util::WARN);
		OC_JSON::error();
		exit();
	}
		
	if(!OC_Collaboration_Post::isPostEditable($_POST['post_id']))
	{
		\OCP\Util::writeLog('collaboration', OC_User::getUser() . ' is trying to delete read only post with ID ' . $_POST['post_id'], \OCP\Util::WARN);
		OC_JSON::error();
		exit();
	}
	
	OCP\JSON::success(array('delete_succeeded' => OC_Collaboration_Post::deletePost($_POST['post_id'])));
	exit();
}
OC_JSON::error();
exit();
