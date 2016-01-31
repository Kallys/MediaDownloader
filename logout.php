<?php
	namespace MediaDownloader;
	require_once 'init.php';
	Utils\Session::getInstance()->logout();
	header("Location: login.php");
?>