<?php
	require_once 'class/Session.php';
	require_once 'class/Downloader.php';

	$session = Session::getInstance();
	$loginError = "";

	if(isset($_POST["password"]))
	{
		if($session->login($_POST["password"]))
		{
			header("Location: index.php");
		}
		else
		{
			$loginError = "Wrong password !";
		}
	}
?>

<?php require 'views/header.php'; ?>
<div class="container">
	<?php
		if($loginError !== "")
		{
	?>
	<div class="alert alert-danger" role="alert"><?php echo $loginError; ?></div>
	<?php
		}
	?>
	<div class="row">
		<div class="col-md-4"></div>
		<div class="col-md-4">
			<h2>Login :</h2>
		</div>
		<div class="col-md-4"></div>
	</div>
	<form class="form-horizontal" action="login.php" method="POST">
		<div class="form-group">
			<div class="col-lg-4"></div>
				<div class="col-lg-4">
					<input class="form-control" id="password" name="password" placeholder="Password" type="password">
				</div>
			<div class="col-lg-4"></div>
		</div>
	</form>
</div><!-- End container -->
<?php require 'views/footer.php'; ?>