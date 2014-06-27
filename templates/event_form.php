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
	print_unescaped('<div id="collaboration_content">');
	
	if(strcmp($_['permission_granted'], 'true') == 0)
	{
		if(!isset($_POST['status']))
		{
			$_POST['member'] = "";
		}
	
		$today = new DateTime();
		$today->setTimezone(new DateTimeZone('Asia/Kolkata'));
		$today_date = $today->format('Y-m-d');
		$today_time = $today->format('H:i:s');
	
		$deadline = explode(' ', OC_Collaboration_Time::convertUITimeShortToDBTimeShort($_POST['deadline_time']));
		$deadline_date = $deadline[0];
		$deadline_time = $deadline[1];
?>
		<input type="hidden" name="create_new" value="true" id="create_new" />

		<form id="ev_form" action="" method="post" style="display: none;" >
			<input type="hidden" name="title" value="<?php p($_POST['title']); ?>" />
			<input type="hidden" name="categories" value="Projects" />
			<input type="hidden" name="calendar" value="<?php p(OC_Collaboration_Calendar::getCalendarId($_POST['pid'])); ?>" />
			<input type="hidden" name="accessclass" value="PUBLIC" />
			<!--input type="hidden" name="allday" value="on" disabled /-->
			<input type="hidden" name="from" value="<?php p($today_date); ?>" />
			<input type="hidden" name="fromtime" value="<?php p($today_time); ?>" />
			<input type="hidden" name="to" value="<?php p($deadline_date); ?>" />
			<input type="hidden" name="totime" value="<?php p($deadline_time); ?>" />
			<input type="hidden" name="location" value="" />
			<input type="hidden" name="description" value="<?php p($_POST['description']); ?>" />
			<input type="hidden" name="repeat" value="doesnotrepeat" />
		</form>

		<form id="task_schedule" action="<?php print_unescaped(\OCP\Util::linkToRoute('collaboration_route', array('rel_path' => 'submit_new_task'))); ?>" method="get" >
			<input type="hidden" name="task" value="<?php p($_['task']); ?>" />
			<input type="hidden" name="title" value="<?php p($_POST['title']); ?>" />
			<input type="hidden" name="event_id" id="event_id" value="" />
			<input type="hidden" name="member" id="member" value="<?php p(strtolower($_POST['member'])); ?>" />
		</form>

<?php
		print_unescaped('<h1 id="title">' . $l->t('Loading...') . '</h1><p>' . $l->t('Creating task \'%s\'. Please be patient.', array($_['title'])) . '</p>');
	}
	else
	{
		print_unescaped('<h1 id="title">' . $l->t('Access Denied') . '</h1><p>' . $l->t('Task \'%s\' cannot be created.', array($_['title'])) . '</p>');
	}
	
	print_unescaped('</div>');
?>
