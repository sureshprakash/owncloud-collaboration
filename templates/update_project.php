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
	<?php
		if(isset($_['project_details']))
			$pid = $_['project_details']['pid'];
		else
			$pid = -1;
	?>

	<h1 id="title">
		<?php p($_['title']); ?>
	</h1>

	<form action="<?php print_unescaped(OCP\Util::linkToRoute('collaboration_route', array('rel_path' => 'submit_update_project')));?>" method="post" id="project_updation_form">

		<?php
			if($pid != -1)
			{
		?>
		<input type="hidden" name="pid" id="pid" value="<?php p($pid); ?>" />
		<?php
			}
		?>
	
		<table>
			<tr>
				<td>
					<?php p($l->t('Project Title')); ?>
				</td>

				<td>
					: <span class="required">*</span>
				</td>
				
				<td>
					<input type="text" name="title" id="project_title" autocomplete="off" pattern="[a-zA-Z]([a-zA-Z0-9]\s?(\-\s)?){2,98}[a-zA-Z0-9]" title="Title can contain alphabets, numbers, spaces and hyphens with 4 to 100 characters. First character should be an alphabet and last one can be an alphabet or a numeral." <?php if($pid != -1) print_unescaped('value="'.$_['project_details']['title'].'"'); ?> required autofocus/>
					<span id="error_msg" class="error" ></span>
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
					<textarea maxlength="3000" name="description" ><?php if($pid != -1) p($_['project_details']['description']); ?></textarea>
				</td>
			</tr>

			<!--input type="hidden" name="creator" value="<?php p(OC_User::getUser()); ?>" /-->
			<!--
			<tr>
				<td>
					<?php p($l->t('Creator')); ?>
				</td>

				<td>
					:
				</td>

				<td>
					<?php print("&nbsp;" . OC_User::getDisplayName() . "&nbsp;(" . OC_User::getUser() . ")"); ?>
				</td>
			</tr>
			-->
		
			<tr>
				<td>
					<?php p($l->t('Deadline')); ?>
				</td>

				<td>
					: <span class="required">*</span>
				</td>
		
				<td>
					<input type="text" id="deadline" name="deadline" placeholder="MM/DD/YYYY" <?php if($pid != -1) print_unescaped('value="'.OC_Collaboration_Time::convertDBTimeToUIDate($_['project_details']['ending_date']).'"'); ?> autocomplete="off" required/>
					<?php
						if($pid != -1)
						{
					?>
					<label for="project_completed" ><?php p($l->t('Completed?')); ?></label>
					<input type="checkbox" name="project_completed" id="project_completed" <?php if(($_['project_details']['completed']) == true) p('checked="checked"'); ?>" />
					<?php
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
				
				<td id="remove_old_member_loading_img" >
				</td>
			</tr>
			
		</table>
		
		<?php
			if($pid != -1)
			{
				$member_role = OC_Collaboration_Project::getMembers($pid);
				
				print_unescaped('<span class="old_mem_list" >');
				
				foreach($member_role as $role => $members)
				{
					print_unescaped('<div class="old_members" data-role="' . $role . '" ><div class="old_mem_role" >' . $role . ': </div>');
					
					if(strcasecmp($role, 'Creator') == 0)
					{
						print_unescaped('<span>' . $members[0] . '</span>');
					}
					else
					{
					
						foreach($members as $index => $member)
						{
							print_unescaped('<span>' . $member . ' <img src="' . OCP\Util::linkTo('core', 'img/actions/delete.png') . '" class="delete_old_member" data-member="' . $member . '" title="' . $l->t('Delete') . '" /><br /></span>');
						}
					}
					
					print_unescaped('</div>');
				}
				
				print_unescaped('</span>');
			}
		?>
	
		<?php p($l->t('(If user does not exist in owncloud, user will be automatically created with password being the same as username in lower case)')); ?>
		<table id="details" >
			<tr>
				<td id="add_member0" >
					<input type="button" id="btn_add_member" value="<?php p($l->t('Add Member')); ?>" />
				</td>
			</tr>
		</table>
		
		<input type="checkbox" id="send_mail" name="send_mail" />
		<label for="send_mail" ><?php p($l->t('Inform member(s) via mail')); ?></label>
		
		<div id="submit-form" >
			<input type="submit" value="<?php p($_['submit_btn_name']); ?>" />
		</div>
	</form>
</div>
