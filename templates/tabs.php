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
?>
<nav id="tabs_collaboration">
	<div>
		<ul>
			<li id="dashboard" class="tab">
				<a href="<?php print_unescaped(OCP\Util::linkToRoute('collaboration_route', array('rel_path' => 'dashboard')));?>">
					<img src="<?php print_unescaped(OCP\Util::imagePath('collaboration', 'dashboard.png')); ?>" height="35px" width="35px" />
					<br />
					<?php p($l->t('Dashboard')) ?>
				</a>
			</li>

			<?php
				if(OC_Collaboration_Project::isAdmin())
				{
			?>
			
			<li id="create_project" class="tab">
				<a href="<?php print_unescaped(OCP\Util::linkToRoute('collaboration_route', array('rel_path' => 'update_project')));?>">
					<img src="<?php print_unescaped(OCP\Util::imagePath('collaboration', 'create_project.png')); ?>" height="35px" width="35px" />
					<br />
					<?php p($l->t('Create Project')) ?>
				</a>
			</li>
			
			<li id="create_task" class="tab">
				<a href="<?php print_unescaped(OCP\Util::linkToRoute( 'collaboration_route', array('rel_path' => 'update_task' )));?>">
					<img src="<?php print_unescaped(OCP\Util::imagePath('collaboration', 'create_task.png')); ?>" height="35px" width="35px" />
					<br />
					<?php p($l->t('Create Task')) ?>
				</a>
			</li>
			
			<?
				}
			?>
		
			<li id="projects" class="tab">
				<a href="<?php print_unescaped(OCP\Util::linkToRoute( 'collaboration_route', array('rel_path' => 'projects' )));?>">
					<img src="<?php print_unescaped(OCP\Util::imagePath('collaboration', 'projects.png')); ?>" height="35px" width="35px" />
					<br />
					<?php p($l->t('Projects')) ?>
				</a>
			</li>

			<li id="tasks" class="tab">
				<a href="<?php print_unescaped(OCP\Util::linkToRoute( 'collaboration_route', array('rel_path' => 'tasks' )));?>">
					<img src="<?php print_unescaped(OCP\Util::imagePath('collaboration', 'tasks.png')); ?>" height="35px" width="35px" />
					<br />
					<?php p($l->t('Tasks')) ?>
				</a>
			</li>
		
			<?php
				if(OC_Collaboration_Project::isAdmin())
				{
			?>
			<li id="report" class="tab">
				<a href="<?php print_unescaped(OCP\Util::linkToRoute( 'collaboration_route', array('rel_path' => 'report' )));?>">
					<img src="<?php print_unescaped(OCP\Util::imagePath('collaboration', 'report.png')); ?>" height="35px" width="35px" />
					<br />
					<?php p($l->t('Report')) ?>
				</a>
			</li>
			<?
				}
			?>

			<li id="notify" class="tab">
				<a href="<?php print_unescaped(OCP\Util::linkToRoute( 'collaboration_route', array('rel_path' => 'notify' )));?>">
					<img src="<?php print_unescaped(OCP\Util::imagePath('collaboration', 'notify.png')); ?>" height="35px" width="35px" />
					<br />
					<?php p($l->t('Notify')) ?>
				</a>
			</li>
			
			<li id="skill_set" class="tab">
				<a href="<?php print_unescaped(OCP\Util::linkToRoute('collaboration_route', array('rel_path' => 'skillset')));?>">
					<img src="<?php print_unescaped(OCP\Util::imagePath('collaboration', 'skill_set.png')); ?>" height="35px" width="35px" />
					<br />
					<?php p($l->t('My Skillset')) ?>
				</a>
			</li>


		</ul>
	</div>
</nav>
