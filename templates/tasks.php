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

<div id="collaboration_content">
	<h1 id="title" >
		<?php p($l->t('Tasks')); ?>
	</h1>

	
	<div id="filter_container" >
		<?php
			$projects = OC_Collaboration_Project::getProjects(OC_User::getUser());
		?>
			
		<form id="filter_form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="get" >
			<span id="status_filter" >
				<?php p($l->t('Filter by status:')); ?>
				<select id="status_list" name="status" class="chzen-select" >
					<?php
						print_unescaped('<option value="ALL" ');
						
						if(!isset($_['status']) || $_['status'] == 'ALL') 
						{ 
							print_unescaped('selected="selected"'); 
						} 
						
						print_unescaped('>' . $l->t('ALL') . '</option>');
						
						print_unescaped('<option value="In Progress" ');
						
						if(isset($_['status']) && strcasecmp($_['status'], 'In Progress') == 0)
						{
							print_unescaped('selected="selected"');
						}
						
						print_unescaped('>' . $l->t('In Progress'). '</option>');
						
						print_unescaped('<option value="Cancelled" ');
						if(isset($_['status']) && strcasecmp($_['status'], 'Cancelled') == 0)
						{
							print_unescaped('selected="selected"');
						}
						
						print_unescaped('>' . $l->t('Cancelled'). '</option>');
						
						print_unescaped('<option value="Completed" ');
						if(isset($_['status']) && strcasecmp($_['status'], 'Completed') == 0)
						{
							print_unescaped('selected="selected"');
						}
						
						print_unescaped('>' . $l->t('Completed'). '</option>');
						
						print_unescaped('<option value="Held" ');
						if(isset($_['status']) && strcasecmp($_['status'], 'Held') == 0)
						{
							print_unescaped('selected="selected"');
						}
						
						print_unescaped('>' . $l->t('Held'). '</option>');
						
						print_unescaped('<option value="Unassigned" ');
						if(isset($_['status']) && strcasecmp($_['status'], 'Unassigned') == 0)
						{
							print_unescaped('selected="selected"');
						}
						
						print_unescaped('>' . $l->t('Unassigned'). '</option>');
						
						print_unescaped('<option value="Verified" ');
						if(isset($_['status']) && strcasecmp($_['status'], 'Verified') == 0)
						{
							print_unescaped('selected="selected"');
						}
						
						print_unescaped('>' . $l->t('Verified'). '</option>');
						?>
						
				</select>
			</span>
			
			<span id="project_filter">
			<?php p($l->t('Filter by project:')); ?>
			<select id="projects_list" name="project" class="chzen-select" >
				<option value="ALL" <?php if(!isset($_['project']) || $_['project'] == 'ALL') { print_unescaped('selected="selected"'); } ?> ><?php p($l->t('ALL')); ?></option>
				<?php
					foreach($projects as $pid => $ptitle)
					{
						print_unescaped('<option value="' . $ptitle . '" ' . (isset($_['project']) && (strtolower($_['project']) == strtolower($ptitle))? 'selected="selected"': '' ) . ' >' . $ptitle . '</option>');
					}
				?>
			</select>
			</span>
			
			<span id="assign_filter">
				<table>
					<tr>
						<th rowspan="2"> 
							<?php print_unescaped($l->t('Show tasks:&nbsp;')); ?>
						</th>
						<td>
							<input type="checkbox" id="assigned_by" name="assigned_by" <?php if(isset($_['assigned_by'])) print_unescaped('checked'); ?> />
							<?php p($l->t('created by me')); ?>
						</td>
					</tr>
					<tr>
						<td>
							<input type="checkbox" id="assigned_to" name="assigned_to" <?php if(isset($_['assigned_to'])) print_unescaped('checked'); ?> />
							<?php p($l->t('assigned to me')); ?>
						</td>
					</tr>
				</table>
			</span>
		</form>
	</div>

	<div id="tasks_list" >
			
		<?php
			if(!isset($_['tasks']) || count($_['tasks']) === 0 || count($_['tasks'][0]) === 0)
			{
				print_unescaped('<p>');
				p($l->t('Sorry, no task is available to display.'));
				print_unescaped('</p>');
			}
			else
			{
				foreach($_['tasks'] as $each)
				{
	?>
				<div class="unit">
					<div class="task_title">		
							<?php p($each['title']); ?>
					</div>

					<div class="contents">	
						<div class="description">	
							<?php p($each['description']); ?>
						</div>							
							<br />
							<form class="view_details" action="<?php p(\OCP\Util::linkToRoute('collaboration_route', array('rel_path'=>'task_details'))); ?>" method="post" >
								<input type="hidden" name="tid" value="<?php p($each['tid']); ?>" />
								<input type="submit" value="<?php p($l->t('View details'));	?>" />
						</form>
							<?php 
								if(strcasecmp(OC_Collaboration_Task::getTaskCreator($each['tid']), OC_User::getUser()) == 0)
								{
									if(strcasecmp($each['status'], 'Cancelled') != 0 && strcasecmp($each['status'], 'Verified') != 0)
									{
							?>	
										<div class="edit" >
											<button class="btn_edit" id="<?php p('btn_edit_' . $each['tid'])?>" >
													<?php
														p($l->t('Edit'));
													?>
											</button>
										</div>
							<?php
									}
								}
								
								if(strcasecmp(OC_Collaboration_Task::getWorkingMember($each['tid']), OC_User::getUser()) == 0)
								{
									print_unescaped('<div class="status_event" data-tid="' . $each['tid'] . '" >');
									
									$ev_stat = OC_Collaboration_Task::getEventStatus($each['status'], 'Performer');
									
									foreach($ev_stat as $event => $status)
									{
										print_unescaped('<button class="event_btn" value="' . $event . '" >' . OC_Collaboration_Task::translateEvent($event) . '</button>');
									}
									
									print_unescaped('</div>');
								}
							?>
					</div>

					<div class="details">
						<div class="task_status">
							<?php
								p($l->t('Status: %s', array(OC_Collaboration_Task::getStatusInFormat($each['status'], $each['member'], $each['creator']))));
							?>
						</div>
						
						<div class="deadline_details">
							<?php
								$datetime = OC_Collaboration_Time::convertDBTimeToUITime($each['ending_time']);
								p($l->t('Deadline: %s', array($l->l('datetime', $datetime)))); 
							?>
						</div>
					</div>
				</div>
	<?php
			}
		}
	?>
	</div>
	
	<form name="status_updation_form" id="status_updation_form" >
		<label for="reason" ><?php p($l->t('Kindly give your reason in passive voice')); ?></label>
		<input type="text" id="reason" autocomplete="off" />
		<div style="text-align: center;" >
			<input type="button" id="change_status" />
		</div>
	</form>
			
</div>
