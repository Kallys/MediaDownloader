<?php
	require 'class/Session.php';
	Session::getInstance()->logout();
	header("Location: index.php");
?>