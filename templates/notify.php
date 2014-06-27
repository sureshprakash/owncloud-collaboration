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
	<h1 id="title" >
		<?php p($l->t('Notify')); ?>
	</h1>
	
	<?php
		if(!isset($_['projects']) || count($_['projects']) == 0)
		{
	?>
			<p>
				<?php p($l->t('Sorry, you are not into any project yet. You can notify only after joining a project.')); ?>
			</p>
	<?php		
		}
		else
		{
	?>
		<form method="post" id="notification_form" >
			<table>
				<tr>
					<td>
						<?php p($l->t('Project')); ?>
					</td>
			
					<td>
						: <span class="required">*</span>
					</td>
			
					<td>
						<select name="pid" id="project" class="chzen-select" >
							<?php
								foreach($_['projects'] as $pid => $ptitle)
								{
									print_unescaped('<option value="' . $pid . '" >' . $ptitle . '</option>');
								}
							?>	
						</select>
						<span id="load_members" > </span>
					</td>
		
				</tr>
			
				<tr>
					<td>
						<?php p($l->t('Notification Subject')); ?>
					</td>
			
					<td>
						: <span class="required">*</span>
					</td>
				
					<td>
						<input type="text" name="title" pattern="[a-zA-Z]([a-zA-Z0-9]\s?(\-\s)?){2,98}[a-zA-Z0-9]" title="Title can contain alphabets, numbers, spaces and hyphens with 4 to 100 characters. First character should be an alphabet and last one can be an alphabet or a numeral." autocomplete="off" required autofocus />
					</td>
				</tr>
		
				<tr> 
					<td>
						<?php p($l->t('Notification Message')); ?>
					</td>
			
					<td>
						: <span class="required">*</span>
					</td>
				
					<td>
						<textarea max="3000" name="content" required ></textarea>
					</td>
				</tr>
			
				<tr>
					<td>
						<?php p($l->t('Notify')); ?>
					</td>
				
					<td>
						:
					</td>
				
					<td>
						&nbsp;&nbsp;<input id="notify_all" type="checkbox" name="post_to_all" checked/>
						<?php p($l->t('Notify all')); ?>
						<br />
					
						<select id="notify_to" multiple="multiple">
						</select>
					
						<div id="member_container" >
							<select id="members_list" name="notify_to[]" disabled="disabled" multiple="multiple" required>
							</select>
						</div>
					</td>
				</tr>
			</table>

			<div id="submit-form" >
				<button id="btn_submit" ><?php p($l->t('Send')); ?></button>
			</div>
		</form>
	<?php
		}
	?>
</div>
