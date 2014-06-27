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
    
    $user = OC_User::getUser();
?>

<div id="collaboration_content">
	<h1 id="title">
		<?php print_unescaped($_['details']['title']); ?>
	</h1>

	<div id="post_content">
		<span class="post_creator" >
			<?php print_unescaped($_['details']['creator'] . ': '); ?>
		</span>
		
		<span id="post_text">
			<?php p($_['details']['content']); ?>
		</span>
		
		<hr />
		
		<div class="post_details" id="post_details" data-collaboration-id="<?php p($_['details']['post_id']) ?>" >
			<?php 
				$datetime = explode(' ', $_['details']['time']); 
				p($l->t('On %s at %s', array($l->l('date', $datetime[0]), $l->l('time', $datetime[1])))); 
				
				if(strcasecmp($_['details']['type'], 'Custom Post') == 0 && (strtolower($user) == strtolower($_['details']['creator'])))
				{
					print_unescaped('<div class="edit_delete_post" >');
					
					print_unescaped('<a class="edit" data-collaboration-type="post" data-collaboration-id="' . $_['details']['post_id'] . '" >');
					p($l->t('Edit'));
					print_unescaped('</a>');
					
					print_unescaped(' | ');
					
					print_unescaped('<a class="delete" data-collaboration-type="post" data-collaboration-id="' . $_['details']['post_id'] . '">');
					p($l->t('Delete'));
					print_unescaped('</a>');
					
					print_unescaped('</div>');
				}
				else if(strcasecmp($_['details']['type'], 'Project Creation') == 0 || strcasecmp($_['details']['type'], 'Project Updation') == 0)
				{
					print_unescaped('<span class="view_details" id="project_details_link" >');
					print_unescaped('<form method="post" action="' . \OCP\Util::linkToRoute('collaboration_route', array('rel_path' => 'project_details')) . '" ><input type="hidden" name="pid" value="' . $_['details']['pid'] . '" /><a>'.$l->t('View Details').'</a></form>');
					print_unescaped('</span>');
				}
				else if(strcasecmp($_['details']['type'], 'Task Unassigned') == 0 || strcasecmp($_['details']['type'], 'Task Assign') == 0 || strcasecmp($_['details']['type'], 'Task Status Changed') == 0)
				{
					print_unescaped('<span class="view_details" id="task_details_link" >');
					print_unescaped('<form method="post" action="' . \OCP\Util::linkToRoute('collaboration_route', array('rel_path' => 'task_details')) . '" ><input type="hidden" name="tid" value="' . $_['details']['tid'] . '" /><a>'.$l->t('View Details').'</a></form>');
					print_unescaped('</span>');
				}
			?>
		</div>
	</div>
	
	<div id="comments" >
	<?php
		foreach($_['comments'] as $comment)
		{
			if((!isset($comment['comment_id'])) || ($comment['comment_id'] == ''))
			{
				break;
			}
			
			print_unescaped('<div class="comment" id="comment_' . $comment['comment_id'] . '" >');
			print_unescaped('<span class="comment_creator" >' . $comment['creator'] . ': </span>');
			print_unescaped('<span id="comment_content_' . $comment['comment_id'] . '" >' . $comment['content'] . '</span>');
			print_unescaped('<hr />');
			print_unescaped('<div class="comment_details" >');
			
			print_unescaped('<span class="updated_time" >');
			$datetime = explode(' ', $comment['time']);
			p($l->t('On %s at %s', array($l->l('date', $datetime[0]), $l->l('time', $datetime[1]))));
			print_unescaped('</span>');
		
			if(strtolower($user) == strtolower($comment['creator']))
			{
				print_unescaped('<div class="edit_delete_comment" >');
				
				print_unescaped('<a class="edit" data-collaboration-type="comment" data-collaboration-id="' . $comment['comment_id'] . '" >');
				p($l->t('Edit'));
				print_unescaped('</a>');
				
				print_unescaped(' | ');
				
				print_unescaped('<a class="delete" data-collaboration-type="comment" data-collaboration-id="' . $comment['comment_id'] . '">');
				p($l->t('Delete'));
				print_unescaped('</a>');
				
				print_unescaped('</div>');
			}
				
			print_unescaped('</div>');
			print_unescaped('</div>');
		}
	?>
	</div>
	
	<div id="new_comment" >
		<?php p($l->t('Write new comment: ')); ?>
		<br />
		<textarea id="new_comment_content" ></textarea>
		<br />
		<button id="submit_new_comment" ><?php p($l->t('Submit')); ?></button>
		<span class="validate_message" ></span>
	</div>
	
	<div id="edit_post_dialog" class="dialog">
		<form>
			<fieldset>
				<?php p($l->t('Edit')); ?>
				<br />
				<input type="text" id="updated_title" placeholder="<?php p($l->t('Updated post title')); ?>"  pattern="[a-zA-Z]([a-zA-Z0-9]\s?(\-\s)?){2,98}[a-zA-Z0-9]" title="Title can contain alphabets, numbers, spaces and hyphens with 4 to 100 characters. First character should be an alphabet and last one can be an alphabet or a numeral." autocomplete="off" required />
				<br />
				<textarea id="updated_post_text" placeholder="<?php p($l->t('Updated post content')); ?>" required></textarea>
				<div class="validate_message" ></div>
				<div id="post_dialog_buttons" >
					<input type="button" value="<?php p($l->t('Update')); ?>" id="post_save_edit" />
					<input type="button" value="<?php p($l->t('Cancel')); ?>" id="post_cancel_edit" />
				</div>
			</fieldset>
		</form>
	</div>
	
	<div id="edit_comment_dialog" class="dialog">
		<form>
			<fieldset>
				<?php p($l->t('Edit')); ?>
				<br />
				<textarea id="updated_comment_text" placeholder="<?php p($l->t('Updated comment')); ?>" required></textarea>
				<div class="validate_message" ></div>
				<div id="comment_dialog_buttons" >
					<input type="button" value="<?php p($l->t('Update')); ?>" id="comment_save_edit" />
					<input type="button" value="<?php p($l->t('Cancel')); ?>" id="comment_cancel_edit" />
				</div>
			</fieldset>
		</form>
	</div>
</div>
