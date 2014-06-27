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
 * This class manages the members' skillsets
 */ 
 
class OC_Collaboration_Skillset
{
	/**
	 * @brief Finds the expertise level given the level value
	 * @param Expertise level value
	 * @return string (Expertise level string)
	 */
	public static function getExpertiseString($val)
	{
		$l = OC_L10N::get('collaboration');
		
		switch($val)
		{
			case 1:
				return $l->t('Beginner');
				
			case 2:
				return $l->t('Intermediate');
			
			case 3:
				return $l->t('Expert');
		}
		
		return $val;
	}
	
	/**
	 * @brief Adds skills to the member
	 * @param Member
	 * @param Skill details
	 * @return boolean (true|false)
	 */
	public static function addSkills($user, $skills)
	{
		try
		{
			$query = OCP\DB::prepare('INSERT INTO `*PREFIX*collaboration_skill`(`member`, `skill`, `experience`, `expertise`, `exp_on_date`) 
										VALUES(?, ?, ?, ?, CURRENT_DATE)');
									
			foreach($skills as $skill)
			{
				$query->execute(array($user, $skill['skill'], $skill['experience'], $skill['expertise']));
			}
		
			return true;
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * @brief Fetches the skills of the given member
	 * @param Member
	 * @return Array (Array containing the details of the skills)
	 */
	public static function readSkills($member)
	{
		try
		{
			$query = OCP\DB::prepare('SELECT `skill`, 
			`experience` + (YEAR(NOW()) - YEAR(`exp_on_date`) - (DATE_FORMAT(NOW(), \'%m%d\') < DATE_FORMAT(`exp_on_date`, \'%m%d\'))) AS `experience`, `expertise` FROM `*PREFIX*collaboration_skill` WHERE `member`=? ORDER BY `skill`');
		
			$result = $query->execute(array($member));
		
			return $result->fetchAll();
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * @brief Remove a skill from a member
	 * @param Member from whom the skill has to be removed
	 * @param Skill to be removed from the member
	 * @return boolean (true|false)
	 */
	public static function removeSkill($member, $skill)
	{
		try
		{
			$query = OCP\DB::prepare('DELETE FROM `*PREFIX*collaboration_skill` WHERE `member`=? AND `skill`=?');
			$query->execute(array($member, $skill));
			
			return true;
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * @brief Remove all skills from a member
	 * @param Member from whom the skills have to be removed
	 * @return boolean (true|false)
	 */
	public static function removeSkillsOfMember($member)
	{
		try
		{
			$query = OCP\DB::prepare('DELETE FROM `*PREFIX*collaboration_skill` WHERE `member`=?');
			$query->execute(array($member));
			
			return true;
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
}

// Instantiated to load the class
new OC_Collaboration_Skillset();
