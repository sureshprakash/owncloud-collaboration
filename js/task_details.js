/**
* This file is licensed under the Affero General Public License version 3 or
* later.
* See the COPYING-README file.
*/

$(document).ready(function()
{
	$("#collaboration_content").css({marginLeft: $("#tabs_collaboration").outerWidth(true), padding: '10px'});
	
	if($('#current_task_status').text().indexOf('Cancelled') == -1 && $('#current_task_status').text().indexOf('Verified') == -1)
	{
		$("#edit_task_btn").removeAttr('disabled');
	}
});
