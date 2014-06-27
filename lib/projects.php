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
 * This class manages the project details
 */ 
	
class OC_Collaboration_Project
{
	/**
	 * @brief Fetch list of projects and their details
	 * @param Member whose projects have to be fetched
	 * @param Title of the project (Default: All projects)
	 * @param The starting project serial number
	 * @param The number of projects to be fetched
	 * @return Array|boolean (Details of the projects|false)
	 */	
	public static function getProjectDetails($member='', $project=NULL, $start=0, $count=20)
	{
		try
		{
			$result = NULL;
			
			if(is_null($project))
			{
				$sql = 'SELECT * FROM `*PREFIX*collaboration_project` WHERE `pid` IN 
						(SELECT `pid` FROM `*PREFIX*collaboration_works_on` WHERE `member`=?)
						ORDER BY `title` ASC LIMIT ' . $start . ', ' . $count;
				
				$query = OCP\DB::prepare($sql);

				$result = $query->execute(array($member));
			}
			else
			{
				$sql = 'SELECT * FROM `*PREFIX*collaboration_project` WHERE `title`=?';
				
				$query = OCP\DB::prepare($sql);

				$result = $query->execute(array($project));
			}

			$val = array(array());
		
			for($i = 0; $row = $result->fetchRow(); $i++)
			{
				foreach($row as $key => $value)
				{
					$val[$i][$key] = $value;
				}
			}
	
			return $val;
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}

	}
	
	/**
	 * @brief Fetch the details of the given project
	 * @param Project ID
	 * @return Array|boolean (Details of the project|false)
	 */
	public static function readProject($pid)
	{
		try
		{
			$sql = 'SELECT * FROM `*PREFIX*collaboration_project` WHERE `pid`=?';
		
			$query = OCP\DB::prepare($sql);
			$result = $query->execute(array($pid))->fetchAll();
		
			return $result[0];
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
			
	}
	
	/**
	 * @brief Creates a new project
	 * @param Project title
	 * @param Description of the project
	 * @param Member who created the project
	 * @param Deadline of the project
	 * @param List of members working in the project and their roles
	 * @return int|boolean (Post ID of the post specifying the project creation|false)
	 */
	public static function createProject($title, $desc, $creator, $deadline, $details=NULL)
	{
		$post_id = NULL;
		
		try
		{
			\OCP\DB::beginTransaction();
			
			
			$calendar_id = OC_Calendar_Calendar::addCalendar(\OC_User::getUser(), $title, 'VEVENT,VTODO,VJOURNAL', null, 0, '#3a87ad');
			
			$query = \OCP\DB::prepare('INSERT INTO `*PREFIX*collaboration_project`(`title`, `description`, `starting_date`, `ending_date`, `last_updated`, `calendar_id`) VALUES(?, ?, CURRENT_TIMESTAMP, ?, CURRENT_TIMESTAMP, ?)');
			$query->execute(array($title, $desc, OC_Collaboration_Time::convertUITimeToDBTime($deadline . ' 23:59:59'), $calendar_id));
		
			$pid = OCP\DB::insertid('*PREFIX*collaboration_project');

			$add_member = \OCP\DB::prepare('INSERT INTO `*PREFIX*collaboration_works_on`(`pid`, `member`, `role`) VALUES(?, ?, ?)');
			$add_member->execute(array($pid, $creator, 'Creator'));
				
			$cnt = count($details);
			
			if($cnt != 0 && isset($details[0]['member']))
			{
				foreach($details as $detail)
				{
					$member = strtolower($detail['member']);
				
					if(!(OC_User::userExists($member)))
					{
						OC_User::createUser($member, $member);
					}
			
					$add_member->execute(array($pid, $member, $detail['role']));
				
					OC_Preferences::setValue($member, 'settings', 'email', $detail['email']);
					OC_Preferences::setValue($member, 'collaboration', 'mobile', $detail['mobile']);
				}
				
			}
		
			$post_id = OC_Collaboration_Post::createPost('Project Created', 'Project \'' . $title . '\' has been created with deadline ' . OC_Collaboration_Time::convertToFullDate($deadline) . '.', $creator, $pid, 'Project Creation', array(), true);
		
			\OCP\DB::commit();
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
		
		return $post_id;
	}

	/**
	 * @brief Finds the project ID corresponding to the project title
	 * @param Project title
	 * @return int|boolean (Project ID|-1|false)
	 */
	public static function getProjectId($title)
	{
		try
		{
			$query = \OCP\DB::prepare('SELECT `pid` FROM `*PREFIX*collaboration_project` WHERE `title`=?');
			$result = $query->execute(array($title));
		
			$row = $result->fetchRow();
		
			return (($row)? $row['pid']: -1);
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * @brief Finds the project title corresponding to the project ID
	 * @param Project ID
	 * @return string|NULL|boolean (Project title|NULL|false)
	 */
	public static function getProjectTitle($pid)
	{
		try
		{
			$query = \OCP\DB::prepare('SELECT `title` FROM `*PREFIX*collaboration_project` WHERE `pid`=?');
			$result = $query->execute(array($pid));
		
			$row = $result->fetchRow();
		
			return (($row)? $row['title']: NULL);
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}

	/**
	 * @brief Modifies the given project
	 * @param Project ID
	 * @param Project title
	 * @param Description of the project
	 * @param Deadline of the project
	 * @param List of members working in the project and their roles
	 * @param Member who updated the project
	 * @param Whether or not the project has been completed
	 * @return int|boolean (Post ID of the post specifying the project updation|false)
	 */
	public static function updateProject($pid, $title, $desc, $deadline, $details, $updater, $completed)
	{
		$post_id = NULL;
		
		try
		{
			if($completed && OC_Collaboration_Task::hasPendingTasks($pid))
			{
				throw new \Exception('Cannot delete project with pending tasks');
			}
			
			\OCP\DB::beginTransaction();
			
			$query = \OCP\DB::prepare('UPDATE `*PREFIX*collaboration_project` SET `title`=?, `description`=?, `last_updated`=CURRENT_TIMESTAMP, `ending_date`=?, `completed`=? WHERE `pid`=?');
			$query->execute(array($title, $desc, OC_Collaboration_Time::convertUITimeToDBTime($deadline . ' 23:59:59'), $completed, $pid));
		
			$add_member = \OCP\DB::prepare('INSERT INTO `*PREFIX*collaboration_works_on`(`pid`, `member`, `role`) VALUES(?, ?, ?)');

			$cnt = count($details);
			
			if($cnt != 0 && isset($details[0]['member']))
			{
				foreach($details as $detail)
				{
					$member = strtolower($detail['member']);
				
					if(!(OC_User::userExists($member)))
					{
						OC_User::createUser($member, $member);
					}
			
					$add_member->execute(array($pid, $member, $detail['role']));
				
					OC_Preferences::setValue($member, 'settings', 'email', $detail['email']);
					OC_Preferences::setValue($member, 'collaboration', 'mobile', $detail['mobile']);
				}
			}
		
			$post_id = OC_Collaboration_Post::createPost('Project Updated', 'Project \'' . $title . '\' has been updated' . '.', $updater, $pid, 'Project Updation', array(), true);
		
			\OCP\DB::commit();
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
		
		return $post_id;
	}
	
	/**
	 * @brief Checks if the given member is working on the given project
	 * @param Member
	 * @param Project ID
	 * @return int|boolean (Project ID|false)
	 */
	public static function isMemberWorkingOnProject($member, $pid)
	{
		try
		{
			$query = \OCP\DB::prepare('SELECT `project`.`pid` 
									   FROM `*PREFIX*collaboration_works_on` AS works_on, `*PREFIX*collaboration_project` AS project
									   WHERE `project`.`pid`=? 
									   AND `member`=? 
									   AND `project`.`pid`=`works_on`.`pid`');
									   
			$result = $query->execute(array($pid, $member));
			return ($result->fetchRow());
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * @brief Checks if the given member is working on the given project
	 * @param Member
	 * @param Project title
	 * @return string|boolean (Project title|false)
	 */
	public static function isMemberWorkingOnProjectByTitle($member, $title)
	{
		try
		{
			$query = \OCP\DB::prepare('SELECT `title` 
									   FROM `*PREFIX*collaboration_works_on` AS works_on, `*PREFIX*collaboration_project` AS project
									   WHERE `title`=? 
									   AND `member`=? 
									   AND `project`.`pid`=`works_on`.`pid`');
									   
			$result = $query->execute(array($title, $member));
			return ($result->fetchRow());
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * @brief Checks if the given member created the given project
	 * @param Project ID
	 * @param Member
	 * @return int|boolean (Project ID|false)
	 */
	public static function isProjectCreatedByMember($pid, $member)
	{
		try
		{
			$query = \OCP\DB::prepare('SELECT `pid` FROM `*PREFIX*collaboration_works_on` WHERE `pid`=? AND `member`=? AND `role`=?');
			$result = $query->execute(array($pid, $member, 'Creator'));
		
			return ($result->fetchRow());
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}

	/**
	 * @brief Fetches the list of roles available in the project
	 * @param Project ID
	 * @return Array|boolean (Array containing the list of roles|false)
	 */
	public static function getRoles($pid=-1)
	{
		try
		{
			$query = \OCP\DB::prepare('SELECT DISTINCT(`role`) FROM `*PREFIX*collaboration_works_on`' . (($pid == -1)? '': ' WHERE `pid`=?') . ' ORDER BY `role`');
		
			if($pid == -1)
			{
				$result = $query -> execute();
			}
			else
			{
				$result = $query -> execute(array($pid));
			}
		
			$roles = array();
		
			for($i = 0; $row = $result->fetchRow(); $i++)
			{
				$roles[$i] = $row['role'];
			}
		
			return $roles;
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * @brief Fetches the list of projects in which the given member works on
	 * @param Member
	 * @return Array|boolean (Project Details|false)
	 */
	public static function getProjects($member)
	{
		try
		{
		$query = \OCP\DB::prepare('SELECT `pid`, `title` FROM `*PREFIX*collaboration_project` WHERE `pid` IN 
									   (SELECT `pid` FROM `*PREFIX*collaboration_works_on` WHERE `member`=?) AND `completed`=? 
									   ORDER BY `title`');
			$result = $query -> execute(array($member, false));
		
			$projects = array();
		
			while($row = $result->fetchRow())
			{
				$projects[$row['pid']] = $row['title'];
			}
		
			return $projects;
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * @brief Fetches the list of members working on the given project
	 * @param Project ID
	 * @return Array|boolean (Array containing the list of members|false)
	 */
	public static function getMembers($pid)
	{
		try
		{
		
			$roles = self::getRoles($pid);
			$members = array();
		
			foreach($roles as $role)
			{
				$query = \OCP\DB::prepare('SELECT `member` FROM `*PREFIX*collaboration_works_on` WHERE `pid`=? AND `role`=? ORDER BY `member`');
				$result = $query->execute(array($pid, $role));
			
				$members[$role] = array();
			
				for($i = 0; $row = $result->fetchRow(); $i++)
				{
					$members[$role][$i] = $row['member'];
				}
			}
		
			return $members;
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * @brief Delete a member from a role of a project
	 * @param Project ID from which the role has to be removed
	 * @param Member whose role has to be removed
	 * @param Role to be removed
	 * @param Member who removed the other member from his/her role
	 * @param Whether the calling method has already initiated the (database) transaction
	 * @return int|boolean (ID of the post intimating the role removal|false)
	 */
	public static function deleteMemberRole($pid, $member, $role, $user, $inTransaction=false)
	{
		try
		{
			if(!$inTransaction)
			{
				\OCP\DB::beginTransaction();
			}
		
			// Remove from all roles
			if(is_null($pid) && is_null($role))
			{
				$query = \OCP\DB::prepare('DELETE FROM `*PREFIX*collaboration_works_on` WHERE `member`=?');
				$result = $query->execute(array($member));
			}
			else
			{
				$query = \OCP\DB::prepare('DELETE FROM `*PREFIX*collaboration_works_on` WHERE `pid`=? AND `member`=? AND `role`=?');
				$result = $query->execute(array($pid, $member, $role));
			}
		
			// Member removed from owncloud
			if(is_null($pid))
			{
				OC_Collaboration_Task::removeMembersTasks($member, $user, true);
			}
			else if(!self::isMemberWorkingOnProject($member, $pid))
			{
				OC_Collaboration_Task::unassignMembersTasks($member, $pid, $user, true);
			}
		
			$post_id = OC_Collaboration_Post::createPost('Removed from role', 'You are removed from \'' . $role . '\' role from project \'' . self::getProjectTitle($pid) . '\'.', $user, $pid, 'Role Deletion', array($member), true);

			if(!$inTransaction)
			{		
				\OCP\DB::commit();
			}
		
			return $post_id;
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * @brief Checks if the project has been completed
	 * @param Project ID
	 * @return boolean (true|false)
	 */
	public static function isCompleted($pid)
	{
		try
		{
			$query = \OCP\DB::prepare('SELECT `completed` FROM `*PREFIX*collaboration_project` WHERE `pid`=? AND `completed`=?');
			$result = $query->execute(array($pid, true));
		
			return ($result->fetchRow());
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * @brief Finds the deadline of the project
	 * @param Project ID
	 * @return date|boolean (Deadline date|false)
	 */
	public static function getDeadline($pid)
	{
		try
		{
			$query = \OCP\DB::prepare('SELECT `ending_date` FROM `*PREFIX*collaboration_project` WHERE `pid`=?');
			$result = $query->execute(array($pid));
		
			$row = $result->fetchRow();
		
			return (($row)? $row['ending_date']: false);
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	} 
	
	/**
	 * @brief Fetches the list of members working on the given projects
	 * @param Project IDs
	 * @return Array|boolean (Array containing the list of members|false)
	 */
	public static function getMembersWorkingOnProjects($pids)
	{
		try
		{
			$pids = implode(' OR ', $pids);
		
			$query = \OCP\DB::prepare('SELECT DISTINCT(`member`) FROM `*PREFIX*collaboration_works_on` WHERE `pid`=' . $pids);
		
			$result = $query->execute();
		
			$members = array();
		
			for($i=0; $row = $result->fetchRow(); $i++)
			{
				$members[$i] = $row['member'];
			}
		
			return $members;
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * @brief Checks if the member, who is logged in currently, is the creator of the project
	 * @return boolean (true|false)
	 */
	public static function isAdmin()
	{
		return OC_User::isAdminUser(OC_User::getUser());
	}
	
}

// Instantiated to load the class
new OC_Collaboration_Project();
?>
