/**
* This file is licensed under the Affero General Public License version 3 or
* later.
* See the COPYING-README file.
*/

var num_posts = 0;

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
	$("#dashboard img").css({opacity: 1.0});
	$("#dashboard a").css({color: 'black', backgroundColor: '#E9E3E3'});
	$("#collaboration_content").css({marginLeft: $("#tabs_collaboration").outerWidth(true), padding: '10px'});
	
	$(".details").css({height: $(".creation_details").outerHeight()});

	$(window).bind('scroll', checkToLoadMorePosts);

	num_posts = $(".unit").length;
		
	$("#projects_list").chosen(
	{
		no_results_text: t('collaboration', 'No matches found!'),
		disable_search_threshold: 5
	});
	
	$("#projects_list").bind('change', filter);
	
	$(".btn_comment").bind('click', function() { comment(this.id.substring(12)); });
});

function checkToLoadMorePosts()
{
	var beforeBottomInPx = 150;
	
	if(($(window).scrollTop() + $(window).height()) > (getDocHeight() - beforeBottomInPx))
	{
   		loadMorePosts();
   	}
}

function loadMorePosts()
{
	// To avoid loading posts more than once
	$(window).unbind('scroll');
	showLoadingImage('posts');
	loadPosts();
}

function loadPosts()
{
	var request_count = 10;
	
	$.ajax(
	{
		type: 'POST',
		url: OC.filePath('collaboration', 'ajax', 'load_posts.php'),
		data: 
		{
			member: oc_current_user,
			start: num_posts,
			count: request_count,
			project: (($("#projects_list").val() == 'ALL')? '': $("#projects_list").val())
		},
		success: 
		function(data)
		{
			$("#img_loading").remove();

			if (data.status == 'success')
			{
				$("#posts").append(data.posts);
				
				var post_cnt = $(".unit").length;
				
				if((num_posts + request_count) == post_cnt)
				{
					$(window).bind('scroll', checkToLoadMorePosts);
				}
				
				num_posts = post_cnt;
				$(".btn_comment").bind('click', function() { comment(this.id.substring(12)); });
				$(".details").css({height: $(".creation_details").outerHeight()});
			}
			else
			{
				alert('Unable to read more posts. Please try again later.');
			}
			
		}
	});
}

/*
function loadProjectsList()
{
	$.ajax(
	{
		type: 'POST',
		url: OC.filePath('collaboration', 'ajax', 'load_projects.php'),
		data: {},
		success: 
		function(data)
		{
			if (data.status == 'success')
			{
				var list = 'Filter by project: <select id="projects_list" ><option value="-1" selected="selected" >ALL</option>';
				
				for(var project in data.projects)
				{
					list += '<option value="' + project + '" >' + data.projects[project] + '</option>';
				}
				
				list += '</select>';
				
				$("#project_list_container").html(list);
			}
		}
	});
}
*/

function filter()
{
	$("#filter_form").submit();
}

function comment(id)
{ 
		window.location.href = OC.Router.generate('collaboration_route', {'rel_path': 'comment'}) + '?post=' + id; 
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
