<?php
	
	$force = false;
	if (isset($_GET['all']) && $_GET['all'] == 1) { $force = true; }
	
	$ds = DIRECTORY_SEPARATOR;
	$albums = dirname(dirname(dirname(__FILE__))) . $ds . 'albums';
	
	$files = glob($albums . $ds . '*' . $ds . 'cache' . $ds . '*');
	
	foreach ($files as $file) {
		if ((fileatime($file) < strtotime('-1 week')) || $force) {
			@unlink($file);
		}
	}

?>