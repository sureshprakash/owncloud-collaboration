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
 
/**
 * This class contains all hooks
 */ 

class OC_Collaboration_Hooks
{
	/**
	 * Handle user removal from ownCloud
	 */
	public static function notifyUserDeletion($args)
	{
		try
		{
			\OCP\DB::beginTransaction();
		
			$query = \OCP\DB::prepare('SELECT `pid`, `title` FROM `*PREFIX*collaboration_project` WHERE `pid` IN 
										(SELECT DISTINCT(`pid`) FROM `*PREFIX*collaboration_works_on` WHERE `member`=?) AND `completed`=?');
									
			$result = $query->execute(array($args['uid'], false));
			$projs = $result->fetchAll();
		
			if(count($projs) != 0)
			{
				$projects = $projs[0]['title'];
				$pids = $projs[0]['pid'];
		
				for($i = 1; $i < count($projs); $i++)
				{
					$projects .= ', ' . $projs[$i]['title'];
					$pids .= ' '.$projs[$i]['pid'];
				}
			
				OC_Collaboration_Post::createPost('Member Removed', 'Owncloud member \'' . $args['uid'] . '\' has been removed from owncloud and hence from the project(s) ' . $projects, OC_User::getUser(), NULL, 'Member removal', OC_Collaboration_Project::getMembersWorkingOnProjects(explode(' ', $pids)), true);
			}
		
			OC_Collaboration_Project::deleteMemberRole(NULL, $args['uid'], NULL, OC_User::getUser(), true);
			OC_Collaboration_Skillset::removeSkillsOfMember($args['uid']);
			OC_Collaboration_Post::removePostsByMember($args['uid'], true);
		
			\OCP\DB::commit();
			
			OC_Log::write('collaboration', 'User deletion notification posted.', OCP\Util::INFO);
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * Notify member about file/folder sharing
	 */
	public static function notifyFileShare($args)
	{
		try
		{
			if(strcasecmp($args['itemType'], 'file') == 0 || strcasecmp($args['itemType'], 'folder') == 0)
			{
				$content = 'A ' . $args['itemType'] . ' has been shared with you at location /Shared' . $args['fileTarget'];
				OC_Collaboration_Post::createPost('File Shared', $content, $args['uidOwner'], NULL, 'File Share', array($args['shareWith']));
				OC_Log::write('collaboration', 'File share notification posted.', OCP\Util::INFO);
			}
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
}

// Instantiated to load the class
new OC_Collaboration_Hooks();
