/**
* This file is licensed under the Affero General Public License version 3 or
* later.
* See the COPYING-README file.
*/

$(document).ready(function()
{
	var obj = {};
	$("#ev_form input").each(function()
	{
		obj[$(this).attr('name')] = $(this).val();
	});
	
	if($("#create_new").val() == 'true')
	{
		$.ajax(
		{
			url: OC.filePath('collaboration', 'ajax', 'new_event.php'),
			type: 'POST',
			data: obj,
			success:
			function(data)
			{
				$("#event_id").val(data.event_id);
			
				if($("#member").val() != "")
				{
					OC.Share.share('event', data.event_id, 0, $("#member").val(), OC.PERMISSION_READ);
				}
			
				$("#task_schedule").submit();
			}
		});
	}
	else
	{
		$.ajax(
		{
			url: OC.filePath('collaboration', 'ajax', 'edit_event.php'),
			type: 'POST',
			data: obj,
			success:
			function(data)
			{
				$("#task_schedule").submit();
			}
		});
	}
});
