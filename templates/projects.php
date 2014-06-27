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
		<?php p($l->t('Projects')); ?>
	</h1>

	<?php
		if(!isset($_['projects']) || count($_['projects']) === 0 || count($_['projects'][0]) === 0)
		{
			print_unescaped('<p>'.$l->t('Sorry, no project is available yet to display.').'</p>');
		}
		else
		{
	?>
	<span id="project_search">
		<form id="search_form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="get" >
			<?php
				$projects = OC_Collaboration_Project::getProjects(OC_User::getUser());
			?>
			<?php p($l->t('Search project:')); ?>
			<select id="search_list" name="project" class="chzen-select" >
				<option value="ALL" <?php if(!isset($_['project']) || $_['project'] == 'ALL') { print_unescaped('selected="selected"'); } ?> ><?php p($l->t('ALL')); ?></option>
				<?php
					foreach($projects as $pid => $ptitle)
					{
						print_unescaped('<option value="' . $ptitle . '" ' . (isset($_['project']) && (strtolower($_['project']) == strtolower($ptitle))? 'selected="selected"': '' ) . ' >' . $ptitle . '</option>');
					}
				?>
			</select>
		</form>
	</span>
			
	<div id="projects_list" >
	<?php
			foreach($_['projects'] as $each)
			{
	?>
				<div class="unit">
					<div class="project_title">		
							<?php p($each['title']); ?>
					</div>

					<div class="contents">
						<div class="description" >	
							<?php p($each['description']); ?>
						</div>
						
						<br />
						
						<form class="view_details" action="<?php p(\OCP\Util::linkToRoute('collaboration_route', array('rel_path'=>'project_details'))); ?>" method="post" >
								<input type="hidden" name="pid" value="<?php p($each['pid']); ?>" />
								<input type="submit" value="<?php p($l->t('View details'));	?>" />
						</form>
						<?php
							if(OC_Collaboration_Project::isAdmin())
							{
						?>
						<div class="edit" >
							<button class="btn_edit" id="<?php p('btn_edit_' . $each['pid'])?>" >
								<?php
									p($l->t('Edit'));
								?>
							</button>
						</div>
						<?php
							}
						?>
					</div>
					
					<div class="details">

						<div class="creation_details">
							<?php
								$datetime = explode(' ', $each['starting_date']); 
								p($l->t('On %s at %s', array($l->l('date', $datetime[0]), $l->l('time', $datetime[1])))); 
							?>
						</div>
						
						<div class="deadline_details">
							<?php
								$datetime = explode(' ', $each['ending_date']); 
								p($l->t('On %s at %s', array($l->l('date', $datetime[0]), $l->l('time', $datetime[1])))); 
							?>
						</div>
					</div>
				</div>
	<?php
			}
	?>
			</div>
			
	<?php
		}
	?>
</div>
