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
    <h1 id="title" ><?php p($l->t('Dashboard')); ?></h1>
			<form id="filter_form" class='ch-right' action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="get" >
				<select id="projects_list" name="project" class="chzen-select" >
					<option value="ALL" <?php if(!isset($_['project']) || $_['project'] == 'ALL') { print_unescaped('selected="selected"'); } ?> ><?php p($l->t('Filter By Project')); ?></option>
					 <?php
					  	foreach($projects as $pid => $ptitle) {
					  	  print_unescaped('<option value="' . $ptitle . '" ' . (isset($_['project']) && (strtolower($_['project']) == strtolower($ptitle))? 'selected="selected"': '' ) . ' >' . $ptitle . '</option>');
						  }
					 ?>
			   </select>
		  </form>
   </div>
	 <div id="content-body" >
   	<?php
      if(!isset($_['posts']) || count($_['posts']) === 0 || count($_['posts'][0]) === 0) {
		  	print_unescaped('<div class="clear-both" >');
		  	p($l->t('Sorry, no posts is available to display.'));
		  	print_unescaped('</div>');
	  	} else {

			foreach($_['posts'] as $each){ ?>
			  <div class="unit">
				  <div class="cb_title">
					  <?php
							if(isset($each['proj_title']) && !is_null($each['proj_title'])) {
								p($l->t($each['proj_title']));
						  } else {
								print_unescaped('<p>&nbsp;</p>');
							}
						?>
				  </div>
					<div class='clear-both-np'>
						<div class="cb-wrapper">
						  <div class="details">
						    <p>
									<b>Post Status</b>
									<?php print_unescaped($each['title']); ?>
						    </p>
						  </div>
				      <div class="contents">
					     <?php p($each['content']); ?>
			  	    </div>
              <div class="cb-date">
	          <?php
	            	$datetime = explode(' ', $each['time']);
		           p($l->t('On %s at %s by %s', array($l->l('date', $datetime[0]), $l->l('time', $datetime[1]), $each['creator'])));
	          ?>
            </div>
            <div class="comment" >
	            <button class="btn_comment" id="<?php p('btn_comment_' . $each['post_id'])?>" >
		             <?php	p($l->t('Comments') . ' (' . OC_Collaboration_Comment::getCommentCount($each['post_id']) . ')');	?>
	            </button>
            </div>
				  </div>
			  </div></div>
	    <?php
			}
		}
	?>
		</div>
</div>
