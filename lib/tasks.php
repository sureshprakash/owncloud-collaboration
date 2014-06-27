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
 * This class manages the tasks
 */ 

class OC_Collaboration_Task
{
	/**
	 * @brief Finds the priority string corresponding to the priority value
	 * @param Priority value
	 * @return string (Priority level)
	 */
	public static function getPriorityString($num)
	{
		$l = OC_L10N::get('collaboration');
	
		switch($num)
		{
			case 1:
				return $l->t('Very High');
			case 2:
				return $l->t('High');
			case 3: 
				return $l->t('Normal');
			case 4:
				return $l->t('Low');
			case 5:
				return $l->t('Very Low');
			default:
				return false;
		}
	
	}
	
	/**
	 * @brief Provides the status in readable format
	 * @param Task status
	 * @param Member who updated the task
	 * @param Member who created the task
	 * @return string (Priority status in readable form)
	 */
	public static function getStatusInFormat($status, $member, $creator)
	{
		if(!is_null($member))
		{
			if(strcasecmp($status, 'In Progress') == 0 || 
				strcasecmp($status, 'Held') == 0 ||
				strcasecmp($status, 'Completed') == 0)
			{
				return $status . ' by ' . $member;
			}
		}
	
		if(!is_null($creator))
		{
			if(strcasecmp($status, 'Created') == 0 ||
				strcasecmp($status, 'Cancelled') == 0 || 
				strcasecmp($status, 'Verified') == 0)
			{
				return $status . ' by ' . $creator;
			}
		}
	
		return $status;
	}
	
	/**
	 * @brief Finds the list of next possible states
	 * @param Current status
	 * @param Type of user
	 * @return Array (List of next events and states)
	 */
	public static function getEventStatus($cstatus, $user_type)
	{
		$states = array();

		if(strcasecmp($user_type, 'Creator') == 0)
		{
			switch($cstatus)
			{
				case 'Created':
					$states = array(
								'Assign' => 'In Progress'
								);
					break;
				
				case 'Completed':
					$states = array(
								'Reject' => 'In Progress',
								'Accept' => 'Verified'
								);
					break;
				
				case 'Held':
					$states = array(
								'Cancel' => 'Cancelled'
								);
					break;
				
				case 'In Progress':
					$states = array(
								'Cancel' => 'Cancelled'
								);
					break;
				
				case 'Unassigned':
					$states = array(
								'Cancel' => 'Cancelled',
								'Assign' => 'In Progress'
								);
					break;
				
			}	
		}
		else if(strcasecmp($user_type, 'Performer') == 0)
		{
			switch($cstatus)
			{
				case 'In Progress':
					$states = array(
								'Finish' => 'Completed',
								'Hold' => 'Held'
								);
					break;
				
				case 'Held':
					$states = array(
								'Resume' => 'In Progress'
								);
					break;
				
			}	
		}
	
		return $states;
	}
	
	/**
	 * @brief Finds the task status from the event by task performer
	 * @param Event name
	 * @return string (Status)
	 */
	public static function getStatusFromPerformerEvent($event)
	{
		switch($event)
		{
			case 'Finish':
				return 'Completed';
			
			case 'Hold':
				return 'Held';
			
			case 'Resume':
				return 'In Progress';
		}
	
		return $event;
	}
	
	/**
	 * @brief Performs the translation of the event
	 * @param Event name
	 * @return string (Translated (localized) event name)
	 */
	public static function translateEvent($ev)
	{
		$l = OC_L10N::get('collaboration');
	
		switch($ev)
		{
			case 'Cancel':
				return $l->t('Cancel');
			
			case 'Finish':
				return $l->t('Finish');
			
			case 'Hold':
				return $l->t('Hold');
			
			case 'Assign':
				return $l->t('Assign');
			
			case 'Resume':
				return $l->t('Resume');
		}
	
		return $ev;
	}
	
	/**
	 * @brief Creates a new task with the given title and contents
	 * @param Title of the task
	 * @param Description of the task
	 * @param Member who created the task
	 * @param Project to which the task corresponds
	 * @param Priority of the task
	 * @param Task deadline
	 * @param Task initial status
	 * @param Member to whom the task is assigned
	 * @return int|boolean (Task ID|false)
	 */
	public static function createTask($title, $desc, $creator, $pid, $priority, $end_time, $status, $member=NULL)
	{
			$tid = NULL;
		
			$deadline = explode(' ', $end_time);
		
			try
			{
				\OCP\DB::beginTransaction();
			
				$end_date = OC_Collaboration_Time::convertUITimeToDBTime($end_time . ':00');
			
				$query = OCP\DB::prepare('INSERT INTO `*PREFIX*collaboration_task`(`title`, `description`, `creator`, `pid`, `priority`, `starting_time`, `ending_time`) VALUES(?, ?, ?, ?, ?, CURRENT_TIMESTAMP, ?)');
		
				$result = $query->execute(array($title, $desc, $creator, $pid, $priority, $end_date));
		
				$tid = OCP\DB::insertid('*PREFIX*collaboration_task');
		
				$query = OCP\DB::prepare('INSERT INTO `*PREFIX*collaboration_task_status`(`tid`, `status`, `last_updated_time`, `member`) VALUES(?, ?, CURRENT_TIMESTAMP, ?)');
				$result = $query->execute(array($tid, $status, $member));
			
				if(is_null($member))
				{
					OC_Collaboration_Post::createPost('Task Unassigned', 'The task \''.$title.'\' has been created with deadline '.OC_Collaboration_Time::convertToFullDate($deadline[0]) . ' and is not yet assigned to any member.', $creator, $pid, 'Task Unassigned', array($creator), true, $tid);
				}
				else
				{
					OC_Collaboration_Post::createPost('Task Assigned', 'The task \''.$title.'\' has been assigned with deadline '.OC_Collaboration_Time::convertToFullDate($deadline[0]) . '.', $creator, $pid, 'Task Assigned', array($member), true, $tid);
				}
		
				\OCP\DB::commit();
			}
			catch(\Exception $e)
			{
				OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
				return false;
			}

			return $tid;
	}
	
	/**
	 * @brief Adds an event ID to the given task
	 * @param Task ID
	 * @param Event ID
	 */
	public static function addEvent($tid, $eventid)
	{
		try
		{
			$query = OCP\DB::prepare('UPDATE `*PREFIX*collaboration_task` SET `event_id`=? WHERE `tid`=?');
			$result = $query->execute(array($eventid, $tid));
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}

	/**
	 * @brief Modifies the given task with the given title and contents
	 * @param Title of the task
	 * @param Description of the task
	 * @param Member who created the task
	 * @param Project to which the task corresponds
	 * @param Priority of the task
	 * @param Task deadline
	 * @param Task status
	 * @param Member to whom the task is assigned
	 * @param Reason for changing the task status
	 * @return int|boolean (Task ID|false)
	 */	
	public static function updateTask($tid, $title, $desc, $creator, $pid, $priority, $end_time, $status, $member=NULL, $reason=NULL)
	{
		$deadline = explode(' ', $end_time);
		
		try
		{
			\OCP\DB::beginTransaction();
			
			$end_date = OC_Collaboration_Time::convertUITimeToDBTime($end_time . ':00');
			
			$query = OCP\DB::prepare('UPDATE `*PREFIX*collaboration_task` SET `title`=?, `description`=?, `priority`=?, `ending_time`=? WHERE `tid`=?');
		
			$result = $query->execute(array($title, $desc, $priority, $end_date, $tid));
		
			if(isset($status) && !is_null($status))
			{
				self::changeStatus($tid, $title, $status, $creator, $member, $reason, true);
			}
						
			if(!is_null($member))
			{
				OC_Collaboration_Post::createPost('Task Assigned', 'The task \''.$title.'\' has been assigned with deadline '.OC_Collaboration_Time::convertToFullDate($deadline[0]) . '.', $creator, $pid, 'Task Assigned', array($member), true, $tid);
			}
		
			\OCP\DB::commit();
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
		
		return $tid;
	}
	
	/**
	 * @brief Changes the status of the task
	 * @param Title ID
	 * @param Title of the task
	 * @param Task modified status
	 * @param Member who created the task
	 * @param Member who modified the task status
	 * @param Reason for status change
	 * @param Whether the calling method has already initiated the (database) transaction
	 * @return boolean (true|false)
	 */
	public static function changeStatus($tid, $title, $status, $creator, $member=NULL, $reason=NULL, $inTransaction=false)
	{
		try
		{
			if(!$inTransaction)
			{
				\OCP\DB::beginTransaction();
			}
			
			if(strcmp($status, 'Unassigned') == 0)
			{
				$member = NULL;
			}
			else
			{
				$member = self::getWorkingMember($tid);
			}
			
			$query = OCP\DB::prepare('INSERT INTO `*PREFIX*collaboration_task_status`(`tid`, `status`, `member`, `reason`, `last_updated_time`) VALUES(?, ?, ?, ?, CURRENT_TIMESTAMP)');
		
			$result = $query->execute(array($tid, $status, $member, $reason));
		
			$post_to = array();
			
			if(is_null($member))
			{
				$post_to = array(self::getTaskCreator($tid));
			}
			else
			{
				$post_to = array(self::getTaskCreator($tid), $member);
			}
			
			OC_Collaboration_Post::createPost('Task Status Changed', 'The status of the task \''.$title.'\' has been changed. Current status: '. self::getStatusInFormat($status, $member, self::getTaskCreator($tid)) . '.', $creator, self::getProjectId($tid), 'Task Status Updation', $post_to, true, $tid);
		
			if(!$inTransaction)
			{
				\OCP\DB::commit();
			}
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
		
		return true;
	}
	
	/**
	 * @brief Fetches the list of tasks
	 * @param Filters based on project, status and the member assigned
	 * @param The starting task serial number
	 * @param The number of tasks to be fetched
	 */
	public static function readTasks($args=array(), $start=0, $count=20)
	{
		try
		{
			$project = NULL;
			$status = NULL;
			$assigned_to = NULL;
			$assigned_by = NULL;
		
			if(isset($args['project']) && !is_null($args['project']))
			{
				$project = $args['project'];
			}
		
			if(isset($args['status']) && !is_null($args['status']))
			{
				$status = $args['status'];
			}
		
			if(isset($args['assigned_to']) && !is_null($args['assigned_to']))
			{
				$assigned_to = $args['assigned_to'];
			}
		
			if(isset($args['assigned_by']) && !is_null($args['assigned_by']))
			{
				$assigned_by = $args['assigned_by'];
			}
		
			$sql = 	'SELECT `task`.`tid`, `task`.`title` AS title, `project`.`title` AS proj_title, `task`.`description`, `creator`, 					`task`.`pid`, `priority`, `ending_time`, `status`, `member`, `last_updated_time`, `reason` 
					FROM `*PREFIX*collaboration_task` AS task, `*PREFIX*collaboration_task_status` AS tstatus, 
					`*PREFIX*collaboration_project` AS project 
				     WHERE `task`.`tid`=`tstatus`.`tid` 
			  	     AND `task`.`pid`=`project`.`pid`
				     AND `project`.`completed`=false
				     AND (' .
				     ((is_null($assigned_by))? '': '`task`.`creator`=?') .
				     ((!is_null($assigned_by) && !is_null($assigned_to))? ' OR ': '') .
				     ((is_null($assigned_to))? '': '`tstatus`.`member`=?') . ')' .
				     ((is_null($project))? '': ' AND `project`.`title`=?') .
				     ((is_null($status))? '': ' AND `tstatus`.`status`=?') .
				     ' AND `tstatus`.`last_updated_time`=
				     (SELECT MAX(`last_updated_time`) 
				     FROM `*PREFIX*collaboration_task_status` 
				     WHERE `tid`=`task`.`tid`)
				     ORDER BY `ending_time` ASC
				     LIMIT ' . $start . ', ' . $count;
						   
			$query = \OCP\DB::prepare($sql);
			
			$params = array();	
			$i = 0;
			if(!is_null($assigned_by))
			{
				$params[$i++] = $assigned_by;
			}
		
			if(!is_null($assigned_to))
			{
				$params[$i++] = $assigned_to;
			}

			if(!is_null($project))
			{
				$params[$i++] = $project;
			}
		
			if(!is_null($status))
			{
				$params[$i++] = $status;
			}
				   
			$result = $query ->execute($params);
			$res = $result->fetchAll();
		
			return $res;
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * @brief Cancel all the tasks created by the given member
	 * @param Member whose created tasks have to be cancelled
	 * @param Project ID
	 * @param Member who removes the other member
	 * @param Whether the calling method has already initiated the (database) transaction
	 */
	public static function cancelMembersTasks($member, $pid, $remover, $inTransaction)
	{
		try
		{
			if(!$inTransaction)
			{
				OCP\DB::beginTransaction();
			}
		
			$query = \OCP\DB::prepare('SELECT `tstatus`.`tid`, `task`.`title`
											FROM `*PREFIX*collaboration_task_status` AS tstatus, 
											     `*PREFIX*collaboration_task` AS task,
											     `*PREFIX*collaboration_project` AS project
											WHERE ' . ((is_null($pid))? '': ' `project`.`pid`=? AND ') . '`project`.`completed`=false
											AND `tstatus`.`tid`=`task`.`tid` AND `task`.`pid`=`project`.`pid`
											AND `task`.`creator`=? AND `tstatus`.`status`<>\'Verified\' AND `tstatus`.`last_updated_time`=
											(SELECT MAX(`last_updated_time`) 
											 FROM `*PREFIX*collaboration_task_status` 
											 WHERE `tid`=`tstatus`.`tid`)');
			
			$args = array();
		
			if(is_null($pid)) // Member removed from owncloud
			{
				$args[0] = $member;
			
				$qry = OCP\DB::prepare('UPDATE `*PREFIX*collaboration_task` SET `creator`=NULL WHERE `creator`=?');
				$qry->execute(array($member));
			}
			else
			{
				$args[0] = $pid;
				$args[1] = $member;
			}
		
			$result = $query->execute($args);
		
			$reason = 'User removed from ' . (is_null($pid)? 'owncloud': OC_Collaboration_Project::getProjectTitle($pid));
		
			while($row = $result->fetchRow())
			{
				self::changeStatus($row['tid'], $row['title'], 'Cancelled', $remover, NULL, $reason, true);
			}
		
			if(!$inTransaction)
			{
				OCP\DB::commit();
			}
		}
		catch(\Exception $e)
			{
				OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
				return false;
			}
	}

	/**
	 * @brief Unassign all the tasks performed by the given member
	 * @param Member whose tasks have to be unassigned
	 * @param Project ID
	 * @param Member who removes the other member
	 * @param Whether the calling method has already initiated the (database) transaction
	 */
	public static function unassignMembersTasks($member, $pid, $remover, $inTransaction)
	{
		try
		{
			if(!$inTransaction)
			{
				OCP\DB::beginTransaction();
			}
		
			$query = \OCP\DB::prepare('SELECT `tstatus`.`tid`, `task`.`title`
											FROM `*PREFIX*collaboration_task_status` AS tstatus, 
											     `*PREFIX*collaboration_task` AS task,
											     `*PREFIX*collaboration_project` AS project
											WHERE ' . ((is_null($pid))? '': ' `project`.`pid`=? AND ') . '`project`.`completed`=false
											AND `tstatus`.`tid`=`task`.`tid` AND `task`.`pid`=`project`.`pid`
											AND (`status`=\'In Progress\' OR `status`=\'Held\') AND `member`=? AND `tstatus`.`last_updated_time`=
											(SELECT MAX(`last_updated_time`) 
											 FROM `*PREFIX*collaboration_task_status` 
											 WHERE `tid`=`tstatus`.`tid`)');
			
			$args = array();
		
			if(is_null($pid)) // Member removed from owncloud
			{							
				$args[0] = $member;
			}
			else
			{
				$args[0] = $pid;
				$args[1] = $member;
			}
		
			$result = $query->execute($args);
		
			$reason = 'User removed from ' . ((is_null($pid))? 'owncloud': OC_Collaboration_Project::getProjectTitle($pid));
		
			while($row = $result->fetchRow())
			{
				self::changeStatus($row['tid'], $row['title'], 'Unassigned', $remover, NULL, $reason, true);
			}
		
			if(!$inTransaction)
			{
				OCP\DB::commit();
			}
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * @brief Remove all the tasks performed or created by the given member
	 * @param Member whose tasks have to be removed
	 * @param Member who removes the other member
	 * @param Whether the calling method has already initiated the (database) transaction
	 */
	public static function removeMembersTasks($member, $user, $inTransaction)
	{
		try
		{
			self::unassignMembersTasks($member, NULL, $user, $inTransaction);
			self::cancelMembersTasks($member, NULL, $user, $inTransaction);
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}	

	/**
	 * @brief Finds the project to which the task corresponds to
	 * @param Task ID
	 * @return int|boolean (Project ID|false)
	 */
	public static function getProjectId($tid)
	{
		try
		{
	
			$query = OCP\DB::prepare('SELECT `pid` FROM `*PREFIX*collaboration_task` WHERE `tid`=?');
			$result = $query->execute(array($tid));
			$row = $result->fetchRow();
			return $row? $row['pid']: false;
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}

	/**
	 * @brief Finds the member who created the task
	 * @param Task ID
	 * @return string|boolean (Member|false)
	 */
	public static function getTaskCreator($tid)
	{
		try
		{
			$query = OCP\DB::prepare('SELECT `creator` FROM `*PREFIX*collaboration_task` WHERE `tid`=?');
			$result = $query->execute(array($tid));
			$row = $result->fetchRow();
			return $row? $row['creator']: false;
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * @brief Finds the title of the given task
	 * @param Task ID
	 * @return string|boolean (Task title|false)
	 */
	public static function getTaskTitle($tid)
	{
		try
		{
			$query = OCP\DB::prepare('SELECT `title` FROM `*PREFIX*collaboration_task` WHERE `tid`=?');
			$result = $query->execute(array($tid));
			$row = $result->fetchRow();
			return $row? $row['title']: false;
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * @brief Finds the member who is currently performing the task
	 * @param Task ID
	 * @return string|boolean (Member|false)
	 */
	public static function getWorkingMember($tid)
	{
		try
		{
			$query = OCP\DB::prepare('SELECT `member` FROM `*PREFIX*collaboration_task_status` WHERE `last_updated_time`=
										(SELECT MAX(`last_updated_time`) FROM `*PREFIX*collaboration_task_status` WHERE `tid`=?)');
			$result = $query->execute(array($tid));
			$row = $result->fetchRow();
			return $row? $row['member']: false;
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * @brief Provides the details about the task
	 * @param Task ID
	 * @return Array|boolean (Array containing the task details|false)
	 */
	public static function readTask($tid)
	{
		try
		{
			$query = OCP\DB::prepare('SELECT `task`.`tid`, `project`.`title` AS proj_title, `task`.`pid`, `task`.`title`, `task`.`description`,
									 `priority`, `ending_time`, `status`, `member`, `task`.`creator`									  
									 FROM `*PREFIX*collaboration_task` AS task, 
									      `*PREFIX*collaboration_project` AS project,
									      `*PREFIX*collaboration_task_status` AS tstatus
									 WHERE `task`.`pid`=`project`.`pid` 
									 AND `task`.`tid`=`tstatus`.`tid` 
									 AND `task`.`tid`=? 
									 AND `tstatus`.`last_updated_time`=
									 	(SELECT MAX(`last_updated_time`) 
									 	FROM `*PREFIX*collaboration_task_status` 
									 	WHERE `tid`=`task`.`tid`)');
			$result = $query->execute(array($tid));
			$row = $result->fetchRow();
		
			return $row? $row: false;
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * @brief Provides the history of status changes over the task
	 * @param Task ID
	 * @return Array|boolean (Array containing status change details|false)
	 */
	public static function readHistory($tid)
	{
		try
		{
			$query = OCP\DB::prepare('SELECT `status`, `member`, `reason` AS comment, `last_updated_time` AS time FROM `*PREFIX*collaboration_task_status` WHERE `tid`=? ORDER BY `time` DESC');
			$result = $query->execute(array($tid));
			$row = $result->fetchAll();
		
			return $row? $row: false;
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * @brief Calculates the number of tasks currently being performed by each member working in the given project
	 * @param Project ID
	 * @return Array|boolean (Array containing the list of members and the number of tasks being performed by them|false)
	 */
	public static function getTasksCount($pid)
	{
		try
		{
			$total = OCP\DB::prepare('SELECT `mem`, COALESCE(`num_task`, 0) AS count 
									  FROM 
									  		(SELECT `member`, COUNT(`tid`) AS num_task 
											FROM `*PREFIX*collaboration_task_status` AS tstatus 
											WHERE (`status`=\'In Progress\' OR `status`=\'Held\') 
											AND `last_updated_time`=
												(SELECT MAX(`last_updated_time`) 
												FROM `*PREFIX*collaboration_task_status` 
												WHERE `tid`=`tstatus`.`tid`) 
											GROUP BY `member`) AS task_count 
									  RIGHT JOIN 
									  		(SELECT DISTINCT(`member`) AS mem 
									  		FROM `*PREFIX*collaboration_works_on` 
									  		WHERE `pid`=?) AS works_on 
									  ON (`mem`=`member`)
									  ORDER BY `count`, `mem`');
			$total_result = $total->execute(array($pid));
				
			$project = OCP\DB::prepare('SELECT `mem`, COALESCE(`num_task`, 0) AS count 
									  FROM 
									  		(SELECT `member`, COUNT(`tstatus`.`tid`) AS num_task 
									  		FROM `*PREFIX*collaboration_task_status` AS tstatus, 
									  			 `*PREFIX*collaboration_task` AS tsk 
									  		WHERE `tstatus`.`tid`=`tsk`.`tid` 
									  		AND `tsk`.`pid`=? 
									  		AND (`status`=\'In Progress\' OR `status`=\'Held\') 
									  		AND `last_updated_time`=
									  			(SELECT MAX(`last_updated_time`) 
									  			FROM `*PREFIX*collaboration_task_status` 
									  			WHERE `tid`=`tstatus`.`tid`) 
									  		GROUP BY `member`) AS task_count 
									  	RIGHT JOIN 
									  		(SELECT DISTINCT(`member`) AS mem 
									  		FROM `*PREFIX*collaboration_works_on`
									  		WHERE `pid`=?) AS works_on
									  	ON (`mem`=`member`)
									  	ORDER BY `count`, `mem`');
			$project_result = $project->execute(array($pid, $pid));
			
			$mem_cnt = array(array());
			
			// Both conditions will fail at the same time
			while(($tot = $total_result->fetchRow()) && ($pro = $project_result->fetchRow()))
			{	
				$mem_cnt[$pro['mem']]['proj_cnt'] = $pro['count'];
				$mem_cnt[$tot['mem']]['tot_cnt'] = $tot['count'];
			}
			
			return $mem_cnt;
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * @brief Checks if the given project has pending tasks
	 * @param Project ID
	 * @return boolean (true|false)
	 */
	public static function hasPendingTasks($pid)
	{
		try
		{
			$query = OCP\DB::prepare('SELECT `task`.`tid` 
									  FROM `*PREFIX*collaboration_task` AS task,
									  	   `*PREFIX*collaboration_task_status` AS tstatus
									  WHERE `task`.`tid`=`tstatus`.`tid`
									  AND `task`.`pid`=?
									  AND `last_updated_time`=
									  		(SELECT MAX(`last_updated_time`)
									  		FROM `*PREFIX*collaboration_task_status`
									  		WHERE `tid`=`tstatus`.`tid`)
									  AND `tstatus`.`status`<>\'Verified\'
									  AND `tstatus`.`status`<>\'Cancelled\'');
			$result = $query->execute(array($pid));
			$row = $result->fetchRow();
			
			return $row? true: false;
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}						  
								  
	}
	
}

// Instantiated to load the class
new OC_Collaboration_Task();
