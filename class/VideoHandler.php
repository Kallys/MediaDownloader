<?php

class VideoHandler
{
	private $config = [];

	public function __construct()
	{
		$this->config = require dirname(__DIR__).'/config/config.php';
	}

	public function listVideos()
	{
		$videos = [];

		if(!$this->outuput_folder_exists())
			return;

		$folder = dirname(__DIR__).'/'.$this->config["outputFolder"].'/';

		foreach(glob($folder.'*') as $file)
		{
			$video = [];
			$video["name"] = str_replace($folder, "", $file);
			$video["size"] = $this->to_human_filesize(filesize($file));
			
			$videos[] = $video;
		}

		return $videos;
	}

	public function delete($id)
	{
		$folder = dirname(__DIR__).'/'.$this->config["outputFolder"].'/';
		$i = 0;

		foreach(glob($folder.'*') as $file)
		{
			if($i == $id)
			{
				unlink($file);
			}
			$i++;
		}
	}

	private function outuput_folder_exists()
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

	public function to_human_filesize($bytes, $decimals = 0)
	{
		$sz = 'BKMGTP';
		$factor = floor((strlen($bytes) - 1) / 3);
		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
	}

	public function free_space()
	{
		return $this->to_human_filesize(disk_free_space($this->config["outputFolder"]));
	}

	public function get_video_folder()
	{
		return $this->config["outputFolder"];
	}
}

?>