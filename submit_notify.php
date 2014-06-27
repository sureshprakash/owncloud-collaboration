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

$tpl = new OCP\Template('collaboration', 'display_message', 'user');
$tpl->assign('title', $l->t('Loading...'));
$tpl->assign('msg', $l->t('Creating notification \'%s\'. Please be patient.', array($_POST['title'])));
$tpl->printPage();

if(!isset($_POST['post_to_all']))
{
	$members = is_array($_POST['notify_to'])? $_POST['notify_to']: array($_POST['notify_to']);
	$status = OC_Collaboration_Post::createPost($_POST['title'], $_POST['content'], OC_User::getUser(), $_POST['pid'], 'Custom Post', $members);
}
else
{
	$status = OC_Collaboration_Post::createPost($_POST['title'], $_POST['content'], OC_User::getUser(), $_POST['pid'], 'Custom Post');
}
print_unescaped('<META HTTP-EQUIV="Refresh" Content="0; URL=' . \OCP\Util::linkToRoute('collaboration_route', array('rel_path' => 'submit_new_notification')) . '?title=' . $_POST['title'] . '">');

?>
