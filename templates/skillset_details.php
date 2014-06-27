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
		<?php p($l->t('Skillset')); ?>
	</h1>

	<table id="skills">
		<tr>
			<th>
				<?php p($l->t('Member')); ?>
			</th>
		
			<th>
				<?php print_unescaped($l->t('Number of tasks in project<br />\'%s\'', array(OC_Collaboration_Project::getProjectTitle($_['project'])))); ?>
			</th>
			
			<th>
				<?php p($l->t('Number of tasks in all projects')); ?>
			</th>
			
			<th>
				<?php p($l->t('Skill')); ?>
			</th>
		
			<th>
				<?php p($l->t('Expertise')); ?>
			</th>
		
			<th>
				<?php p($l->t('Experience')); ?>
			</th>
		
		</tr>

		<?php
			foreach($_['members'] as $member => $cnt)
			{
				if(!is_null($member) && $member != '0')
				{
					$skills = OC_Collaboration_Skillset::readSkills($member);
					$skl_cnt = count($skills);
				
					if($skl_cnt == 0)
					{
						print_unescaped('<tr><td>' . $member . '</td><td>' . $cnt['proj_cnt'] . '</td><td>' . $cnt['tot_cnt'] . '</td><td colspan="3" >' . $l->t('Skills yet to be added by member') . '</td></tr>');
					}
					else
					{
						print_unescaped('<tr><td rowspan="' . $skl_cnt . '" >' . $member . '</td><td rowspan="' . $skl_cnt . '" >' . $cnt['proj_cnt'] . '</td><td rowspan="' . $skl_cnt . '" >' . $cnt['tot_cnt'] . '</td>');
					
						foreach($skills as $skill)
						{
							print_unescaped('<td>' . $skill['skill'] . '</td><td>' . OC_Collaboration_Skillset::getExpertiseString($skill['expertise']) . '</td><td>' . $skill['experience'] . ' ' . $l->t('year(s)') . '</td></tr><tr>');
						}
					
						print_unescaped('</tr>');
					}
				}
			}
		?>	
	</table>
</div>		
