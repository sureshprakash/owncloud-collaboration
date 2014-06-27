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
		<?php p($l->t('Dashboard')); ?>
	</h1>

		<div id="project_list_container" >
			<?php
				$projects = OC_Collaboration_Project::getProjects(OC_User::getUser());
			?>
			
			<form id="filter_form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="get" >
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
			</form>
		</div>

		<div id="posts" >
	<?php
		if(!isset($_['posts']) || count($_['posts']) === 0 || count($_['posts'][0]) === 0)
		{
			print_unescaped('<div style="clear: both" >');
			p($l->t('Sorry, no post is available to display.'));
			print_unescaped('</div>');
		}
		else
		{
			foreach($_['posts'] as $each)
			{
	?>
			<div class="unit">
				<div class="post_title">		
						<?php p($each['title']); ?>
				</div>

				<div class="contents">		
						<?php p($each['content']); ?>
						<br />
						<br />
						<div class="comment" >
							<button class="btn_comment" id="<?php p('btn_comment_' . $each['post_id'])?>" >
								<?php
									p($l->t('Comments') . ' (' . OC_Collaboration_Comment::getCommentCount($each['post_id']) . ')');
								?>
							</button>
						</div>
				</div>

				<div class="details">
					<div class="proj_title">
						<?php
							if(isset($each['proj_title']) && !is_null($each['proj_title']))
							{
								p($l->t('Project: %s', array($each['proj_title'])));
							}
						?>
					</div>
					
					<div class="creation_details">
						<?php
							$datetime = explode(' ', $each['time']); 
							p($l->t('On %s at %s by %s', array($l->l('date', $datetime[0]), $l->l('time', $datetime[1]), $each['creator']))); 
						?>
					</div>
				</div>
			</div>
	<?php
			}
		}
	?>
		</div>
			
</div>
