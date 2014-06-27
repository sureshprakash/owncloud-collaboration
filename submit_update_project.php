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

\OCP\User::checkLoggedIn();
\OCP\App::checkAppEnabled('collaboration');

OCP\App::setActiveNavigationEntry( 'collaboration' );

OCP\Util::addStyle('collaboration', 'tabs');
OCP\Util::addStyle('collaboration', 'display_message');

OCP\Util::addScript('collaboration', 'display_message');

$l = OC_L10N::get('collaboration');

// Fetch member list
$details = array(array());

$i = 0;
foreach($_POST as $key => $value)
{
	if(strpos($key, 'mem_name') === 0)
	{
		$id = substr($key, 8);
		
		$details[$i]['member'] = $value;
		$details[$i]['role'] = $_POST['mem_role' . $id];
		$details[$i]['email'] = $_POST['mem_email' . $id];
		$details[$i]['mobile'] = $_POST['mem_mobile' . $id];
		
		$i++;
	}
}

$tpl = new OCP\Template('collaboration', 'display_message', 'user');

$tpl->assign('title', $l->t('Loading...'));
$tpl->assign('msg', $l->t('Updating project \'%s\'. Please be patient.', array($_POST['title'])));

$tpl->printPage();

$redirect = '';
$post_id = false;

if(!isset($_POST['pid']))
{
	$post_id = OC_Collaboration_Project::createProject($_POST['title'], $_POST['description'], OC_User::getUser(), $_POST['deadline'], $details);
	$redirect = 'submit_new_project';
}
else
{
	$post_id = OC_Collaboration_Project::updateProject($_POST['pid'], $_POST['title'], $_POST['description'], $_POST['deadline'], $details, OC_User::getUser(), isset($_POST['project_completed']));
	$redirect = 'submit_change_project';
}

if($post_id != false && isset($_POST['send_mail']))
{
	OC_Collaboration_Mail::sendProjectAssignmentMail($_POST['title'], $details);
}

print_unescaped('<META HTTP-EQUIV="Refresh" Content="0; URL=' . \OCP\Util::linkToRoute('collaboration_route', array('rel_path' => $redirect)) . '?post=' . $post_id . '&title=' . $_POST['title'] . '">');

?>
