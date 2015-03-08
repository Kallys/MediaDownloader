<?php

class Downloader
{
	private $urls = [];
	private $config = [];
	private $audio_only = false;

	public function __construct($post, $audio_only)
	{
		$this->config = require dirname(__DIR__).'/config/config.php';
		$this->audio_only = $audio_only;

		if($this->is_installed() != 0)
		{
			die("youtube-dl is not installed !");
		}

		if(!$this->outuput_folder_exists())
		{
			die("Output folder doesn't exist !");
		}

		if($this->is_extracter_installed())
		{
			die("Install and configure an extracter !");
		}

		$this->urls = explode(",", $post);

		$this->do_download();
	}

	private function is_installed()
	{
		exec("which youtube-dl", $out, $r);
		return $r;
	}

	private function is_extracter_installed()
	{
		exec("which ".$this->config["extracter"], $out, $r);
		return $r;
	}

	private function is_valid_url($url)
	{
		return filter_var($url, FILTER_VALIDATE_URL);
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

	private function do_download()
	{
		$cmd = "youtube-dl";
		$cmd .= ' -o '.$this->config["outputFolder"].'/';
		$cmd .= escapeshellarg('%(title)s-%(uploader)s.%(ext)s');

		if($this->audio_only)
		{
			$cmd .= ' -x ';
		}

		foreach($this->urls as $url)
		{
			$cmd .= ' '.$url;
		}

		$cmd .= ' --restrict-filenames'; // --restrict-filenames is for specials chars
		$cmd .= ' 2>&1';

		exec($cmd, $out, $ret);
	}
}

?>