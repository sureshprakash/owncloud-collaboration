/**
* This file is licensed under the Affero General Public License version 3 or
* later.
* See the COPYING-README file.
*/

$(document).ready(function()
{
	$("#collaboration_content").css({marginLeft: $("#tabs_collaboration").outerWidth(true), padding: '10px'});
	
	if($('#project_status').text().indexOf('Completed') == -1)
	{
		$("#edit_project_btn").removeAttr('disabled');
	}
});
