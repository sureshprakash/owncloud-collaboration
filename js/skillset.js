/**
* This file is licensed under the Affero General Public License version 3 or
* later.
* See the COPYING-README file.
*/

var no_created = 1;
var skill_count = 0;

$(document).ready(function()
{
	$("#skill_set img").css({opacity: 1.0});
	$("#skill_set a").css({color: 'black', backgroundColor: '#E9E3E3'});
	$("#collaboration_content").css({marginLeft: $("#tabs_collaboration").outerWidth(true), padding: '10px'});
	
	$("#btn_add_skill").bind("click", addSkill);
	
	$('.old_skill').each(function()
	{
		$(this).bind("mouseover", function(event) 
		{ 
			this.setAttribute('src', OC.imagePath('core', 'actions/delete-hover.png')); 
		});
	
		$(this).bind("mouseout", function(event) 
		{ 
			this.setAttribute('src', OC.imagePath('core', 'actions/delete.png')); 
		});
		
		$(this).bind("click", removeOldSkill);
	});
});

function addSkill(event)
{
	if(skill_count != 0)
	{
		document.getElementById('add_skill' + (no_created - 1)).removeChild(document.getElementById('btn_add_skill'));
	}
	else
	{
		document.getElementById('add_skill0').removeChild(document.getElementById('btn_add_skill'));
	}
	
	var new_skill = 
			"<td>\
				<input class=\"skill_name\" type=\"text\" id=\"skill_name" + no_created + "\" name=\"skill_name" + no_created + "\" placeholder=\"" + t('collaboration', 'Skill') + "\" maxlength=\"30\" required />\
			</td>\
			<td>\
				<select class=\"expertise\" id=\"expertise" + no_created + "\" name=\"expertise" + no_created + "\" required />\
					<option value=\"1\" >" + t('collaboration', 'Beginner') + "</option>\
					<option value=\"2\" >" + t('collaboration', 'Intermediate') + "</option>\
					<option value=\"3\" >" + t('collaboration', 'Expert') + "</option>\
				</select>\
			</td>\
			<td>\
				<input class=\"skill_exp\" type=\"number\" min=\"0\" id=\"skill_exp" + no_created + "\" name=\"skill_exp" + no_created + "\" required />\
			</td>\
			<td>\
				<img title=\"Delete\" src=\"" + OC.imagePath('core', 'actions/delete.png') + "\" id=\"del_skill" + no_created + "\" width=\"15px\" height=\"15px\" />\
			</td>\
			<td id=\"add_skill" + no_created + "\" >\
				<input type=\"button\" value=\"" + t('collaboration', 'Add Skill') + "\" id=\"btn_add_skill\" >\
			</td>";

	var tr = document.createElement("tr");
	tr.setAttribute("id", "skill" + no_created);
	tr.innerHTML = new_skill;

	var insert_into = document.getElementById("skills");
	insert_into.appendChild(tr);

	var btn_rem = $("#del_skill" + no_created);
	btn_rem.bind("click", function(event) { removeSkill(this.id.substring(9)); });
	btn_rem.bind("mouseover", function(event) { this.setAttribute('src', OC.imagePath('core', 'actions/delete-hover.png')); });
	btn_rem.bind("mouseout", function(event) { this.setAttribute('src', OC.imagePath('core', 'actions/delete.png')); });

	$("#btn_add_skill").bind("click", addSkill);
	
	$("#skill_name" + no_created).focus();

	no_created++;
	skill_count++;
}

function removeSkill(sklid)
{	
	id = parseInt(sklid);
	if(id + 1 == no_created && skill_count != 1)
	{
		do
		{
			id--;
		}while(document.getElementById("skill"+id) == null && id != 1);
		
		document.getElementsByName("skill_name" + sklid)[0].value = document.getElementsByName("skill_name" + id)[0].value;
		document.getElementsByName("expertise" + sklid)[0].value = document.getElementsByName("expertise" + id)[0].value;
		document.getElementsByName("skill_exp" + sklid)[0].value = document.getElementsByName("skill_exp" + id)[0].value;
		
		document.getElementById("del_skill" + sklid).setAttribute('src', OC.imagePath('core', 'actions/delete.png'));
	}

	document.getElementById("skills").removeChild(document.getElementById("skill"+id));
	skill_count--;

	if(skill_count == 0)
	{
		document.getElementById("add_skill0").innerHTML = "<input type=\"button\" value=\"" + t('collaboration', 'Add Skill') + "\" id=\"btn_add_skill\" >";
		$("#btn_add_skill").bind("click", addSkill);
	}
}

function removeOldSkill(event)
{
	var td = $(this).parent();
	var tr = $(td).parent();
	var skl = $(tr).children(':first-child').text();

	if(confirm('You are about to delete skill \'' + skl + '\'. It will be immediately deleted and cannot be undone.'))
	{
		$(this).attr('src', OC.imagePath('core', 'loading.gif'));
		
		$.ajax(
		{
			type: 'POST',
			url: OC.filePath('collaboration', 'ajax', 'delete_skill.php'),
			data:
			{
				skill: skl
			},
			success:
			function(data)
			{
				$(this).attr('src', OC.imagePath('core', 'actions/delete.png'));
				
				if(data.status == 'success' && data.delete_succeeded)
				{
					$(tr).remove();
				}
				else
				{
					alert('Unable to remove skill. Please try again later.');
				}
			}
		});
	}
}
