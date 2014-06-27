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
		<?php p($l->t('Project Report')); ?>
	</h1>

	<div id="project_list_container" >
		<?php
			$projects = OC_Collaboration_Project::getProjects(OC_User::getUser());
		?>
		
		<form id="chooser_form" action="" >
			<select id="projects_list" name="project" class="chzen-select" >
				<option value="" ><?php p($l->t('Select Project')); ?></option>
				<?php
					foreach($projects as $pid => $ptitle)
					{
						print_unescaped('<option value="' . $pid . '">' . $ptitle . '</option>');
					}
				?>
			</select>
			
			<select id="report_type" name="report_type" >
				<option value="" ><?php p($l->t('Select Report Type')); ?></option>
				<option value="contribution" ><?php p($l->t('Contribution Report')); ?></option>
				<option value="project_status" ><?php p($l->t('Project Timeline')); ?></option>
				<option value="task_status" ><?php p($l->t('Task Status Report')); ?></option>
			</select>
		</form>
	</div>
		
	<div id="message" ><?php p($l->t('Kindly select project and report type to generate the report')); ?></div>
	
	<div id="bargraph" >
	</div>
		
	<div id="report_content" >
		<span id="diagram" >
			<canvas id="canvas" width="350" height="350" >
				
			</canvas>
		</span>
		
		<span id="legend" >
		</span>
	</div>
</div>
