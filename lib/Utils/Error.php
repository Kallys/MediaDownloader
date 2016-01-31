<?php

namespace MediaDownloader\Utils;

class Error
{
	private static $_instance = null;
	private $errors = [];
	private $warnings = [];

	private function __construct()
	{
	}

	public static function getInstance()
	{
		if(is_null(self::$_instance))
		{
			self::$_instance = new Error();
		}

		return self::$_instance;
	}
	
	public function PrintErrors()
	{
		if($this->HasError())
		{
			foreach($_SESSION['errors'] as $e)
			{
				echo "<div class=\"alert alert-danger\" role=\"alert\">$e</div>";
			}
			
			$this->errors = $_SESSION['errors'];
			unset($_SESSION['errors']);
		}
	}
	
	public function PrintWarnings()
	{
		if($this->HasWarning())
		{
			foreach($_SESSION['warnings'] as $w)
			{
				echo "<div class=\"alert alert-warning\" role=\"alert\">$w</div>";
			}
			
			$this->warnings = $_SESSION['warnings'];
			unset($_SESSION['warnings']);
		}
	}
	
	public function Error($message)
	{
		$_SESSION['errors'][] = $message;
	}
	
	public function Warning($message)
	{
		$_SESSION['warnings'][] = $message;
	}

	public function HasError()
	{
		return isset($_SESSION['errors']) ? !empty($_SESSION['errors']) : !empty($this->errors);
	}

	public function HasWarning()
	{
		return isset($_SESSION['warnings']) ? !empty($_SESSION['warnings']) : !empty($this->warnings);
	}
}

?>