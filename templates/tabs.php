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
<div id="controls">
    <div class="actions">
        
        <div class="button" id="dashboard">
            <a href="<?php print_unescaped(OCP\Util::linkToRoute('collaboration_route', array('rel_path' => 'dashboard')));?>"><?php p($l->t('Dashboard')) ?></a>
        </div>
        
        <div class="button" id="projects">
            <a href="<?php print_unescaped(OCP\Util::linkToRoute('collaboration_route', array('rel_path' => 'projects')));?>"><?php p($l->t('Projects')) ?></a>
        </div>
        
        <?php
            if(OC_Collaboration_Project::isAdmin())
            {
        ?>
        
        <div class="button" id="create_project">
            <a class="svg icon icon-add" href="<?php print_unescaped(OCP\Util::linkToRoute('collaboration_route', array('rel_path' => 'update_project')));?>">&nbsp;</a>
        </div>
        
        <?php
            }
        ?>
        
        <div class="button" id="tasks">
            <a href="<?php print_unescaped(OCP\Util::linkToRoute('collaboration_route', array('rel_path' => 'tasks')));?>"><?php p($l->t('Tasks')) ?></a>
        </div>
        
        <?php
            if(OC_Collaboration_Project::isAdmin())
            {
        ?>
        
        <div class="button" id="create_task" original-title="<?php p($l->t('Create Task')) ?>">
            <a class="svg icon icon-add" href="<?php print_unescaped(OCP\Util::linkToRoute('collaboration_route', array('rel_path' => 'update_task')));?>">&nbsp;</a>
        </div>
        
        <?php
            }
        ?>
        
        <?php
            if(OC_Collaboration_Project::isAdmin())
            {
        ?>
        
        <div class="button" id="report">
            <a href="<?php print_unescaped(OCP\Util::linkToRoute('collaboration_route', array('rel_path' => 'report')));?>"><?php p($l->t('Report')) ?></a>
        </div>
        
        <?php
            }
        ?>
        
        <div class="button" id="notify">
            <a href="<?php print_unescaped(OCP\Util::linkToRoute('collaboration_route', array('rel_path' => 'notify')));?>"><?php p($l->t('Notify')) ?></a>
        </div>
        
        <div class="button" id="skill_set">
            <a href="<?php print_unescaped(OCP\Util::linkToRoute('collaboration_route', array('rel_path' => 'skillset')));?>"><?php p($l->t('My Skillset')) ?></a>
        </div>
        
    </div>
</div>
<div class="tabs_spacer"></div>
