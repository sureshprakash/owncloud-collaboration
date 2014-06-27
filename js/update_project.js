/**
* This file is licensed under the Affero General Public License version 3 or
* later.
* See the COPYING-README file.
*/

var no_created = 1;
var member_count = 0;

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
	
	$("#btn_add_member").bind("click", addMember);
	$("#deadline").datepicker(
	{
		dateFormat: 'm/d/yy',
		minDate: 10,
		maxDate: '+40Y',
		changeMonth: true,
		changeYear: true
	});

	$("#project_title").bind("focus", function(event) { $("#error_msg").empty(); });
	$("#project_title").bind("blur", projectCheck);
	
	$("#project_updation_form").bind("submit", pTitleAvailable);
	
	$(".delete_old_member").each(function()
	{
		$(this).bind("mouseover", function(event) 
		{ 
			$(this).attr('src', OC.imagePath('core', 'actions/delete-hover.png')); 
		});
	
		$(this).bind("mouseout", function(event) 
		{ 
			$(this).attr('src', OC.imagePath('core', 'actions/delete.png')); 
		});
		
		$(this).bind("click", removeOldMember);
	});
	
		// Creating new project or updating existing project
	if($('input[name="pid"]').length == 0)
	{
		$("#create_project img").css({opacity: 1.0});
		$("#create_project a").css({color: 'black', backgroundColor: '#E9E3E3'});
	}
	else
	{
		if($("#project_completed").is(':checked'))
		{
			// OC.Router.generate() will work only after loading the page completely
			setTimeout(function()
			{
				$('#collaboration_content').append('<form id="project_details" method="post" action="" >' +
				'<input type="hidden" name="pid" value="' + $('#pid').val() + '" />' +
				'<input type="hidden" name="msg" value="' + t('collaboration', 'You cannot make changes on a completed project') + '" />' +
				'</form>');
			
				$('#project_details').attr('action', OC.Router.generate('collaboration_route', {rel_path: 'project_details'}));
				$('#project_details').submit();
			}, 10);
		}
	}
});

function addMember()
{
	if(member_count != 0)
	{
		document.getElementById('add_member' + (no_created - 1)).removeChild(document.getElementById('btn_add_member'));
	}
	else
	{
		document.getElementById('add_member0').removeChild(document.getElementById('btn_add_member'));
	}
	
	var new_member = 
			"<td>\
				<input class=\"mem_name\" type=\"text\" id=\"mem_name" + no_created + "\" name=\"mem_name" + no_created + "\" placeholder=\"" + t('collaboration', 'Username') + "\" pattern=\"[a-zA-Z0-9\\s_\\.@\\-]+\" title=\"Allowed characters are: 'a-z', 'A-Z', '0-9', <space> and '_.@-'\" required />\
			</td>\
			<td>\
				<input class=\"mem_role\" type=\"text\" id=\"mem_role" + no_created + "\" name=\"mem_role" + no_created + "\" placeholder=\"" + t('collaboration', 'Role') + "\" pattern=\"[a-zA-Z0-9\\s]+\" title=\"Allowed characters are: 'a-z', 'A-Z', '0-9' and <space>\" required />\
			</td>\
			<td>\
				<input class=\"mem_email\" type=\"email\" id=\"mem_email" + no_created + "\" name=\"mem_email" + no_created + "\" placeholder=\"" + t('collaboration', 'Email') + "\" style=\"width: 200px;\" required />\
			</td>\
			<td>\
				<input class=\"mem_mobile\" type=\"text\" id=\"mem_mobile" + no_created + "\" name=\"mem_mobile" + no_created + "\" placeholder=\"" + t('collaboration', 'Mobile') + "\" style=\"width: 200px;\" required maxlength=\"10\" />\
				<span id=\"load_contact_" + no_created + "\"></span>\
			</td>\
			<td>\
				<img title=\"Delete\" src=\"" + OC.imagePath('core', 'actions/delete.png') + "\" id=\"close" + no_created + "\" width=\"15px\" height=\"15px\" />\
			</td>\
			<td id=\"add_member" + no_created + "\" >\
				<input type=\"button\" value=\"" + t('collaboration', 'Add Member') + "\" id=\"btn_add_member\" >\
			</td>";

	var tr = document.createElement("tr");
	tr.setAttribute("id", "member" + no_created);
	tr.innerHTML = new_member;

	var insert_into = document.getElementById("details");
	insert_into.appendChild(tr);

	var btn_rem = $("#close" + no_created);
	btn_rem.bind("click", function(event) { removeMember(this.id.substring(5)); });
	btn_rem.bind("mouseover", function(event) { this.setAttribute('src', OC.imagePath('core', 'actions/delete-hover.png')); });
	btn_rem.bind("mouseout", function(event) { this.setAttribute('src', OC.imagePath('core', 'actions/delete.png')); });

	$("#btn_add_member").bind("click", addMember);
	
	$("#mem_name" + no_created).focus();
	$("#mem_name" + no_created).bind('blur', fetchMailID);

	no_created++;
	member_count++;
}

function removeMember(memid)
{	
	id = parseInt(memid);
	if(id + 1 == no_created && member_count != 1)
	{
		do
		{
			id--;
		}while(document.getElementById("member"+id) == null && id != 1);
		
		document.getElementsByName("mem_name" + memid)[0].value = document.getElementsByName("mem_name" + id)[0].value;
		document.getElementsByName("mem_role" + memid)[0].value = document.getElementsByName("mem_role" + id)[0].value;
		document.getElementsByName("mem_email" + memid)[0].value = document.getElementsByName("mem_email" + id)[0].value;
		document.getElementsByName("mem_mobile" + memid)[0].value = document.getElementsByName("mem_mobile" + id)[0].value;
		
		document.getElementById("close" + memid).setAttribute('src', OC.imagePath('core', 'actions/delete.png'));
	}

	document.getElementById("details").removeChild(document.getElementById("member"+id));
	member_count--;

	if(member_count == 0)
	{
		document.getElementById("add_member0").innerHTML = "<input type=\"button\" value=\"" + t('collaboration', 'Add Member') + "\" id=\"btn_add_member\" >";
		$("#btn_add_member").bind("click", addMember);
	}
}

function projectCheck()
{
	$('#error_msg').empty();
	showLoadingImage('#error_msg');
	
	$.ajax(
	{
		type: 'POST',
		url: OC.filePath('collaboration', 'ajax', 'project_check.php'),
		data: {'title': $("#project_title").val()},
		success: 
		function(data)
		{
			$("#error_msg").empty();
			
			if (data.status == 'success')
			{
				if((($("input[name='pid']").length == 0) && (data.pid != -1)) || (($("input[name='pid']").length != 0) && (data.pid != -1) && (data.pid != $("input[name='pid']").val())))
				{
					$("#error_msg").html(t('collaboration', 'Another project with this title already exists.'));
				}
			}
		}
	});
}

function pTitleAvailable()
{	
	var accepted = false;
	$('#error_msg').empty();
	showLoadingImage('#error_msg');
	
	$.ajax(
	{
		type: 'POST',
		url: OC.filePath('collaboration', 'ajax', 'project_check.php'),
		async: false,
		data: {'title': $("#project_title").val()},
		success: 
		function(data)
		{
			$("#error_msg").empty();
			
			if (data.status == 'success')
			{
				if((($("input[name='pid']").length == 0) && (data.pid != -1)) || (($("input[name='pid']").length != 0) && (data.pid != -1) && (data.pid != $("input[name='pid']").val())))
				{
					$("#error_msg").html(t('collaboration', 'Another project with this title already exists.'));
					accepted = false;
				}
				else
				{
					$(".mem_email").removeAttr('disabled');
					$(".mem_name").each(function()
					{
						$(this).val($(this).val().toTitleCase());
					});
					
					accepted = true;
				}
			}
			else
			{
				alert('Unable to create project now. Please try again later.');
				accepted = false;
			}
		}
	});
	
	if(($('input[name="pid"]').length != 0) && ($('#project_completed').is(':checked')))
	{
		$.ajax(
		{
			type: 'POST',
			url: OC.filePath('collaboration', 'ajax', 'check_pending_tasks.php'),
			async: false,
			data: 
			{
				'pid': $("#pid").val()
			},
			success: 
			function(data)
			{
				$("#error_msg").empty();
			
				if (data.status == 'success')
				{
					if(data.has_pending_tasks)
					{
						$("#error_msg").html(t('collaboration', 'Project with pending tasks cannot be completed. See report for more details'));
						accepted = false;
					}
					else
					{
						accepted = true;
					}
				}
				else
				{
					alert('Unable to create project now. Please try again later.');
					accepted = false;
				}
			}
		});
	}
	
	return accepted;
}

function removeOldMember(event)
{
	var mem = $(this).data('member');
	var span = $(this).parent();
	var div = $(span).parent();
	var rol = $(div).data('role');
	
	if(confirm('You are about to delete \'' + mem + '\' from \'' + rol + '\'. Member will be immediately deleted and cannot be undone.'))
	{
		showLoadingImage('#remove_old_member_loading_img');
		
		$.ajax(
		{
			type: 'POST',
			url: OC.filePath('collaboration', 'ajax', 'delete_member.php'),
			data:
			{
				member: mem,
				role: rol,
				pid: $('#pid').val()
			},
			success:
			function(data)
			{
				$('#remove_old_member_loading_img').empty();
				
				if(data.status == 'success' && data.delete_succeeded)
				{
					$(span).remove();
					
					if($(div).children().length == 1)
					{
						$(div).remove();
					}
				}
				else
				{
					alert('Unable to remove member now. Please try after some time.');
				}
			}
		});
	}
}

function showLoadingImage(selector)
{
	var img = document.createElement('img');
	img.setAttribute('src', OC.imagePath('core', 'loading.gif'));
	$(img).css({width: '15px', height: '15px'});
	
	$(selector).append(img);
}

function fetchMailID(event)
{
	var user = $(this).val().trim().toLowerCase();
	var id = this.id.substr(8);
	
	for(var i = 1; i < no_created; i++)
	{
		if(document.getElementById("member"+i) != null && i != id)
		{
			if($("#mem_name" + i).val().trim().toLowerCase() == user)
			{
				$("#mem_email" + id).val($("#mem_email" + i).val().trim());
				
				if($("#mem_email" + i).attr('disabled') == 'disabled')
				{
					$("#mem_email" + id).attr('disabled', 'disabled');
				}
				else
				{
					$("#mem_email" + id).removeAttr('disabled');
				}
				
				$("#mem_mobile" + id).val($("#mem_mobile" + i).val().trim());
				
				if($("#mem_mobile" + i).attr('disabled') == 'disabled')
				{
					$("#mem_mobile" + id).attr('disabled', 'disabled');
				}
				else
				{
					$("#mem_mobile" + id).removeAttr('disabled');
				}
			}
		}
		else
		{
			showLoadingImage('#load_contact_' + id);
			
			$.ajax(
			{
				type: 'POST',
				url: OC.filePath('collaboration', 'ajax', 'get_contact_details.php'),
				data:
				{
					member: $("#mem_name" + id).val().trim()
				},
				success:
				function(data)
				{
					$("#load_contact_" + id + " img").remove();
		 
					if(data.status == 'success')
					{
						if(data.email != null)
						{
							$("#mem_email" + id).val(data.email);
							$("#mem_email" + id).attr('disabled', 'disabled');
						}
						else
						{
							$("#mem_email" + id).removeAttr('disabled');
						}
						
						if(data.mobile != null)
						{
							$("#mem_mobile" + id).val(data.mobile);
							$("#mem_mobile" + id).attr('disabled', 'disabled');
						}
						else
						{
							$("#mem_mobile" + id).removeAttr('disabled');
						}
					}
					else
					{
						alert('An error occurred while fetching email. Please try again later');
					}
				}
			});
		}
	}
}
