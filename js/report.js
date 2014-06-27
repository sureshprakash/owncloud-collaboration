/**
* This file is licensed under the Affero General Public License version 3 or
* later.
* See the COPYING-README file.
*/

$(document).ready(function()
{
	$("#report img").css({opacity: 1.0});
	$("#report a").css({color: 'black', backgroundColor: '#E9E3E3'});
	$("#collaboration_content").css({marginLeft: $("#tabs_collaboration").outerWidth(true), padding: '10px'});
	
	$("#projects_list").chosen(
	{
		no_results_text: t('collaboration', 'No matches found!'),
		disable_search_threshold: 5
	});
	
	$("#report_type").chosen(
	{
		disable_search_threshold: 4
	});
	
	$("#projects_list").bind('change', generateReport);
	$("#report_type").bind('change', generateReport);
});

function generateReport(event)
{
	var proj = $("#projects_list").val();
	var rep_type = $("#report_type").val();
	
	clearCanvas();
	$("#legend").empty();
	$("#bargraph").empty();
		
	if(proj != '' && rep_type != '')
	{
		$("#message").html('<img src="' + OC.imagePath('core', 'loading.gif') + '" style="width: 15px; height: 15px" />');
		
		switch(rep_type)
		{
			case 'contribution':
				$.ajax(
				{
					type: 'POST',
					url: OC.filePath('collaboration', 'ajax', 'contribution_report.php'),
					data:
					{
						project: proj
					},
					success:
					function(data)
					{
						$("#message").empty();
						
						if(data.status == 'success')
						{
							drawBarChart(data.member_count);
						}
						else
						{
							alert('Unable to generate report now. Please try again later');
						}
					}
				});
				
				break;
				
			case 'task_status':
				$.ajax(
				{
					type: 'POST',
					url: OC.filePath('collaboration', 'ajax', 'task_status_report.php'),
					data:
					{
						project: proj
					},
					success:
					function(data)
					{
						$("#message").empty();
						
						if(data.status == 'success')
						{
							// List of statuses in alphabetical order
							var status = ['Cancelled', 'Completed', 'Held', 'In Progress', 'Unassigned', 'Verified'];
							var status_count = {};
							
							for(var i in status)
							{
								if(data.task_status[status[i]] == undefined)
								{
									status_count[status[i]] = 0;
								}
								else
								{
									status_count[status[i]] = parseInt(data.task_status[status[i]]);
								}
							}
							
							drawPieChart(status_count);
						}
						else
						{
							alert('Unable to generate report now. Please try again later');
						}
					}
				});
				
				break;
				
			case 'project_status':
				$.ajax(
				{
					type: 'POST',
					url: OC.filePath('collaboration', 'ajax', 'project_timeline_report.php'),
					data:
					{
						project: proj
					},
					success:
					function(data)
					{
						$("#message").empty();

						if(data.status == 'success')
						{
							drawTimeline(data.project_status);
						}
						else
						{
							alert('Unable to generate report now. Please try again later');
						}
					}
				});
					
		}
	}
	else
	{
		$("#message").html(t('collaboration', 'Kindly select project and report type to generate the report'));
/*		clearCanvas();
		$("#legend").empty();
		$("#bargraph").empty();*/
	}
}

function drawPieChart(status_count)
{
	var canvas = document.getElementById('canvas');
	canvas.width = 350;
	canvas.height = 350;
	
	var context = canvas.getContext('2d');
	
	var sum = 0;
	for(var status in status_count)
	{
		sum += status_count[status];
	}
	
	context.clearRect(0, 0, canvas.width, canvas.height);
	$("#legend").empty();
	
	if(sum == 0)
	{
		$("#message").html(t('collaboration', 'No tasks available in this project'));
		return;
	}
	
	var pie_angle = 0;
	var circle = 
	{
		center: 
		{
			x: canvas.width / 2,
			y: canvas.height / 2
		},
		radius: Math.min(canvas.width, canvas.height) / 2
	}
	var colors = ['red', 'blue', 'black', 'brown', 'orange', 'grey', 'green'];
	
	var i = 0;
	var legend = '<table id="legend_table" >';
	
	for(var status in status_count)
	{
		var count = status_count[status];
		var percent = count / sum * 100;
		var part = percent / 100 * 360;
		
		end_angle = pie_angle + convertDegreeToRadian(part);
		
		context.beginPath();
		context.moveTo(circle.center.x, circle.center.y);
		context.arc(circle.center.x, circle.center.y, circle.radius, pie_angle, end_angle);
		context.closePath();

		context.fillStyle=colors[i];
		context.fill();
		
		pie_angle = end_angle;
		
		legend += '<tr><td><div style="width: 20px; height: 20px; background-color: ' + colors[i] + ';" ></div></td><td>' + getLocalTaskStatus(status) + '</td><td>' + count + ' ' + t('collaboration', 'task(s)') + '</td></tr>';
		i++;
	}
	
	legend += '</table>' + t('collaboration', 'Total number of tasks: ') + sum;
	
	$("#legend").append(legend).css({marginTop: canvas.height / 8 + 'px'});
}

function convertDegreeToRadian(val)
{
	return (val * Math.PI) / 180;
}

function getLocalTaskStatus(status)
{
	switch(status)
	{
		case 'Cancelled':
			return t('collaboration', 'Cancelled');
			
		case 'Completed':
			return t('collaboration', 'Completed');

		case 'Held':
			return t('collaboration', 'Held');

		case 'In Progress':
			return t('collaboration', 'In Progress');

		case 'Unassigned':
			return t('collaboration', 'Unassigned');

		case 'Verified':
			return t('collaboration', 'Verified');
	}
}

function drawTimeline(project_details)
{
	var canvas = document.getElementById('canvas');
	canvas.width = 900;
	canvas.height = 350;
	
	var context = canvas.getContext('2d');
	var progress;
	var deadline, start_date, now, updated_date;
	
	var bar_height = 20;

	start_date = new Date(project_details.start_date);
	deadline = new Date(project_details.deadline);
	now = new Date(project_details.now);
	updated_date = new Date(project_details.updated_date);
	
	context.clearRect(0, 0, canvas.width, canvas.height);
	$("#legend").empty();
	
	var date = [];
	
	date[0] = 'Started: ' + project_details.start_date.split(' ')[0];
	
	if(project_details.completed == 1)
	{
		progress = 100;
		
		context.beginPath();
		context.rect(0, 0, canvas.width, bar_height);
		context.closePath();
		context.fillStyle='green';
		context.fill();
		
		date[1] = 'Completed: ' + project_details.updated_date.split(' ')[0];
		date[2] = '';
	}
	else
	{
		progress = project_details.progress * canvas.width / 100;
		
		var second_color;
		
		if(now > deadline)
		{
			second_color = 'red';
			date[1] = 'Deadline: ' + project_details.deadline.split(' ')[0];
			date[2] = 'Today: ' + project_details.now.split(' ')[0];
		}
		else
		{
			second_color = 'green';
			date[1] = 'Today: ' + project_details.now.split(' ')[0];
			date[2] = 'Deadline: ' + project_details.deadline.split(' ')[0];
		}
		
		context.beginPath();
		context.rect(0, 0, progress, bar_height);
		context.closePath();
		context.fillStyle='blue';
		context.fill();
	
		context.beginPath();
		context.rect(progress, 0, canvas.width, bar_height);
		context.closePath();
		context.fillStyle=second_color;
		context.fill();
	}
	
	context.beginPath();
	context.moveTo(1, 0);
	context.lineTo(1, bar_height + 20);
	context.closePath();
	context.stroke();
	
	context.beginPath();
	context.moveTo(progress, 0);
	context.lineTo(progress, bar_height + 60);
	context.closePath();
	context.stroke();
	
	context.beginPath();
	context.moveTo(canvas.width - 1, 0);
	context.lineTo(canvas.width - 1, bar_height + 100);
	context.closePath();
	context.stroke();
	
	var next_pos = 2;
	var prev_pos = 105;
	
	context.fillStyle='black';
	
	if(project_details.progress < 50)
	{
		context.fillText(date[1], progress + next_pos, bar_height + 58);
		context.fill();
	}
	else
	{
		context.fillText(date[1], progress - prev_pos, bar_height + 58);
		context.fill();
	}
	
	// To avoid overlapping of line with date
	context.clearRect(3, bar_height + 9, prev_pos, 12);

	context.fillText(date[0], 3, bar_height + 18);
	context.fill();
	
	context.fillText(date[2], canvas.width - prev_pos, bar_height + 98);
	context.fill();

	$("#message").html(t('collaboration', 'Number of verified tasks: ') + project_details.num_completed_tasks + '<br />' + t('collaboration', 'Number of pending tasks: ') + project_details.num_pending_tasks);
	
	var colors = ['blue', 'green', 'red'];
	
	var i = 0;
	var legend = '<table id="legend_table" ><tr>';
	var type = [t('collaboration', 'Progressed'), t('collaboration', 'To progress'), t('collaboration', 'Crossed deadline')];
	
	for(var i = 0; i < type.length; i++)
	{
		legend += '<td><div style="width: 20px; height: 20px; background-color: ' + colors[i] + ';" ></div></td><td>' + type[i] + '</td>';
	}
	
	legend += '</tr></table>';
	
	$("#legend").append(legend).css({marginTop: canvas.height / 8 + 'px'});

/*	
	context.rect(3, bar_height+9, prev_pos, 12);
	context.strokeStyle='red';
	context.stroke();
*/
}

function clearCanvas()
{
	var canvas = document.getElementById('canvas');
	var context = canvas.getContext('2d');
	context.clearRect(0, 0, canvas.width, canvas.height);
}

/*
function drawBarChart(member_count)
{
	var text_space = 150;
	
	var canvas = document.getElementById('canvas');
	canvas.width = 900;
	canvas.height = 350 + text_space;
	
	var context = canvas.getContext('2d');
	
	context.clearRect(0, 0, canvas.width, canvas.height);
	
//	var bar_width = canvas.width / (Object.keys(member_count).length * 1.5);
	var bar_width = 80;
	var bar_height_divisions = getMaxCount(member_count) / (canvas.height - text_space) * 100;
	
	var pos = 0;
	
	for(var member in member_count)
	{
		context.beginPath();
		
		if(bar_height_divisions != 0)
		{
			context.rect(pos, 0, bar_width, (member_count[member] / bar_height_divisions * 100) + 2);
		}
		else
		{	
			context.rect(pos, 0, bar_width, 2);
		}
		
		context.fillStyle='green';
		context.fill();
		context.closePath();
		
		pos = pos + bar_width + bar_width / 2;
	}
}*/

function drawBarChart(member_count)
{
	var bar_width = 100;
	var bar_height_divisions = getMaxCount(member_count) / 350 * 100;
	
	var pos = 0;
	
	var graph = '';
	
	$("#message").html(t('collaboration', 'Number of tasks carried out by each member (working on this project) is presented below'));
	
	for(var member in member_count)
	{
		if(bar_height_divisions != 0)
		{
			graph += '<div style="float: left; margin-left: 50px; width: ' + bar_width + 'px;"><div style="width: inherit; height: 350px; background-color: green;" ><div style="width: inherit; height: ' + (350 - ((member_count[member] / bar_height_divisions * 100) + 2)) + 'px; background-color: white; vertical-align: bottom; text-align: center; display: table-cell;" >' + member_count[member] + '</div></div><div class="member" style="text-align: center; width: inherit;" >' + member.split(' ').join('<br />') + '</div></div>';
		}
		else
		{		
			graph += '<div style="float: left; margin-left: 50px; width: ' + bar_width + 'px;"><div style="width: inherit; height: 350px; background-color: green;" ><div style="width: inherit; height: 348px; background-color: white; vertical-align: bottom; text-align: center; display: table-cell;" >' + member_count[member] + '</div></div><div class="member" style="text-align: center; width: inherit;" >' + member.split(' ').join('<br />') + '</div></div>';
		}
		
		pos = pos + bar_width + bar_width / 2;
		
		$("#bargraph").css({width: (($("#bargraph").width() + bar_width) + 'px')});
	}
	
	$("#bargraph").html(graph);
}

function getMaxCount(member_count)
{
	var maximum = -1;
	
	for(var member in member_count)
	{
		if(member_count[member] > maximum)
		{
			maximum = member_count[member];
		}
	}
	
	return maximum;
}
