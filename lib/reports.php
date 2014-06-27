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
 * This class supports report generation
 */ 

class OC_Collaboration_Report
{
	/**
	 * @brief Provides details for generating the task status report
	 * @param Project ID
	 */
	public static function getTaskStatus($pid)
	{
		try
		{	
			$query = OCP\DB::prepare('SELECT `status`, COUNT(`status`) AS count
									  FROM `*PREFIX*collaboration_task_status` AS tstatus 
									  JOIN `*PREFIX*collaboration_task` AS task 
									  ON (`tstatus`.`tid`=`task`.`tid`) 
									  WHERE `task`.`pid`=? 
									  AND `last_updated_time`=
									  		(SELECT MAX(`last_updated_time`) 
									  		FROM `*PREFIX*collaboration_task_status` 
									  		WHERE `tid`=`tstatus`.`tid`) 
									  GROUP BY `status`');
			$result = $query->execute(array($pid));
			
			$status = array();
			
			while($row = $result->fetchRow())
			{
				$status[$row['status']] = $row['count'];
			}
			
			return $status;
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * @brief Provides details for generating the project timeline
	 * @param Project ID
	 */
	public static function getProjectStatus($pid)
	{
		try
		{
			$query = OCP\DB::prepare('SELECT `finished`.`count` AS finished, `pending`.`count` AS pending, 
									 `starting_date`, `ending_date`, now() as now, `completed`, `last_updated` 
									 FROM (SELECT COUNT(`status`) AS count
									  	  FROM `*PREFIX*collaboration_task_status` AS tstatus 
									  	  JOIN `*PREFIX*collaboration_task` AS task 
									  	  ON (`tstatus`.`tid`=`task`.`tid`) 
									  	  WHERE `task`.`pid`=? 
										  AND `last_updated_time`=
										  		(SELECT MAX(`last_updated_time`) 
										  		FROM `*PREFIX*collaboration_task_status` 
										  		WHERE `tid`=`tstatus`.`tid`) 
										  AND `status`=\'Verified\') AS finished,
										  (SELECT COUNT(`status`) AS count
									  	  FROM `*PREFIX*collaboration_task_status` AS tstatus 
									  	  JOIN `*PREFIX*collaboration_task` AS task 
									  	  ON (`tstatus`.`tid`=`task`.`tid`) 
									  	  WHERE `task`.`pid`=? 
										  AND `last_updated_time`=
										  		(SELECT MAX(`last_updated_time`) 
										  		FROM `*PREFIX*collaboration_task_status` 
										  		WHERE `tid`=`tstatus`.`tid`) 
										  AND `status`<>\'Verified\'
										  AND `status`<>\'Cancelled\') AS pending,
										  `*PREFIX*collaboration_project`
									WHERE `pid`=?');
			$result = $query->execute(array($pid, $pid, $pid));
			
			$details = array();
			
			if($row = $result->fetchRow())
			{
				$details['progress'] = self::findProgress($row['starting_date'],  $row['ending_date'], $row['now'], $row['completed']);
				$details['start_date'] = OC_Collaboration_Time::convertDBTimeToUITime($row['starting_date']);
				$details['deadline'] = OC_Collaboration_Time::convertDBTimeToUITime($row['ending_date']);
				$details['updated_time'] = OC_Collaboration_Time::convertDBTimeToUITime($row['last_updated']);
				$details['now'] = OC_Collaboration_Time::convertDBTimeToUITime($row['now']);
				$details['completed'] = $row['completed'];
				$details['num_completed_tasks'] = $row['finished'];
				$details['num_pending_tasks'] = $row['pending'];
			}
			
			return $details;
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * @brief Finds the percentage progress of the project
	 * @param Project starting date
	 * @param Project deadline
	 * @param Today's date
	 * @param Whether or not the project has completed
	 */
	public static function findProgress($start, $end, $now, $completed)
	{
		if($completed)
		{
			return 100;
		}
		
		$ending = DateTime::createFromFormat('Y-m-d H:i:s', $end);
		$starting = DateTime::createFromFormat('Y-m-d H:i:s', $start);
		$current = DateTime::createFromFormat('Y-m-d H:i:s', $now);
		
		if(strcmp($now, $end) > 0)
		{
			return (($ending->diff($starting)->format('%a'))/($current->diff($starting)->format('%a')) * 100);
		}
		else
		{
			return ($current->diff($starting)->format('%a'))/($ending->diff($starting)->format('%a')) * 100;
		}
	}
	
	/**
	 * @brief Provides details for generating the contribution report
	 * @param Project ID
	 */
	public static function getMemberTaskCount($pid)
	{
		try
		{
			$query = OCP\DB::prepare('SELECT `works_on`.`member` AS member, COALESCE(`count`, 0) AS num_tasks 
									 FROM 
									 	(SELECT DISTINCT(`member`) 
									 	FROM `*PREFIX*collaboration_works_on` 
									 	WHERE `pid`=?) AS works_on 
									 LEFT JOIN 
									 	(SELECT `tstatus`.`member`, COUNT(`tstatus`.`tid`) AS count 
									 	FROM `*PREFIX*collaboration_task_status` AS tstatus 
									 	JOIN `*PREFIX*collaboration_task` AS task ON (`task`.`tid`=`tstatus`.`tid`) 
								 	WHERE `task`.`pid`=? 
								 	AND `last_updated_time`=
								 		(SELECT MAX(`last_updated_time`) 
								 		FROM `*PREFIX*collaboration_task_status` 
								 		WHERE `tid`=`tstatus`.`tid`) 
								 	AND `status`=\'Verified\' 
									GROUP BY `member`) AS cnt ON (`cnt`.`member`=`works_on`.`member`) 
									ORDER BY `num_tasks` DESC, `member`');
			$result = $query->execute(array($pid, $pid));
			
			$mem_cnt = array();
			
			while($row = $result->fetchRow())
			{
				$mem_cnt[$row['member']] = $row['num_tasks'];
			}
			
			return $mem_cnt;
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
}

// Instantiated to load the class
new OC_Collaboration_Report();
?>
