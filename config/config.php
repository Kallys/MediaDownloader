<?php

$CONFIG = array (
	// Enable password restriction (boolean)
	'security' => true,
		
	// MD5 hash of choosen password. Default is 'root'. (string)
	'password' => '63a9f0ea7bb98050796b649e85481845',
	
	// Relative folder (from MediaDownloader folder) where to store downloaded files. No need for trailing slash. (string)
	'output_folder' => 'downloads',
		
	// Prefered post processor between avconv or ffmpeg. (string)
	'post_processor' => 'avconv',
		
	// Maximum simultaneous server downloads limit. Set to 0 to disable limit. (integer)
	'max_dl' => 3
);

?>
