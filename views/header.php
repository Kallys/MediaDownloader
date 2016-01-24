<!DOCTYPE html>
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
				<a class="navbar-brand" href="">Media Downloader</a>
			</div>
			<div class="navbar-collapse collapse navbar-responsive-collapse">
				<ul class="nav navbar-nav">
					<li><a href="./">Download</a></li>
					<li><a href="./list.php">Files</a></li>
					<?php
						if($session->is_logged_in())
						{
					?>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
							<?php if(Downloader::background_jobs() > 0) echo "<b>"; ?>Background downloads : <?php echo Downloader::background_jobs()." / ".Downloader::max_background_jobs(); if(Downloader::background_jobs() > 0) echo "</b>"; ?> <span class="caret"></span></a>
						<ul class="dropdown-menu" role="menu">
							<?php
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

							?>
						</ul>
					</li>
					<?php
						}
					?>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<?php
						if($session->is_logged_in())
						{
							echo "<li><a href=\"./logout.php\">Logout</a></li>";
						}
					?>
				</ul>
			</div>
		</div>
