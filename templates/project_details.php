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

	print_unescaped($this->inc('tabs'));
?>

<div id="collaboration_content" >
	<h1 id="title">
		<?php p($l->t('Project Details')); ?>
	</h1>

	<table id="details">
		<tr>
			<td>
				<?php p($l->t('Project Title')); ?>
			</td>

			<td>
				:
			</td>
			
			<td>
				<?php 
					p($_['project_details']['title']); 
				?>
			</td>
		</tr>

		<tr>
			<td>
				<?php p($l->t('Description')); ?>
			</td>
		
			<td>
				:
			</td>

			<td>
				<?php p($_['project_details']['description']); ?>
			</td>
		</tr>
	
		<tr>
			<td>
				<?php p($l->t('Deadline')); ?>
			</td>

			<td>
				:
			</td>
	
			<td>
				<?php
					p(OC_Collaboration_Time::convertDBTimeToUIDate($_['project_details']['ending_date'])); 
				?>
			</td>
		</tr>
		
		<tr>
			<td>
				<?php p($l->t('Status')); ?>
			</td>

			<td>
				:
			</td>
	
			<td id="project_status" >
				<?php
					if($_['project_details']['completed'] == true)
					{
						p($l->t('Completed on %s', array($l->l('date', OC_Collaboration_Time::convertDBTimeToUIDate($_['project_details']['last_updated'])))));
							if(isset($_['msg']))
							{
								print_unescaped('<span id="message" > (' . $_['msg'] . ')</span>');
							}
					}
					else
					{
						p($l->t('In progress'));
					}
				?>
			</td>
		</tr>

		<tr>
			<td>
				<?php p($l->t('Members')); ?>
			</td>

			<td>
				:
			</td>
			
			<td>
			</td>
		</tr>
		
	</table>
	
	<table id="members_list" >
			
		<?php
			$member_role = OC_Collaboration_Project::getMembers($_['project_details']['pid']);
			
			if(count($member_role) != 0)
			{
		?>
				<tr>
					<th><?php p($l->t('Role')); ?></th>
					<th><?php p($l->t('Member(s)')); ?></th>
				</tr>
		<?
			}
			
			foreach($member_role as $role => $members)
			{
				print_unescaped('<tr><td>' . $role . '</td><td>');
		
				foreach($members as $index => $member)
				{
					print_unescaped($member . '<br />');
				}
		
				print_unescaped('</td></tr>');
			}
	
		?>
	</table>
	
	<?php
		if(OC_Collaboration_Project::isProjectCreatedByMember($_['project_details']['pid'], OC_User::getUser()))
		{
	?>
			<form action="<?php print_unescaped(OCP\Util::linkToRoute('collaboration_route', array('rel_path'=>'update_project'))); ?>" method="post" >
				<input type="hidden" name="pid" value="<?php p($_['project_details']['pid']); ?>" />
				<input type="submit" value="<?php p($l->t('Edit')); ?>" id="edit_project_btn" disabled />
			</form>
	<?php
		}
	?>
</div>
