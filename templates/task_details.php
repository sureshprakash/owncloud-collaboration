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
		<?php p($l->t('Task details')); ?>
	</h1>
	
	<table id="details">
				<tr>
					<td>
						<?php p($l->t('Project')); ?>
					</td>
			
					<td>
						:
					</td>
			
					<td>
						<?php p($_['task_details']['proj_title']); ?>
					</td>
		
				</tr>

				<tr>
					<td>
						<?php p($l->t('Task Title')); ?>
					</td>

					<td>
						:
					</td>

					<td>
						<?php p($_['task_details']['title']); ?>
					</td>
				</tr>

				<tr>
					<td>
						<?php p($l->t('Task Description')); ?>
					</td>
			
					<td>
						: 
					</td>

					<td>
						<?php p($_['task_details']['description']); ?>
					</td>
				</tr>
				
				<tr>
					<td>
						<?php p($l->t('Priority')); ?>
					</td>

					<td>
						: 
					</td>
	
					<td>
						<?php p(OC_Collaboration_Task::getPriorityString($_['task_details']['priority'])); ?>
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
						<?php p(OC_Collaboration_Time::convertDBTimeToUITime($_['task_details']['ending_time'])); ?>
					</td>
				</tr>
				
				<tr>
					<td>
						<?php p($l->t('Task Status')); ?>
					</td>

					<td>
						:
					</td>
	
					<td id="current_task_status" >
						<?php p($_['status_details'][0]['status']); ?>
					</td>
				</tr>
			</table>
			
			<?php
				if(isset($_['msg']))
				{
					print_unescaped('<div id="message" > (' . $_['msg'] . ')</div>');
				}
				
				p($l->t('Task History:'));
			?>
	
	<table id="status_table">
			
				<tr>
					<th><?php p($l->t('Status')); ?></th>
					<th><?php p($l->t('Time')); ?></th>
					<th><?php p($l->t('Comment')); ?></th>
				</tr>
		<?php			
			for($i=0; $i<count($_['status_details']); $i++)
			{
				print_unescaped('<tr><td>' . OC_Collaboration_Task::getStatusInFormat($_['status_details'][$i]['status'], $_['status_details'][$i]['member'], $_['task_details']['creator']) . '</td><td>' . OC_Collaboration_Time::convertDBTimeToUITime($_['status_details'][$i]['time']) . '</td><td>' . $_['status_details'][$i]['comment'] . '</td></tr>');
			}
	
		?>
	</table>
		
	<?php
		if(strcasecmp(OC_Collaboration_Task::getTaskCreator($_['task_details']['tid']), OC_User::getUser()) == 0)
		{
	?>
			<form action="<?php print_unescaped(OCP\Util::linkToRoute('collaboration_route', array('rel_path'=>'update_task'))); ?>" method="post" >
				<input type="hidden" name="tid" value="<?php p($_['task_details']['tid']); ?>" />
				<input type="submit" value="<?php p($l->t('Edit')); ?>" id="edit_task_btn" disabled />
			</form>
	<?php
		}
	?>
</div>
