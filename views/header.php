<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Youtube-dl WebUI</title>
		<link rel="stylesheet" href="css/bootstrap.min.css" media="screen">
	</head>
	<body>
		<div class="navbar navbar-default navbar-fixed-top">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="">Youtube-dl WebUI</a>
			</div>
			<div class="navbar-collapse collapse navbar-responsive-collapse">
				<ul class="nav navbar-nav">
					<li><a href="./">Download</a></li>
					<li><a href="./list.php?type=v">List of videos</a></li>
					<li><a href="./list.php?type=m">List of musics</a></li>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<?php
						if($session->is_logged_in())
						{
					?>
						<li><a href="./logout.php">Logout</a></li>
					<?php
						}
					?>
				</ul>
			</div>
		</div>