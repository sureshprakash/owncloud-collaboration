/**
* This file is licensed under the Affero General Public License version 3 or
* later.
* See the COPYING-README file.
*/

var post_saved = false;
var comment_saved = false;

$(document).ready(function()
{
	$("#collaboration_content").css({marginLeft: $("#tabs_collaboration").outerWidth(true), padding: '10px'});
	
	$("a.edit").bind('click', edit);
	
	$("a.delete").bind('click', erase);
	
	$("#post_save_edit").bind('click', savePost);
	$("#post_cancel_edit").bind('click', closePostDialog);
	
	$("#comment_save_edit").bind('click', saveComment);
	$("#comment_cancel_edit").bind('click', closeCommentDialog);
	
	$("#submit_new_comment").bind('click', writeNewComment);
	
	$("#edit_post_dialog").dialog(
	{
      autoOpen: false,
      height: 550,
      width: 950,
      modal: true,
      resizable: false,
      title: t('collaboration', 'Edit Post'),
      beforeClose:
  	  function(event)
	  {
	      if(!post_saved && !confirm('You are going to exit without updating the post.'))
	  	  {
	  	      event.preventDefault();
	  	  }
	  }
    });
    
   	$("#edit_comment_dialog").dialog(
	{
      autoOpen: false,
      height: 550,
      width: 950,
      modal: true,
      resizable: false,
      title: t('collaboration', 'Edit Comment'),
	  beforeClose:
	  function(event)
	  {
	  	if(!comment_saved && !confirm('You are going to exit without updating the comment.'))
	  	{
	  		event.preventDefault();
	  	}
	  }
    });
    
    $("#project_details_link form a").bind('click', function()
    {
    	$(this).parent().submit();
    });
    
    $("#task_details_link form a").bind('click', function()
    {
    	$(this).parent().submit();
    });
});

function edit(event)
{	
	var id = $(this).data('collaborationId');
	
	post_saved = comment_saved = false;
	
	switch($(this).data('collaborationType'))
	{
		case 'post':			
			$("#updated_post_text").text($('#post_text').text().trim());
			$("#updated_title").val($('#title').text().trim());
			
			$("#post_save_edit").attr('data-collaboration-id', id);
			
			$("#edit_post_dialog").dialog('open');
			break;
			
		case 'comment':
			$("#updated_comment_text").text($('#comment_content_' + id).text().trim());
			
			$("#comment_save_edit").attr('data-collaboration-id', id);
			
			$("#edit_comment_dialog").dialog('open');
			break;
	}
}

function erase(event)
{
	var id = $(this).data('collaborationId');
	
	switch($(this).data('collaborationType'))
	{
		case 'post':			
			deletePost(id);
			break;
			
		case 'comment':
			deleteComment(id);
			break;
	}
}

function closePostDialog(event)
{
	$("#edit_post_dialog").dialog('close');
}

function closeCommentDialog(event)
{
	$("#edit_comment_dialog").dialog('close');
}

function savePost(event)
{
	if(!((new RegExp("^[a-zA-Z]([a-zA-Z0-9]\\s?(\\-\\s)?){2,98}[a-zA-Z0-9]$")).test($("#updated_title").val().trim())))
	{
		$("#edit_post_dialog .validate_message").text(t('collaboration', 'Post title should match the requested format'));
	}
	else if($("#updated_post_text").val().trim() == '')
	{
		$("#edit_post_dialog .validate_message").text(t('collaboration', 'Post content cannot be empty'));
	}
	else
	{
		$("#edit_post_dialog .validate_message").text('');
		if(confirm('Updating the post will delete all the previous comments.'))
		{
			var id = $(this).data('collaborationId');
	
			showLoadingImage('#post_details .edit_delete_post');
	
			$.ajax(
			{
				type: 'POST',
				url: OC.filePath('collaboration', 'ajax', 'edit_post.php'),
				data: 
				{
					post_id: id,			
					title: $("#updated_title").val(),
					content: $("#updated_post_text").val()
				},
				success: 
				function(data)
				{
					$("#post_details .edit_delete_post #loading_img").remove();
			
					if (data.status == 'success' && data.edit_succeeded == true)
					{
						window.location.reload();
						
					/*	Do these things if you don't want to reload. But, last updated time will have to be updated.
						$("#title").text($("#updated_title").val());
						$("#post_text").text($("#updated_post_text").val());
						$("#comments").remove(); */
					}
					else
					{
						alert('Unable to update comment. Please try again later.');
					}
				}
			});
		
			post_saved = true;
			$("#edit_post_dialog").dialog('close');
		}
	}
}

function saveComment(event)
{
	if($("#updated_comment_text").val().trim() == '')
	{
		$("#edit_comment_dialog .validate_message").text(t('collaboration', 'Comment cannot be empty'));
	}
	else
	{
		var id = $(this).data('collaborationId');
		$("#edit_comment_dialog .validate_message").text('');

		showLoadingImage("#comment_" + id + " .edit_delete_comment");
	
		$.ajax(
		{
			type: 'POST',
			url: OC.filePath('collaboration', 'ajax', 'edit_comment.php'),
			data: 
			{
				comment_id: id,			
				content: $("#updated_comment_text").val().trim()
			},
			success: 
			function(data)
			{
				$("#comment_" + id + " .edit_delete_comment #loading_img").remove();
			
				if (data.status == 'success' && data.edit_succeeded == true)
				{
					$("#comment_content_" + id).text($("#updated_comment_text").val());
					$("#comment_" + id + " .updated_time").text(data.updated_time);
				}
				else
				{
					alert('Unable to update comment. Please try again later.');
				}
			}
		});
	
		comment_saved = true;
		$("#edit_comment_dialog").dialog('close');
	}
}

function deletePost(id)
{
	if(confirm('Deleting the post cannot be undone.'))
	{
		showLoadingImage('#post_details .edit_delete_post');
		
		$.ajax(
		{
			type: 'POST',
			url: OC.filePath('collaboration', 'ajax', 'delete_post.php'),
			data: 
			{
				post_id: id
			},
			success: 
			function(data)
			{
				if (data.status == 'success' && data.delete_succeeded == true)
				{
					window.location.href = OC.Router.generate('collaboration_route', {rel_path: 'dashboard'});
				}
				else
				{
					$("#post_details .edit_delete_post #loading_img").remove();
					alert('Unable to delete post. Please try again later.');
				}
			}
		});
	}
}

function deleteComment(id)
{
	if(confirm('Deleting the comment cannot be undone.'))
	{
		showLoadingImage("#comment_" + id + " .edit_delete_comment");
	
		$.ajax(
		{
			type: 'POST',
			url: OC.filePath('collaboration', 'ajax', 'delete_comment.php'),
			data: 
			{
				comment_id: id
			},
			success: 
			function(data)
			{
				if (data.status == 'success' && data.delete_succeeded == true)
				{
					$("#comment_" + id).remove();
				}
				else
				{
					$("#comment_" + id + " .edit_delete_comment #loading_img").remove();
					alert('Unable to delete comment. Please try again later.');
				}
			}
		});
	}
}

function showLoadingImage(selector)
{
	var img = document.createElement('img');
	img.setAttribute('src', OC.imagePath('core', 'loading.gif'));
	$(img).attr('id', 'loading_img');
	$(img).css({width: '15px', height: '15px'});
	
	$(selector).append('&nbsp;&nbsp;').append(img);
}

function writeNewComment()
{
	if($("#new_comment_content").val().trim() == '')
	{
		$('#new_comment .validate_message').text(t('collaboration', 'Comment cannot be empty'));
	}
	else
	{
		showLoadingImage('#new_comment .validate_message');
		
		$.ajax(
		{
			type: 'POST',
			url: OC.filePath('collaboration', 'ajax', 'create_comment.php'),
			data: 
			{
				content: $("#new_comment_content").val().trim(),
				post_id: $("#post_details").data('collaborationId')
			},
			success: 
			function(data)
			{
				$("#new_comment .validate_message #loading_img").remove();
			
				if (data.status == 'success')
				{
					$("#comments").append(data.creation_string);
					
					// To avoid double binding
					$("a.edit").unbind('click');
					$("a.delete").unbind('click');
					
					$("a.edit").bind('click', edit);
					$("a.delete").bind('click', erase);
				}
				else
				{
					alert('Unable to save comment. Please try again later');
				}
			
				$("#new_comment_content").val('');
			}
		});
	}
}
