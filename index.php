<?php
	require_once 'class/Session.php';
	require_once 'class/Downloader.php';
	require_once 'class/FileHandler.php';
	require_once 'class/Format.php';
	require_once 'class/HumanReadable.php';
	require_once 'class/Media.php';
	require_once 'views/MediaFormatSelection.php';

	$session = Session::getInstance();
	$file = new FileHandler;

	require 'views/header.php';
	error_reporting(E_ALL);
	ini_set("display_errors", 1);
	if(!$session->is_logged_in())
	{
		header("Location: login.php");
	}
	else
	{
		if(isset($_GET['kill']) && !empty($_GET['kill']) && $_GET['kill'] === "all")
		{
			Downloader::kill_them_all();
		}
		
		if(isset($_POST['formats']) && !empty($_POST['formats']) && $session->load('downloader', $downloader))
		{
			try {
				$downloader->downloadFormats(isset($_POST['formats']) ? $_POST['formats'] : null);
				$session->un_set('downloader');
				header("Location: index.php");
			}
			catch(Exception $e)
			{
				$_SESSION['errors'][] = $e->getMessage();
			}
		}
		else if(isset($_POST['urls']) && !empty($_POST['urls']))
		{
			$stream = isset($_POST['stream']) ? $_POST['stream'] : null;
			$quality = isset($_POST['quality']) ? $_POST['quality'] : null;

			try {
				$downloader = new Downloader($_POST['urls'], $stream, $quality);
				
				if(!$downloader->IsManualFormatSelection())
				{
					$downloader->download();
					header("Location: index.php");
				}
			}
			catch(Exception $e)
			{
				$_SESSION['errors'][] = $e->getMessage();
			}
		}
	}
?>
		<div class="container">
			<h1>Download</h1>
			<?php

				if(!empty($_SESSION['errors']))
				{
					foreach ($_SESSION['errors'] as $e)
					{
						echo "<div class=\"alert alert-danger\" role=\"alert\">$e</div>";
					}
				}
				
				if(!empty($_SESSION['warnings']))
				{
					foreach($_SESSION['warnings'] as $w)
					{
						echo "<div class=\"alert alert-warning\" role=\"alert\">$w</div>";
					}
				}

			?>
			
<?php 
if(isset($downloader) && $downloader->IsManualFormatSelection() && empty($_SESSION['errors']) && !empty($downloader->medias))
{
	echo '
			<form id="download-form" class="form-horizontal" action="index.php" method="post">
				<div class="form-group">';
	
	foreach($downloader->medias as $media)
	{
		MediaFormatSelection::PrintMediaFormatSelection($media);
	}
	
	echo '
				</div>
				<button type="submit" class="btn btn-primary btn-block" style="margin:0 auto;">Download</button>
			</form>
';
}
else
{
?>
			<form id="download-form" class="form-horizontal" action="index.php" method="post">					
				<div class="form-group">
					<div class="col-md-10">
						<textarea class="form-control" id="url" name="urls" placeholder="One URL per line" rows="6"><?php echo empty($_POST['urls']) ? '' : $_POST['urls']; ?></textarea>
					</div>
					<div class="col-md-2">
						<div class="list-group-item">
							<label for="stream" class="h4 list-group-item-heading">Stream:</label>
							<select name="stream" class="list-group-item-text">
								<option value="<?php echo StreamEnum::Both; ?>" selected>Audio and Video</option>
								<option value="<?php echo StreamEnum::Audio_only; ?>">Audio only</option>
								<option value="<?php echo StreamEnum::Video_only; ?>">Video only</option>
							</select>
						</div>
						<div class="list-group-item">
							<label for="quality" class="h4 list-group-item-heading">Quality:</label>
							<select name="quality" class="list-group-item-text">
							<option value="<?php echo QualityEnum::Best; ?>" selected>Best</option>
							<option value="<?php echo QualityEnum::Worst; ?>">Worst</option>
							<option value="<?php echo QualityEnum::Best_ever; ?>">Best ever</option>
							<option value="<?php echo QualityEnum::Manual; ?>">Let me choose</option>
							</select>
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
							<p>Free space : <b><?php echo $file->free_space(); ?></b></p>
							<p>Download folder : <?php echo $file->get_downloads_folder(); ?></p>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="panel panel-info">
						<div class="panel-heading"><h3 class="panel-title">Help</h3></div>
						<div class="panel-body">
							<p><b>How does it work ?</b></p>
							<p>Simply paste your video link(s) in the field and click "Download"</p>
							<p><b>With which sites does it work?</b></p>
							<p><a href="http://rg3.github.io/youtube-dl/supportedsites.html">Here's</a> a list of the supported sites</p>
							<p><b>How can I download the video on my computer?</b></p>
							<p>Go to <a href="./list.php">Files</a> -> choose one -> right click on the link -> "Save target as ..." </p>
						</div>
					</div>
				</div>
			</div>
<?php
}
?>
		</div>
<?php
	unset($_SESSION['errors']);
	unset($_SESSION['warnings']);
	require 'views/footer.php';
?>
