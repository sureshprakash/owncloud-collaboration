<?php

$this->create('collaboration_route', '/{rel_path}')->action(
	function($params)
	{
		$file = ($params['rel_path'] == '')?
				'dashboard':
				$params['rel_path'];
				
		$url = explode('?', $file);
		$data = '';
		
		if(count($url) == 2)
		{
			$file = $url[0];
			$data = $url[1];
		}
				
		if(!file_exists(__DIR__ . '/../' . $file . '.php'))
		{
			header('Location: ' . \OCP\Util::linkToRoute('collaboration_route', 'dashboard'));
			exit();
		}
			
	    require_once __DIR__ . '/../' . $file . '.php' . (($data == '')? '': ('?' . $data));
	}
);

?>

