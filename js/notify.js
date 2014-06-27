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
	$("#notify img").css({opacity: 1.0});
	$("#notify a").css({color: 'black', backgroundColor: '#E9E3E3'});
	$("#collaboration_content").css({marginLeft: $("#tabs_collaboration").outerWidth(true), padding: '10px'});
	
	if($("#notification_form").length != 0)
	{
		loadMembersList();
	}
	
	$("#project").chosen(
	{
		no_results_text: t('collaboration', 'No matches found!'),
		disable_search_threshold: 5
	});
	$("#project").bind("change", loadMembersList);
	$("#notify_to").multiselect(
	{
		show: ["slide", 200], 
		hide: ["slide", 200], 
		noneSelectedText: t('collaboration', 'Do not notify anybody'),
		selectedText: t('collaboration', 'Notify only to the members specified below'),
		click: alterList,
		checkAll: updateList,
		uncheckAll: updateList,
		optgrouptoggle: alterGroupList,
		checkAllText: t('collaboration', 'Select all'),
		uncheckAllText: t('collaboration', 'Select none')
	}).multiselectfilter();
	
	$("#member_container").bind('click', function()
	{
		$("#notify_to").multiselect('open');
	});
	
	$("#notify_all").bind('change', enableOrDisableDD);
	
	$("#btn_submit").bind('click', correctForm);
	
	enableOrDisableDD();
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
				
				for(var role in data.members)
				{
					list += '<optgroup label="' + role + '">';
					
					for(var member in data.members[role])
					{
						list += '<option value="' + data.members[role][member].toTitleCase() + '"selected="selected">' + data.members[role][member].toTitleCase() + '</option>';
					}
					
					list += '</optgroup>';
				}
				
				$("#notify_to").html(list);
				$("#notify_to").multiselect('refresh');
			}
			else
			{
				alert('An error occurred in loading members list. Please try again later.');
				$("#notify_to").empty();
				$("#notify_to").multiselect('refresh');
			}
			
			updateList();
		}
	});
}

function alterList(event, ui)
{
	if(ui.checked)
	{
		checkAllMember(ui.value);
	}
	else
	{
		uncheckAllMember(ui.value);
	}
	
	// Refresh should take place after the default action
	setTimeout(
	function()
	{
		$("#notify_to").multiselect('refresh');
		updateList();
	}, 20);
}

function alterGroupList(event, ui)
{
	var members = $.map(ui.inputs, 
	function(checkbox)
	{
         return checkbox.value;
    });
      
    if(ui.checked)
    {
	  	for(var i = 0; i < members.length; i++)
	  	{
	  		checkAllMember(members[i]);
	  	}
	}
	else
	{
		for(var i = 0; i < members.length; i++)
	  	{
	  		uncheckAllMember(members[i]);
	  	}
	}
	
	$("#notify_to").multiselect('refresh');
	updateList();
}

function checkUncheck(member, check)
{
	$("#notify_to option[value='" + member + "']").each(function()
	{
		if(check)
			$(this).attr('selected', 'selected');
		else
			$(this).removeAttr('selected');
	});
	
	$("#notify_to").multiselect('refresh');
}

function checkAllMember(member)
{
	checkUncheck(member, true);
}

function uncheckAllMember(member)
{
	checkUncheck(member, false);
}

function updateList()
{
	var members = $("#notify_to").multiselect("getChecked").map(
	function()
	{
   		return this.value;    
	}).get();
	
	if(members.length == 0)
	{
		$("#members_list").empty();
		$("#members_list").css({display: 'none'});
		return;
	}
	else
	{
		$("#members_list").css({display: 'block'});
	}
	
	members = getUnique(members);
	
	$("#members_list").empty();
	
	for(var i = 0; i < members.length; i++)
	{
		$("#members_list").html($("#members_list").html() + '<option>' + members[i] + '</option>');
	}
}

function getUnique(array)
{
	var unique_array = [];

	array = array.sort();
	
	unique_array[0] = array[0];
	
	for(var i = 1, j = 1; i < array.length; i++)
	{
		if(array[i] != array[i - 1])
		{
			unique_array[j++] = array[i];
		}
	}
	
	return unique_array;
}

function enableOrDisableDD(event)
{
	if(($("#notify_all").attr('checked')) == 'checked')
	{
		$("#notify_to").attr('disabled', 'disabled');
		$("#notify_to").multiselect('disable');
		$("#member_container").css({'display': 'none'});
	}
	else
	{
		$("#notify_to").removeAttr('disabled');
		$("#notify_to").multiselect('enable');
		$("#member_container").css({'display': 'block'});
		updateList();
	}
	
}

function correctForm(event)
{
	if($("#notify_all").attr('checked') != 'checked')
	{
		$("#members_list").removeAttr('disabled');
		$("#members_list option").each(
		function()
		{
			$(this).attr('selected', 'selected');
		});
	}
	
	$("#notification_form").attr('action', OC.Router.generate('collaboration_route', {'rel_path': 'submit_notify'}));
	$("#notification_form").submit();
}

function showLoadingImage(id)
{
	var img = document.createElement('img');
	img.setAttribute('src', OC.imagePath('core', 'loading.gif'));
	$(img).css({width: '15px', height: '15px'});
	
	$("#" + id).append(img);
}
