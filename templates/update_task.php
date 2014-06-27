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
		<?php p($_['title']); ?>
	</h1>

	<?php
		if(!isset($_['tid']) && (!isset($_['projects']) || count($_['projects']) == 0))
		{
	?>
			<p>
				<?php p($l->t('Sorry, you are not into any project yet. You can create tasks only after joining a project.')); ?>
			</p>
	<?php		
		}
		else
		{
	?>
		<form action="<?php print_unescaped(OCP\Util::linkToRoute('collaboration_route', array('rel_path' => 'submit_update_task')));?>" method="post" name="task_updation_form">

		<?php
			if(isset($_['tid']))
			{
		?>
		<input type="hidden" id="tid" name="tid" value="<?php p($_['tid']); ?>" />
		<?php
			}
		?>
			<table>
				<tr>
					<td>
						<?php p($l->t('Project')); ?>
					</td>
			
					<td>
						: 
						<?php
							if(!isset($_['tid']))
							{
						?>
							<span class="required">*</span>
						<?php
							}
						?>
					</td>
			
					<td>
						<?php
							if(!isset($_['tid']))
							{
						?>
							<select name="pid" id="project" class="chzen-select" required>
						<?php	
										foreach($_['projects'] as $pid => $ptitle)
										{
											print_unescaped('<option value="' . $pid . '" >' . $ptitle . '</option>');
										}
						?>	
							</select>
						<?php
							}
							else
							{
								print_unescaped('<span>' . $_['task_details']['proj_title'] . '</span>');
								print_unescaped('<input id="project" type="hidden" name="pid" value="' . $_['task_details']['pid'] . '" />');
							}
						?>
						<span id="load_members" > </span>
					</td>
		
				</tr>

				<tr>
					<td>
						<?php p($l->t('Task Title')); ?>
					</td>

					<td>
						: <span class="required">*</span>
					</td>

					<td>
						<input type="text" name="title" pattern="[a-zA-Z]([a-zA-Z0-9]\s?(\-\s)?){2,98}[a-zA-Z0-9]" title="Title can contain alphabets, numbers, spaces and hyphens with 4 to 100 characters. First character should be an alphabet and last one can be an alphabet or a numeral." autocomplete="off" required autofocus <?php if(isset($_['tid'])) print_unescaped('value="'.$_['task_details']['title'].'"'); ?> />
					</td>
				</tr>

				<tr>
					<td>
						<?php p($l->t('Task Description')); ?>
					</td>
			
					<td>
						: <span class="required">*</span>
					</td>

					<td>
						<textarea maxlength="3000" name="description" required ><?php if(isset($_['tid'])) p($_['task_details']['description']); ?></textarea>
					</td>
				</tr>
				
				<tr>
					<td>
						<?php p($l->t('Priority')); ?>
					</td>

					<td>
						: <span class="required">*</span>
					</td>
	
					<td>
						<select name="priority" required >
							<option value="1" <?php if(isset($_['tid']) && $_['task_details']['priority'] == 1) print_unescaped('selected'); ?> ><?php p($l->t('Very High')); ?></option>
							<option value="2" <?php if(isset($_['tid']) && $_['task_details']['priority'] == 2) print_unescaped('selected'); ?> ><?php p($l->t('High')); ?></option>
							<option value="3" <?php if((isset($_['tid']) && $_['task_details']['priority'] == 3) || !isset($_['tid'])) print_unescaped('selected'); ?> ><?php p($l->t('Normal')); ?></option>
							<option value="4" <?php if(isset($_['tid']) && $_['task_details']['priority'] == 4) print_unescaped('selected'); ?> ><?php p($l->t('Low')); ?></option>
							<option value="5" <?php if(isset($_['tid']) && $_['task_details']['priority'] == 5) print_unescaped('selected'); ?> ><?php p($l->t('Very Low')); ?></option>
						</select>
					</td>
				</tr>

				<tr>
					<td>
						<?php p($l->t('Status')); ?>
					</td>

					<td>
						:
					</td>
		
					<td>
						<?php
							$ev_stat = array();
							
							$status = NULL;
							
							if(!isset($_['tid']))
							{
								$ev_stat = OC_Collaboration_Task::getEventStatus('Created', 'Creator');
								$status = 'New';
							}
							else
							{
								$ev_stat = OC_Collaboration_Task::getEventStatus($_['task_details']['status'], 'Creator');
								$status = $_['task_details']['status'];
								$member = $_['task_details']['member'];
								
								print_unescaped('<span id="task_status">' . OC_Collaboration_Task::getStatusInFormat($status, $member, OC_User::getUser()) . '</span><br />');
							}
							
							$i = 0;
							foreach($ev_stat as $event => $status)
							{
								print_unescaped('<input class="event" id="event_' . $i . '" value="' . $status . '" name="status" type="radio" data-next-status="' . $status . '" />');
								print_unescaped('<label for="event_' . $i . '" >' . OC_Collaboration_Task::translateEvent($event) . '</label>');
								
								switch($event)
								{
									case 'Assign':
										print_unescaped('<span class="event_info" id="event_info_' . $i . '" >
															<select id="members" name="member" ></select>
															<br />
															<a id="skillset_link" href="" target="_blank" >' . $l->t('View availablility & skillset') . '</a><br />
															<input type="checkbox" id="send_mail" name="send_mail" />
															<label for="send_mail" >' . $l->t('Inform member via mail') . '</label></span>');
														
										break;
										
									case 'Cancel':
									case 'Reject':
										print_unescaped('<span class="event_info" id="event_info_' . $i . '" >
															<input type="text" name="reason" placeholder="' . $l->t('Reason') . '" />
														</span>');
														
										break;
									
								}
								
								print_unescaped('<br />');
								
								$i++;
							}
						?>
					
					</td>					
					
				</tr>

				<tr>
					<td>
						<?php p($l->t('Deadline')); ?>
					</td>

					<td>
						: <span class="required">*</span>
					</td>
	
					<td>
						<input type="text" id="deadline_time" name="deadline_time" placeholder="MM/DD/YYYY HH:MM" autocomplete="off" required <?php if(isset($_['tid'])) print_unescaped('value="' . OC_Collaboration_Time::convertDBTimeToUITimeShort($_['task_details']['ending_time']) . '"'); ?> />
					</td>
				</tr>
			</table>
	
			<div id="submit-form" >
				<input type="submit" value="<?php p($_['submit_btn_name']); ?>" />
			</div>
		</form>
	<?php
		}
	?>	
</div>
