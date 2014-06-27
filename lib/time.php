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
 * This class manages the time unit conversions
 */ 
	
class OC_Collaboration_Time
{
	/**
		Timestamp formats can be obtained from
		http://in2.php.net/manual/en/function.date.php
	*/
	public static function convertTimestamp($datetime, $from, $to) 
	{
		try
		{
			return DateTime::createFromFormat($from, $datetime)->format($to);
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * Change time format from 'Y-m-d H:i:s' to 'm/d/Y H:i:s'
	 */
	public static function convertDBTimeToUITime($datetime)
	{
		try
		{
			return self::convertTimestamp($datetime, 'Y-m-d H:i:s', 'm/d/Y H:i:s');
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * Change time format from 'Y-m-d H:i:s' to 'm/d/Y H:i'
	 */
	public static function convertDBTimeToUITimeShort($datetime)
	{
		try
		{
			return self::convertTimestamp($datetime, 'Y-m-d H:i:s', 'm/d/Y H:i');
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * Change time format from 'm/d/Y H:i' to 'Y-m-d H:i'
	 */
	public static function convertUITimeShortToDBTimeShort($datetime)
	{
		try
		{
			return self::convertTimestamp($datetime, 'm/d/Y H:i', 'Y-m-d H:i');
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * Change time format from 'm/d/Y H:i:s' to 'Y-m-d H:i:s'
	 */
	public static function convertUITimeToDBTime($datetime)
	{
		try
		{
			return self::convertTimestamp($datetime, 'm/d/Y H:i:s', 'Y-m-d H:i:s');
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * Change time format from 'm/d/Y' to 'F d, Y'
	 */
	public static function convertToFullDate($date)
	{
		try
		{
			return self::convertTimestamp($date, 'm/d/Y', 'F d, Y');
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * Change time format from 'Y-m-d H:i:s' to 'm/d/Y'
	 */
	public static function convertDBTimeToUIDate($datetime)
	{
		try
		{
			return self::convertTimestamp($datetime, 'Y-m-d H:i:s', 'm/d/Y');
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
}

// Instantiated to load the class
new OC_Collaboration_Time();
?>
