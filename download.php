<?php

namespace MediaDownloader;
require_once 'init.php';

use \MediaDownloader\Utils\Error;

if(isset($_POST['formats']) && !empty($_POST['formats']) && $session->load('downloader', $downloader))
{
	try {
		$downloader->downloadFormats(isset($_POST['formats']) ? $_POST['formats'] : null);
		$session->un_set('downloader');
		header("Location: index.php");
	}
	catch(\Exception $e)
	{
		Error::getInstance()->Error($e->getMessage());
	}
}
else if(isset($_POST['urls']) && !empty($_POST['urls']))
{
	$stream = isset($_POST['stream']) ? $_POST['stream'] : null;
	$quality = isset($_POST['quality']) ? $_POST['quality'] : null;
	$download = isset($_POST['download']) ? $_POST['download'] : null;

	try {
		$downloader = new Downloader($_POST['urls'], $stream, $quality, $download);
		
		if(!$downloader->NeedSelectionPage())
		{
			$downloader->download();
			header("Location: index.php");
		}
	}
	catch(\Exception $e)
	{
		Error::getInstance()->Error($e->getMessage());
	}
}
else
{
	header("Location: index.php");
}

Utils\Document::getInstance()->src_js[] = 'js/download.js';
Utils\Document::getInstance()->need_jquery = true;

Views\Header::PrintView();

echo '
		<div class="container">
			<h1>Download</h1>';

Error::getInstance()->PrintErrors();
Error::getInstance()->PrintWarnings();

if(isset($downloader) && $downloader->NeedSelectionPage() && !Error::getInstance()->HasError())
{
	if(!empty($downloader->medias))
	{
		echo '
			<form id="download-form" class="form-horizontal" action="download.php" method="post">
				<div class="form-group">';
	
		foreach($downloader->medias as $media)
		{
			Views\FormatSelection::PrintMediaFormatSelection($media, $downloader->IsManualFormatSelection(), $downloader->IsDirectDownloadMode());
		}
	
		echo '
				</div>
				'.($downloader->IsDirectDownloadMode() ? '': '<button type="submit" class="btn btn-primary btn-block" style="margin:0 auto;">Download</button>').'
			</form>';
	}
}

echo PHP_EOL.'		</div>';

Views\Footer::PrintView();

?>
