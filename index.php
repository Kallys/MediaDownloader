<?php
	require 'class/Session.php';
	require 'class/FileHandler.php';
	require 'class/Downloader.php';

	$session = Session::getInstance();
	$file = new FileHandler;

	if(!$session->is_logged_in())
	{
		header("Location: login.php");
	}
	else
	{
		if(isset($_POST['urls']) && !empty($_POST['urls']))
		{
			$audio_only = false;

			if(isset($_POST['audio']) && !empty($_POST['audio']))
			{
				$audio_only = true;
			}

			$downloader = new Downloader($_POST['urls'], $audio_only);
			
			if(!isset($_SESSION['errors']))
			{
				if($audio_only)
				{
					header("Location: list.php?type=m");
				}
				else
				{
					header("Location: list.php?type=v");
				}
			}
		}
	}
	
	require 'views/header.php';
?>
		<div class="container">
			<br>
			<br>
			<h1>Download</h1>
			<?php

				if(isset($_SESSION['errors']) && $_SESSION['errors'] > 0)
				{
					foreach ($_SESSION['errors'] as $e)
					{
						echo "<div class=\"alert alert-warning\" role=\"alert\">$e</div>";
					}
				}

			?>
			<form id="download-form" class="form-horizontal" action="index.php" method="post">					
				<div class="form-group">
					<div class="col-md-10">
						<input class="form-control" id="url" name="urls" placeholder="Link(s) separate with comma" type="text">
					</div>
					<div class="col-md-2">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="audio"> Audio Only
							</label>
						</div>
					</div>
				</div>
				<button type="submit" class="btn btn-primary">Download</button>
			</form>
			<br>
			<div class="row">
				<div class="col-lg-6">
					<div class="panel panel-info">
						<div class="panel-heading"><h3 class="panel-title">Info</h3></div>
						<div class="panel-body">
							<p><b>Background downloads : <?php echo Downloader::background_jobs()." / ".Downloader::max_jobs() ?> </b></p>
							<p>Free space : <?php echo $file->free_space(); ?></b></p>
							<p>Download folder : <?php echo $file->get_downloads_folder(); ?></p>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="panel panel-info">
						<div class="panel-heading"><h3 class="panel-title">Help</h3></div>
						<div class="panel-body">
							<p><b>How does it work ?</b></p>
							<p>Simply paste your video link in the field and click "Download"</p>
							<p><b>With which sites does it works ?</b></p>
							<p><a href="http://rg3.github.io/youtube-dl/supportedsites.html">Here</a> is the list of the supported sites</p>
							<p><b>How can I download the video on my computer ?</b></p>
							<p>Go to <a href="./list.php">List of videos</a>, choose one, right click on the link and do "Save target as ..." </p>
						</div>
					</div>
				</div>
			</div>
		</div>
<?php
	unset($_SESSION['errors']);
	require 'views/footer.php';
?>