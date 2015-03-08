<?php

class Session
{
	private $config = [];

	private static $_instance;

	public function __construct()
	{
		session_start();

		$this->config = require dirname(__DIR__).'/config/config.php';

		if($this->config["security"])
		{
			if(!isset($_SESSION["logged_in"]))
			{
				$_SESSION["logged_in"] = false;
			}
		}
		else
		{
			$_SESSION["logged_in"] = true;
		}
	}

	public static function getInstance()
	{
		if(is_null(self::$_instance))
		{
			self::$_instance = new Session();
		}

		return self::$_instance;
	}

	public function login($password)
	{
		if($this->config["password"] === md5($password))
		{
			$_SESSION["logged_in"] = true;
			return true;
		}
		else
		{
			$_SESSION["logged_in"] = false;
			return false;
		}
	}

	public function is_logged_in()
	{
		return $_SESSION["logged_in"];
	}

	public function logout()
	{
		$_SESSION = array();
		session_destroy();
	}
}

?>