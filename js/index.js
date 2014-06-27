/**
* This file is licensed under the Affero General Public License version 3 or
* later.
* See the COPYING-README file.
*/

$(document).ready(function()
{
	var params = {'rel_path': 'dashboard'};
	window.location.href = OC.Router.generate('collaboration_route', params);
});
