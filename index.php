<?php
	namespace MediaDownloader;
	require_once 'init.php';
	
	if(isset($_GET['kill']) && !empty($_GET['kill']) && $_GET['kill'] === "all")
	{
		Downloader::kill_them_all();
	}

	Utils\Document::getInstance()->src_js[] = 'js/index.js';
	Utils\Document::getInstance()->need_jquery = true;
	
	Views\Header::PrintView();
?>
		<div class="container">
			<h1>Download</h1>
			
<?php
	Utils\Error::getInstance()->PrintErrors();
	Utils\Error::getInstance()->PrintWarnings();
?>
			<form id="download-form" class="form-horizontal" action="download.php" method="post">					
				<div class="form-group">
					<div class="col-md-10">
						<textarea class="form-control" id="url" name="urls" placeholder="One URL per line" rows="6"><?php echo empty($_POST['urls']) ? 'https://www.youtube.com/watch?v=i90pCWPzABg' : $_POST['urls']; ?></textarea>
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
				<button type="submit" class="btn btn-primary" name="download" value="indirect">Download</button>
				<button type="submit" class="btn btn-default" name="download" value="direct">Direct Download</button>
			</form>
			<br>
			<div class="row">
				<div class="col-lg-6">
					<div class="panel panel-info">
						<div class="panel-heading"><h3 class="panel-title">Info</h3></div>
						<div class="panel-body">
							<p>Free space : <b><?php echo FileHandler::free_space(); ?></b></p>
							<p>Download folder : <?php echo Utils\Config::Get('output_folder_url'); ?></p>
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
		</div>

<?php
	Views\Footer::PrintView();
?>
