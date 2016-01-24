<?php

require_once 'HumanReadable.php';

class FileHandler
{
	private $config = [];

	public function __construct()
	{
		$this->config = require dirname(__DIR__).'/config/config.php';
	}

	public function listFiles()
	{
		$files = array();

		if(!$this->output_folder_exists())
			return;

		$folder = dirname(__DIR__).'/'.$this->config["outputFolder"].'/';

		foreach(glob($folder.'*.*', GLOB_BRACE) as $filename)
		{
			$files[] = array(
				"name" => str_replace($folder, "", $filename),
				"size" => HumanReadable::GetFileSize(filesize($filename))
			);
		}
		
		return $files;
	}

	public function delete($ids)
	{
		$folder = dirname(__DIR__).'/'.$this->config["outputFolder"].'/';
		$i = 0;
		$files_to_remove = array();

		foreach(glob($folder.'*.*', GLOB_BRACE) as $file)
		{
			if(in_array($i, $ids))
			{
				$files_to_remove[] = $file;
			}
			$i++;
		}
		
		foreach($files_to_remove as $file)
		{
			unlink($file);
		}
	}

	private function output_folder_exists()
	{
		if(!is_dir($this->config['outputFolder']))
		{
			//Folder doesn't exist
			if(!mkdir('./'.$this->config['outputFolder'], 0777))
			{
				return false; //No folder and creation failed
			}
		}
		
		return true;
	}

	public function free_space()
	{
		return HumanReadable::GetFileSize(disk_free_space($this->config["outputFolder"]));
	}

	public function get_downloads_folder()
	{
		return $this->config["outputFolder"];
	}
}

?>