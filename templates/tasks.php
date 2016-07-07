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
   // print_r($_['tasks']);
?>
<div id="app-content">
  <div id="content-header" >
    <h1 id="title" ><?php p($l->t('Tasks')); ?></h1>
    <?php
      if(!isset($_['tasks']) || count($_['tasks']) === 0 || count($_['tasks'][0]) === 0)	{
        print_unescaped('<p>'.$l->t('Sorry, no project is available yet to display.').'</p>');
      }	else {
    ?>
    <form id="search_form" class='ch-right' action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="get" >
      <select id="search_list" name="project" class="chzen-select" >
        <option value="ALL" <?php if(!isset($_['project']) || $_['project'] == 'ALL') { print_unescaped('selected="selected"'); } ?> ><?php p($l->t('Search for tasks by project')); ?></option>
        <?php
          foreach($_['tasks'] as $each) {
            print_unescaped('<option value="'.$each["proj_title"].'" >'.$each["proj_title"].'</option>');
          }
        ?>
      </select>
      <?php } ?>
    </form>
  </div>
  <div id="content-body" >
		<?php
			if(!isset($_['tasks']) || count($_['tasks']) === 0 || count($_['tasks'][0]) === 0)	{
				print_unescaped('<p>');
				p($l->t('Sorry, no task is available to display.'));
				print_unescaped('</p>');

			} else {
			 foreach($_['tasks'] as $each) {
	   ?>
			 <div class="unit">
         <div class="cb_title">
					 <?php p($each['title']); ?>
				</div>
        <div class='clear-both-np'>
          <div class="cb-wrapper">
					<div class="contents">
            <div class="cb-date">
              <p>
                <b>Project</b>
                <?php print_unescaped( $each['proj_title']); ?>
              </p>
            </div>
            <div class="cb-date">
              <p>
                <b>Task Status</b>
                <?php
                  print_unescaped(
                    OC_Collaboration_Task::getStatusInFormat($each['status'],
                    $each['member'],
                    $each['creator']
                  ));
                ?>
              </p>
            </div>
            <div class="cb-date">
              <b> Deadline</b>
              <?php
                $datetime = OC_Collaboration_Time::convertDBTimeToUITime($each['ending_time']);
                p($l->t(' %s', array($l->l('datetime', $datetime))));
              ?>
            </div>
            <!--
            <div class="cb-date">
              <a href='#' class="open-description">Read more</a>
            </div>
            -->
						<div class="unit-description">
							<?php print_unescaped($each['description']); ?>
						</div>

            <div class="comment" >
						  <form class="view_details" action="<?php p(\OCP\Util::linkToRoute('collaboration_route', array('rel_path'=>'task_details'))); ?>" method="post" >
							  	<input type="hidden" name="tid" value="<?php p($each['tid']); ?>" />
								  <input type="submit" value="<?php p($l->t('View details'));	?>" />
						  </form>
							 <?php if(strcasecmp(OC_Collaboration_Task::getTaskCreator($each['tid']), OC_User::getUser()) == 0) {  ?>
                 <?php	if(strcasecmp($each['status'], 'Cancelled') != 0 && strcasecmp($each['status'], 'Verified') != 0)	{ ?>
                          <form class="view_details" action="<?php p(\OCP\Util::linkToRoute('collaboration_route', array('rel_path'=>'update_task'))); ?>" method="post" >
                                <input type="hidden" name="tid" value="<?php p($each['tid']); ?>" />
                                <input type="submit" value="<?php p($l->t('Edit'));	?>" />
                          </form>
						  	  <?php } ?>
								<?php } ?>
                <?php
								  if(strcasecmp(OC_Collaboration_Task::getWorkingMember($each['tid']), OC_User::getUser()) == 0) {
									  print_unescaped('<div class="status_event" data-tid="' . $each['tid'] . '" >');
                    $ev_stat = OC_Collaboration_Task::getEventStatus($each['status'], 'Performer');

									  foreach($ev_stat as $event => $status) {
										  print_unescaped('<button class="event_btn" value="' . $event . '" >' . OC_Collaboration_Task::translateEvent($event) . '</button>');
									  }

									  print_unescaped('</div>');
								  } ?>
            </div>
					</div>
				</div>
      </div>
    </div>
	<?php
			}
		}
	?>
</div>
