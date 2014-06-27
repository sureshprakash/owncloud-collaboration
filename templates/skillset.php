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
		<?php p($l->t('My Skillset')); ?>
	</h1>
	
	<form action="<?php print_unescaped(OCP\Util::linkToRoute('collaboration_route', array('rel_path' => 'submit_skillset'))); ?>" method="post" >
	<table id="skills" >
		<tr>
			<th><?php p($l->t('Skill')); ?></th>
			<th><?php p($l->t('Expertise')); ?></th>
			<th><?php print_unescaped($l->t('Experience') . '<br />' . $l->t('(in years)')); ?></th>
			<th></th>
		</tr>
		
		<?php
			$skills = OC_Collaboration_Skillset::readSkills(OC_User::getUser());
			
			foreach($skills as $skill)
			{
				print_unescaped('<tr><td>' . $skill['skill'] . '</td><td>' . OC_Collaboration_Skillset::getExpertiseString($skill['expertise']) . '</td><td>' . $skill['experience'] . '</td><td><img class="old_skill" src="' . OCP\Util::imagePath('core', 'actions/delete.png') . '" width="15px" height="15px" /></td></tr>');
			}
		?>
		<tr>
			<td id="add_skill0" >
				<input type="button" id="btn_add_skill" value="<?php p($l->t('Add a skill')); ?>" />
			</td>
		</tr>
	</table>
	
	<div id="submit_btn" >
		<input type="submit" value="<?php p($l->t('Update')); ?>" />
	</div>
	</form>
</div>
