<?php
	require 'class/Session.php';
	require 'class/VideoHandler.php';
	
	$session = Session::getInstance();
	$video = new VideoHandler;

	if(!$session->is_logged_in())
	{
		header("Location: login.php");
	}

	if($session->is_logged_in() && isset($_GET["delete"]))
	{
		$video->delete($_GET["delete"]);
		header("Location: list.php");
	}

	require 'views/header.php';
?>
		<div class="container">
		<h2>List of available videos :</h2>
		<?php
			$videos = $video->listVideos();
			
			if(!empty($videos))
			{
		?>
			<table class="table table-striped table-hover ">
				<thead>
					<tr>
						<th style="min-width:800px; height:35px">Title</th>
						<th style="min-width:80px">Size</th>
						<th style="min-width:110px">Remove link</th>
					</tr>
				</thead>
				<tbody>
			<?php
				$i = 0;
				$totalSize = 0;

				foreach($videos as $v)
				{
					echo "<tr>";
					echo "<td><a href=\"".$video->get_video_folder().'/'.$v["name"]."\" download>".$v["name"]."</a></td>";
					echo "<td>".$v["size"]."</td>";
					echo "<td><a href=\"./list.php?delete=$i\" class=\"btn btn-danger btn-sm\">Delete</a></td>";
					echo "</tr>";
					$i++;
				}
			?>
				</tbody>
			</table>
		<?php
			}
			else
			{
				echo "<br><div class=\"alert alert-warning\" role=\"alert\">No videos !</div>";
			}
		?>
			<br/>
		</div><!-- End container -->
<?php
	require 'views/footer.php';
?>