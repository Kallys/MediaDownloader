<?php

class Downloader
{
	private $urls = [];
	private $config = [];
	private $audio_only = false;
	private $errors = [];

	public function __construct($post, $audio_only)
	{
		$this->config = require dirname(__DIR__).'/config/config.php';
		$this->audio_only = $audio_only;

		if($this->is_installed() != 0)
		{
			$errors[] = "Youtube-dl is not installed, see <a>https://rg3.github.io/youtube-dl/download.html</a> !";
		}

		if(!$this->outuput_folder_exists())
		{
			$errors[] = "Output folder doesn't exist !";
		}

		if($audio_only && $this->is_extracter_installed())
		{
			$errors[] = "Install an audio extracter (ex: avconv) !";
		}

		if(isset($errors) && count($errors) > 0)
		{
			$_SESSION['errors'] = $errors;
			return;
		}

		$this->urls = explode(",", $post);

		if($this->config["max_dl"] == 0)
		{
			$this->do_download();
		}
		elseif($this->config["max_dl"] > 0)
		{
			if($this->background_jobs() >= 0 && $this->background_jobs() < $this->config["max_dl"])
			{
				$this->do_download();
			}
			else
			{
				$errors[] = "Simultaneous downloads limit reached !"; 
			}
		}

		if(isset($errors) && count($errors) > 0)
		{
			$_SESSION['errors'] = $errors;
			return;
		}
	}

	public static function background_jobs()
	{
		return shell_exec("ps aux | grep -v grep | grep youtube-dl | wc -l");
	}

	public static function max_jobs()
	{
		$config = require dirname(__DIR__).'/config/config.php';
		return $config["max_dl"];
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
		$cmd .= " -o ".$this->config["outputFolder"]."/";
		$cmd .= escapeshellarg("%(title)s-%(uploader)s.%(ext)s");

		if($this->audio_only)
		{
			$cmd .= " -x ";
		}

		foreach($this->urls as $url)
		{
			$cmd .= " ".$url;
		}

		$cmd .= " --restrict-filenames"; // --restrict-filenames is for specials chars
		$cmd .= " > /dev/null & echo $!";

		shell_exec($cmd);
	}
}

?>