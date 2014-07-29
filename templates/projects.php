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
$projects = OC_Collaboration_Project::getProjects(OC_User::getUser());

print_unescaped($this->inc('tabs'));
?>

<div id="app-content">
  <div id="content-header" >
	  <h1 id="title" ><?php p($l->t('Projects')); ?></h1>
	  <?php
		  if(!isset($_['projects']) || count($_['projects']) === 0 || count($_['projects'][0]) === 0) {
			  print_unescaped('<p>'.$l->t('Sorry, no project is available yet to display.').'</p>');
		  }	else {
	  ?>
		<form id="search_form" class='ch-right' action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="get" >
			<select id="search_list" name="project" class="chzen-select" >
				<option value="ALL" <?php if(!isset($_['project']) || $_['project'] == 'ALL') { print_unescaped('selected="selected"'); } ?> ><?php p($l->t('Search projects')); ?></option>
				<?php
					foreach($projects as $pid => $ptitle) {
						print_unescaped('<option value="' . $ptitle . '" ' . (isset($_['project']) && (strtolower($_['project']) == strtolower($ptitle))? 'selected="selected"': '' ) . ' >' . $ptitle . '</option>');
					}
				?>
			</select>
	  </form>
   </div>
   <div id="content-body" >
	    <?php 	foreach($_['projects'] as $each) { ?>
				<div class="unit">
					<div class="project_title">
							<?php p($each['title']); ?>
					</div>
          <div class='clear-both-np'>
             <div class="cb-wrapper">
               <div class="details">
                 <p>
                   <b>Creation</b>
                   <?php
                     $datetime = explode(' ', $each['starting_date']);
                     print_unescaped(
                      $l->t('On %s at %s',
                        array(
                          $l->l('date', $datetime[0]),
                          $l->l('time', $datetime[1])
                          )
                        )
                      );
                    ?>
                 </p>
               </div>
               <div class="details">
                 <p>
                   <b>Deadline</b>
                   <?php
                     $datetime = explode(' ', $each['ending_date']);
                     print_unescaped(
                       $l->t('On %s at %s',
                         array(
                           $l->l('date', $datetime[0]),
                           $l->l('time', $datetime[1])
                          )
                        )
                      );
                   ?>
                 </p>
               </div>
               <div class="contents">
						     <?php p($each['description']); ?>
						   </div>
               <div class="comment" >
						     <form class="view_details" action="<?php p(\OCP\Util::linkToRoute('collaboration_route', array('rel_path'=>'project_details'))); ?>" method="post" >
								   <input type="hidden" name="pid" value="<?php p($each['pid']); ?>" />
								   <input type="submit" value="<?php p($l->t('View details'));	?>" />
						     </form>
						     <?php if(OC_Collaboration_Project::isAdmin()) { ?>
						       <div class="edit" >
							       <button class="btn_edit" id="<?php p('btn_edit_' . $each['pid'])?>" >
								      <?php p($l->t('Edit')); ?>
							       </button>
						       </div>
						     <?php	} ?>
					     </div>
            </div>
				  </div>
				</div>
	     <?php 	} ?>
		 </div>
	<?php } ?>
</div>
