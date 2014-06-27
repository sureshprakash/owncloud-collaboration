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
 * This class manages the mail notifications
 */ 

class OC_Collaboration_Mail
{
	/**
	 * @brief Send an email while creating a project
	 * @param Title of the project
	 * @param List of members assigned to that project along with their roles in an associated array
	 */
	public static function sendProjectAssignmentMail($title, $details)
	{
		try
		{
			$member_role = array();
			$member_email = array();
		
			$cnt = count($details);
		
			for($i = 0; $i < $cnt; $i++)
			{
				$member_role[$details[$i]['member']][count($member_role[$details[$i]['member']])] = $details[$i]['role'];
				$member_email[$details[$i]['member']] = $details[$i]['email'];
			}
		
			$subject = 'You are assigned to the project \''.$title.'\'';
		
			foreach($member_role as $member => $roles)
			{
				$message = 'Hello '.$member.',';
				$message .= '<br /><p style="text-indent: 50px;" >';
				$message .= 'You are assigned to the project \''.$title.'\' with the following role(s).';
			
				$i = 1;
				foreach($roles as $role)
				{
					$message .= '<br />' . $i++ . ') ' . $role;
				}
			
				$message .= '<br /><br />';
				$message .= 'For further details, logon to your owncloud account.';
				$message .= '<br /><br />';
			
				OC_Mail::send($member_email[$member], $member, $subject, $message, OC_Config::getValue('mail_smtpname', ''), 'Owncloud Collaboration App', true);
			}
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}

	/**
	 * @brief Send an email while creating and assigning a task
	 * @param Title of the task
	 * @param Task description
	 * @param Project ID to which the task belongs
	 * @param To whom the task is assigned
	 * @param Deadline for completing the task
	 */
	public static function sendTaskCreationMail($title, $desc, $pid, $member, $deadline)
	{
		try
		{
			$subject = 'You are assigned a task \''.$title.'\'';
		
			$message = 'Hello '.$member.',';
			$message .= '<br /><p style="text-indent: 50px;" >';
			$message .= 'You are assigned to the task \''.$title.'\' under the project \'' . OC_Collaboration_Project::getProjectTitle($pid) . '\'.';
			$message .= '<br /><p style="text-align: justify;" ><span style="font-weight: bold;" >';
			$message .= 'Description: ';
			$message .= '</span>'.$desc.'<br /><br /><span style="font-weight: bold;" >';
			$message .= 'Deadline: ';
			$message .= '</span>'.$deadline.'</p><br /><br />';
			$message .= 'For further details, logon to your owncloud account.';
			$message .= '<br /><br />';
			
			OC_Mail::send(OC_Preferences::getValue($member, 'settings', 'email'), $member, $subject, $message, OC_Config::getValue('mail_smtpname', ''), 'Owncloud Collaboration App', true);
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
}

// Instantiated to load the class
new OC_Collaboration_Mail();
?>
