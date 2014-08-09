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
<nav id="app-navigation">
		<ul id="navigation-list">
			<li id="dashboard" class="tab">
				<a href="<?php print_unescaped(OCP\Util::linkToRoute('collaboration_route', array('rel_path' => 'dashboard')));?>">
					<?php p($l->t('Dashboard')) ?>
				</a>
			</li>

			<?php if(OC_Collaboration_Project::isAdmin()) { ?>
			  <li id="create_project" class="tab">
				  <a href="<?php print_unescaped(OCP\Util::linkToRoute('collaboration_route', array('rel_path' => 'update_project')));?>">
					  <?php p($l->t('Create Project')) ?>
				  </a>
			  </li>

			  <li id="create_task" class="tab">
				  <a href="<?php print_unescaped(OCP\Util::linkToRoute( 'collaboration_route', array('rel_path' => 'update_task' )));?>">
					  <?php p($l->t('Create Task')) ?>
			  	</a>
			  </li>
			<?php 	} //-- end admin	?>

			<li id="projects" class="tab">
				<a href="<?php print_unescaped(OCP\Util::linkToRoute( 'collaboration_route', array('rel_path' => 'projects' )));?>">
					<?php p($l->t('Projects')) ?>
				</a>
			</li>

			<li id="tasks" class="tab">
				<a href="<?php print_unescaped(OCP\Util::linkToRoute( 'collaboration_route', array('rel_path' => 'tasks' )));?>">
					<?php p($l->t('Tasks')) ?>
				</a>
			</li>

			<?php if(OC_Collaboration_Project::isAdmin()){ ?>
			  <li id="report" class="tab">
				  <a href="<?php print_unescaped(OCP\Util::linkToRoute( 'collaboration_route', array('rel_path' => 'report' )));?>">
					  <?php p($l->t('Report')) ?>
				  </a>
			  </li>
			<?php 	} //-- end admin	?>

			<li id="notify" class="tab">
				<a href="<?php print_unescaped(OCP\Util::linkToRoute( 'collaboration_route', array('rel_path' => 'notify' )));?>">
					<?php p($l->t('Notify')) ?>
				</a>
			</li>

			<li id="skill_set" class="tab">
				<a href="<?php print_unescaped(OCP\Util::linkToRoute('collaboration_route', array('rel_path' => 'skillset')));?>">
					<?php p($l->t('My Skillset')) ?>
				</a>
			</li>
		</ul>
</nav>
