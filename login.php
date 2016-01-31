<?php

namespace MediaDownloader;
require_once 'init.php';

$loginFailed = false;

if(isset($_POST["password"]))
{
	if(Utils\Session::getInstance()->login($_POST["password"]))
	{
		header("Location: index.php");
		exit(0);
	}
	else
	{
		$loginFailed = true;
	}
}

Views\Header::PrintView();
?>

		<div class="container">
<?php
if($loginFailed)
{
	echo '			<div class="alert alert-danger" role="alert">Wrong password !</div>';
}
?>

			<div class="row">
				<div class="col-md-4"></div>
				<div class="col-md-4">
					<h2>Login :</h2>
					<form class="form-horizontal" action="login.php" method="POST">
						<input class="form-control" id="password" name="password" placeholder="Password" type="password">
					</form>
				</div>
				<div class="col-md-4"></div>
			</div>
		</div>
<?php
	Views\Footer::PrintView();
?>