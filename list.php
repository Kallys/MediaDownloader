<?php

require_once 'class/Session.php';
require_once 'class/Downloader.php';
require_once 'class/FileHandler.php';

$session = Session::getInstance();
if(!$session->is_logged_in())
{
	header("Location: login.php");
}

$file = new FileHandler;
$files = $file->listFiles();

if(isset($_POST["action"]) && !empty($_POST["action"]))
{
	if($_POST["action"] == "Delete")
	{
		$file->delete($_POST["cb_file"]);
	}
	header("Location: list.php");
}

require 'views/header.php';

?>
		<script type="text/javascript" src="js/list.js"> </script>

		<div class="container">
		<?php
			if(!empty($files))
			{
		?>
			<h2>List of downloaded files :</h2>
			<p>Action to apply on selected files :</p>
			<form action="list.php" method="post">
				<input type="submit" class="btn btn-danger btn-sm" name="action" value="Delete" />
				<table class="table table-striped table-hover ">
					<thead>
						<tr>
							<th style="width:30px"><input type="checkbox" onClick="toggle(this);" /></th>
							<th style="">Title</th>
							<th style="width:80px">Size</th>
						</tr>
					</thead>
					<tbody>
					
			<?php
				$i = 0;
				$totalSize = 0;

				foreach($files as $f)
				{
					echo "
						<tr>
							<td><input type=\"checkbox\" name=\"cb_file[]\" value=\"$i\" /></td>
							<td><a href=\"".$file->get_downloads_folder().'/'.$f["name"]."\" download>".$f["name"]."</a></td>
							<td>".$f["size"]."</td>
						</tr>";
					$i++;
				}
			?>
			
					</tbody>
				</table>
			</form>
			<br/>
			<br/>
		<?php
			}
			else
			{
				echo "<br><div class=\"alert alert-warning\" role=\"alert\">No files found!</div>";
			}
		?>
			<br/>
		</div><!-- End container -->

<?php
require 'views/footer.php';
?>