/**
* This file is licensed under the Affero General Public License version 3 or
* later.
* See the COPYING-README file.
*/

String.prototype.toTitleCase = function () 
{
	var A = this.split(' '), B = [];
	for (var i = 0; A[i] !== undefined; i++) 
	{
		B[B.length] = A[i].substr(0, 1).toUpperCase() + A[i].substr(1).toLowerCase();
	}
	return B.join(' ');
}

$(document).ready(function()
{	
	$("#collaboration_content").css({marginLeft: $("#tabs_collaboration").outerWidth(true), padding: '10px'});
	
	if($("#project").length != 0)
	{
		loadMembersList();
	}
	
	$("#deadline_time").datetimepicker(
	{
		minDate: 1,
		timeFormat: 'HH:mm'
	});
	
	$("#project").bind("change", loadMembersList);
	
	$(".event_info").css({display: 'none'});
	
	$(".event").bind("change", function(event) 
	{
		if($(this).is(':checked'))
		{
			$('.event_info').css({display: 'none'});
			$('#event_info_' + this.id.substr(6)).css({display: 'inline'});
		}
	});
	
	if($('input[name="tid"]').length == 0)
	{
		$("#create_task img").css({opacity: 1.0});
		$("#create_task a").css({color: 'black', backgroundColor: '#E9E3E3'});
		
		$("#project").chosen(
		{
			no_results_text: t('collaboration', 'No matches found!'),
			disable_search_threshold: 5
		});
	}
	else
	{
		var status = $('#task_status').text();
		
		if(status.indexOf('Cancelled') != -1 || status.indexOf('Verified') != -1)
		{
			// OC.Router.generate() will work only after loading the page completely
			setTimeout(function()
			{
				$('#collaboration_content').append('<form id="task_details" method="post" action="" >' +
				'<input type="hidden" name="tid" value="' + $('#tid').val() + '" />' +
				'<input type="hidden" name="msg" value="' + 
				((status.indexOf('Cancelled') != -1)?
				t('collaboration', 'You cannot make changes on a cancelled task.'):
				t('collaboration', 'You cannot make changes on a verified task.')) + '" />' +
				'</form>');
			
				$('#task_details').attr('action', OC.Router.generate('collaboration_route', {rel_path: 'task_details'}));
				$('#task_details').submit();
			}, 10);
		}
	}
});

function loadMembersList()
{
	showLoadingImage('load_members');
	
	$.ajax(
	{
		type: 'POST',
		url: OC.filePath('collaboration', 'ajax', 'load_members.php'),
		data: {'pid': $("#project").val()},
		success: 
		function(data)
		{
			$("#load_members").empty();
			
			if (data.status == 'success')
			{
				var list = '';
				var user = oc_current_user.toLowerCase();
				
				for(var role in data.members)
				{
					list += '<optgroup label="' + role + '">';
					
					for(var member in data.members[role])
					{
						list += '<option value="' + data.members[role][member].toTitleCase() + '" ' + ((data.members[role][member].toLowerCase() == user)? ' selected="selected"': '') + ' >' + data.members[role][member].toTitleCase() + '</option>';
					}
					
					list += '</optgroup>';
				}
				
				$("#members").html(list);
				
				$("#skillset_link").attr('href', OC.Router.generate('collaboration_route', {rel_path: 'skillset_details'}) + '?project=' + $("#project").val());
				
				var deadline = '';
				
				$.ajax(
				{
					type: 'POST',
					url: OC.filePath('collaboration', 'ajax', 'get_project_deadline.php'),
					data: {'pid': $("#project").val()},
					success: 
					function(inner)
					{
						if(inner.status == 'success')
						{
							var old_time = $("#deadline_time").datetimepicker('getDate');
							$("#deadline_time").datetimepicker('option', 'maxDate', new Date(inner.deadline.split(' ')[0]));
							$("#deadline_time").datetimepicker('setDate', old_time);
						}
					}
				});
			}
			else
			{
				alert('An error occurred in loading members list. Please try again later.');
				$("#members").empty();
			}
		}
	});
}


function showLoadingImage(id)
{
	var img = document.createElement('img');
	img.setAttribute('src', OC.imagePath('core', 'loading.gif'));
	$(img).css({width: '15px', height: '15px'});
	
	$("#" + id).append(img);
}

