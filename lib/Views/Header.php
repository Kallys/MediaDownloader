<?php

namespace MediaDownloader\Views;

use \MediaDownloader\Utils\Session;
use \MediaDownloader\Downloader;

abstract class Header
{
	public static function PrintView()
	{
		$logged_in = Session::getInstance()->is_logged_in();
		
		echo '<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Media Downloader</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.6/lumen/bootstrap.min.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="css/main.css">
	</head>
	<body>
		<div class="navbar navbar-default">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="./">Media Downloader</a>
			</div>';
		
		if($logged_in)
		{
			echo '
			<div class="navbar-collapse collapse navbar-responsive-collapse">
				<ul class="nav navbar-nav">
					<li><a href="./index.php">Download</a></li>
					<li><a href="./list.php">Files</a></li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
							'.(Downloader::background_jobs() > 0 ? '<b>' : '').'Background downloads : '.Downloader::background_jobs().' / '.Downloader::max_background_jobs().(Downloader::background_jobs() > 0 ? '</b>' : '').' <span class="caret"></span></a>
						<ul class="dropdown-menu" role="menu">';

			if(Downloader::get_current_background_jobs() != null)
			{
				foreach(Downloader::get_current_background_jobs() as $key)
				{
					if (strpos($key['cmd'], '-x') !== false) //Music
					{
						echo "<li><a href=\"#\"><i class=\"fa fa-music\"></i> Elapsed time : ".$key['time']."</a></li>";
					}
					else
					{
						echo "<li><a href=\"#\"><i class=\"fa fa-video-camera\"></i> Elapsed time : ".$key['time']."</a></li>";
					}
				}

				echo "<li class=\"divider\"></li>";
				echo "<li><a href=\"./index.php?kill=all\">Kill all downloads</a></li>";
			}
			else
			{
				echo "<li><a>No jobs !</a></li>";
			}

			echo '
						</ul>
					</li>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<li><a href="./logout.php">Logout</a></li>
				</ul>
			</div>';
		}
		
		echo '
		</div>';
	}
}

?>
