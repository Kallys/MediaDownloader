<?php

namespace MediaDownloader\Utils;

class Config
{
	private static $_instance = null;
	private $config = null;
	
	private function __construct()
	{
		require_once DIR_CONF.'config.php';
		$this->config = $CONFIG;
		
		// Be sure path ends with a slash
		if(substr($this->config['output_folder'], -1) != DIRECTORY_SEPARATOR)
		{
			$this->config['output_folder'] .= DIRECTORY_SEPARATOR;
		}
		
		$this->config['output_folder_url'] = $this->config['output_folder'];
		
		// Make path absolute
		$this->config['output_folder'] = DIR_BASE.DIRECTORY_SEPARATOR.$this->config['output_folder'];

		
		$this->config['REQUEST_URI'] = explode('/', $_SERVER["REQUEST_URI"]);
	}
	
	public function GetRequestPage()
	{
		return end($this->config['REQUEST_URI']);
	}
	
	public function Check()
	{
		if(!$this->DoesOutputFolderExist() && !$this->MakeOutputFolder())
			throw new \Exception('Unable to create non-existent output folder "'.$this->config['output_folder'].'"');
		
		if(!$this->IsOutputFolderWritable())
			throw new \Exception('Output folder is not writable.');
	}
	
	public function DoesOutputFolderExist()
	{
		return is_dir($this->config['output_folder']);
	}
	
	private function MakeOutputFolder()
	{
		return @mkdir($this->config['output_folder'], 0775, true);
	}
	
	private function IsOutputFolderWritable()
	{
		return is_writable($this->config['output_folder']);
	}
	
	public static function GetInstance()
	{
		if(is_null(self::$_instance))
		{
			self::$_instance = new Config();
		}
	
		return self::$_instance;
	}
	
	public static function Get($name)
	{
		return self::GetInstance()->config[$name];
	}
}