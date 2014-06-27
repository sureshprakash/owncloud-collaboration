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
 * This class manages the calendar integration
 */ 

class OC_Collaboration_Calendar
{
	/**
	 * @brief Finds the calendar (ID) corresponding to the project
	 * @param Project ID
	 * @return int|boolean (Calendar ID|false)
	 */
	public static function getCalendarId($pid)
	{
		try
		{
			$query = OCP\DB::prepare('SELECT `calendar_id` FROM `*PREFIX*collaboration_project` WHERE `pid`=?');
			$result = $query->execute(array($pid));
			$row = $result->fetchRow();
		
			return $row? $row['calendar_id']: false;
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}

	/**
	 * @brief Finds the event (ID) corresponding to the task
	 * @param Task ID
	 * @return int|boolean (Event ID|false)
	 */	
	public static function getEventId($tid)
	{
		try
		{
			$query = OCP\DB::prepare('SELECT `event_id` FROM `*PREFIX*collaboration_task` WHERE `tid`=?');
			$result = $query->execute(array($tid));
			$row = $result->fetchRow();
		
			return $row? $row['event_id']: false;
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * @brief Finds the starting date and time of the event
	 * @param Event ID
	 * @return date|boolean (timestamp|false)
	 */
	public static function getEventStartTime($eid)
	{
		try
		{
			$query = OCP\DB::prepare('SELECT `startdate` FROM `*PREFIX*clndr_objects` WHERE `id`=?');
			$result = $query->execute(array($eid));
			$row = $result->fetchRow();
		
			return $row? $row['startdate']: false;
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * @brief Finds the last modified date and time of the event
	 * @param Event ID
	 * @return date|boolean (timestamp|false)
	 */
	public static function getModifiedTime($eid)
	{
		try
		{
			$query = OCP\DB::prepare('SELECT `lastmodified` FROM `*PREFIX*clndr_objects` WHERE `id`=?');
			$result = $query->execute(array($eid));
			$row = $result->fetchRow();
		
			return $row? $row['lastmodified']: false;
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
}

// Instantiated to load the class
new OC_Collaboration_Calendar();
