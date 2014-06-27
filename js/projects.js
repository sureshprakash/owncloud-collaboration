/**
* This file is licensed under the Affero General Public License version 3 or
* later.
* See the COPYING-README file.
*/

var num_projects = 0;

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
	$("#projects img").css({opacity: 1.0});
	$("#projects a").css({color: 'black', backgroundColor: '#E9E3E3'});
	$("#collaboration_content").css({marginLeft: $("#tabs_collaboration").outerWidth(true), padding: '10px'});

	if($("#search_list").val() == 'ALL' || $("#search_list").val() == '')
	{
		$(window).bind('scroll', checkToLoadMoreProjects);
	}

	num_projects = $(".unit").length;
	
	$("#search_list").chosen(
	{
		no_results_text: t('collaboration', 'No matches found!'),
		disable_search_threshold: 5
	});
	
	$("#search_list").bind('change', function() { $("#search_form").submit(); });
	
	$(".btn_edit").bind('click', editProject);
});

function checkToLoadMoreProjects()
{
	var beforeBottomInPx = 150;
	
	if(($(window).scrollTop() + $(window).height()) > (getDocHeight() - beforeBottomInPx))
	{
   		loadMoreProjects();
   	}
}

function loadMoreProjects()
{
	// To avoid loading posts more than once
	$(window).unbind('scroll');
	
	var div = document.createElement('div');
	$(div).css({width: '100%', textAlign: 'center'});
	div.setAttribute('id', 'img_loading');
	
	var img = document.createElement('img');
	img.setAttribute('src', OC.filePath('core', 'img', 'loading.gif'));
	$(img).css({width: '15px', height: '15px'});
	
	$(div).append(img);
	$("#projects_list").append(div);
	
	loadProjects();
}

function loadProjects()
{
	var request_count = 10;
	
	$.ajax(
	{
		type: 'POST',
		url: OC.filePath('collaboration', 'ajax', 'load_project_details.php'),
		data: 
		{
			member: oc_current_user,
			start: num_projects,
			count: request_count,
		},
		success: 
		function(data)
		{
			if (data.status == 'success')
			{
				$("#projects_list").append(data.projects);
				
				$(".btn_edit").unbind('click');
				$(".btn_edit").bind('click', editProject);
				
				var projects_cnt = $(".unit").length;
				
				if((num_projects + request_count) == projects_cnt)
				{
					$(window).bind('scroll', checkToLoadMoreProjects);
				}
				
				num_projects = projects_cnt;
			}
			
			$("#img_loading").remove();
		}
	});
}

function editProject(event)
{
	$('#collaboration_content').append('<form id="edit_project" method="post" action="' + OC.Router.generate('collaboration_route', {rel_path: 'update_project'}) + '" ><input type="hidden" name="pid" value="' + this.id.substr(9) + '" /></form>');
	$("#edit_project").submit();
}
