/**
* This file is licensed under the Affero General Public License version 3 or
* later.
* See the COPYING-README file.
*/

var num_tasks = 0;

function getDocHeight() 
{
    return Math.max(
        Math.max(document.body.scrollHeight, document.documentElement.scrollHeight),
        Math.max(document.body.offsetHeight, document.documentElement.offsetHeight),
        Math.max(document.body.clientHeight, document.documentElement.clientHeight)
    );
}

$(document).ready(function()
{
	$("#tasks img").css({opacity: 1.0});
	$("#tasks a").css({color: 'black', backgroundColor: '#E9E3E3'});
	$("#collaboration_content").css({marginLeft: $("#tabs_collaboration").outerWidth(true), padding: '10px'});

	$(window).bind('scroll', checkToLoadMoreTasks);

	num_tasks = $(".unit").length;
	
	$("#projects_list").chosen(
	{
		no_results_text: t('collaboration', 'No matches found!'),
		disable_search_threshold: 5
	});
	
	$("#status_list").chosen(
	{
		no_results_text: t('collaboration', 'No matches found!'),
		disable_search_threshold: 8
	});
	
	$("#projects_list").bind('change', filter);
	$("#status_list").bind('change', filter);
	
	$('#assign_filter input[type="checkbox"]').bind('change', filter);
	
	$(".btn_edit").bind('click', editTask);
	
	$(".status_event button").bind('click', checkReason);
	
	$("#status_updation_form").dialog(
	{
		autoOpen: false,
		height: 150,
		width: 400,
		modal: true,
		resizable: false,
		title: t('collaboration', 'Reason')
	});
});

function editTask(event)
{
	$('#collaboration_content').append('<form id="edit_task" method="post" action="' + OC.Router.generate('collaboration_route', {rel_path: 'update_task'}) + '" ><input type="hidden" name="tid" value="' + this.id.substr(9) + '" /></form>');
	$("#edit_task").submit();

}

function checkToLoadMoreTasks()
{
	var beforeBottomInPx = 150;
	
	if(($(window).scrollTop() + $(window).height()) > (getDocHeight() - beforeBottomInPx))
	{
   		loadMoreTasks();
   	}
}

function loadMoreTasks()
{
	// To avoid loading tasks more than once
	$(window).unbind('scroll');
	showLoadingImage('tasks_list');
	loadTasks();
}

function loadTasks()
{
	var request_count = 10;
	
	$.ajax(
	{
		type: 'POST',
		url: OC.filePath('collaboration', 'ajax', 'load_tasks.php'),
		data: 
		{
			start: num_tasks,
			count: request_count,
			project: (($("#projects_list").val() == 'ALL')? '': $("#projects_list").val()),
			status: (($("#status_list").val() == 'ALL')? '': $("#status_list").val()),
			assigned_to: $('#assigned_to').is(':checked'),
			assigned_by: $('#assigned_by').is(':checked')
		},
		success: 
		function(data)
		{
			$("#img_loading").remove();
			
			if (data.status == 'success')
			{
				$("#tasks_list").append(data.tasks);
				
				var task_cnt = $(".unit").length;
				
				if((num_tasks + request_count) == task_cnt)
				{
					$(window).bind('scroll', checkToLoadMoreTasks);
				}
				
				num_tasks = task_cnt;
				$(".btn_edit").bind('click', editTask);
			}
			else
			{
				alert('Unable to read more tasks. Please try again later.');
			}
			
		}
	});
}


function filter(event)
{
	if(this.id == 'assigned_by' && !$('#assigned_by').is(':checked'))
	{
		if(!$('#assigned_to').is(':checked'))
		{
			$('#assigned_to').attr('checked', 'on');
		}
	}
	
	if(this.id == 'assigned_to' && !$('#assigned_to').is(':checked'))
	{
		if(!$('#assigned_by').is(':checked'))
		{
			$('#assigned_by').attr('checked', 'on');
		}
	}
	
	$("#filter_form").submit();
}

function checkReason(ev)
{
	$("#change_status").val($(this).val());
	var elem = this;
	
	switch($(this).val())
	{
		case 'Reject':
		case 'Hold':
			$("#status_updation_form").dialog('open');
			$("#change_status").bind('click', function() 
			{	
				if($("#reason").val().trim() != '')
				{
					$("#status_updation_form").dialog('close');
					changeStatus(elem, true);
				}
				else
				{
					alert('Reason is required');
				}
			});
		break;
		
		default:
			changeStatus(elem, false);
	}
	
}

function changeStatus(elem, reason_required)
{
	var img = document.createElement('img');
	img.setAttribute('src', OC.imagePath('core', 'loading.gif'));
	$(img).css({width: '15px', height: '15px'});
	
	$(elem).parent().prepend(img);
	
	$.ajax(
	{
		url: OC.filePath('collaboration', 'ajax', 'change_status.php'),
		data:
		{
			tid: $(elem).parent().data('tid'),
			event: $(elem).val(),
			reason: ((reason_required)? $("#reason").val().trim(): '')
		},
		type: 'POST',
		success:
		function(data)
		{
			if(data.status == 'success')
			{
				window.location.reload();
			}
			else
			{
				$(this).parent().children('img').remove();
				alert('Unable to change status. Kindly try again later.');
			}
		}
	});
	
}

function showLoadingImage(id)
{
	var div = document.createElement('div');
	$(div).css({width: '100%', textAlign: 'center'});
	div.setAttribute('id', 'img_loading');
	
	var img = document.createElement('img');
	img.setAttribute('src', OC.imagePath('core', 'loading.gif'));
	$(img).css({width: '15px', height: '15px'});
	
	$(div).append(img);
	$("#" + id).append(div);
}
