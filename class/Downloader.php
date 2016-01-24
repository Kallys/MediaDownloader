<?php

require_once 'class/Session.php';
require_once 'class/Format.php';
require_once 'class/Media.php';

class Downloader
{
	public $medias = array();
	public $format;// Output format
	
	private $urls = [];
	private $config = [];
	private $download_path = "";

	public function __construct($post, $stream, $quality)
	{
		$session = Session::getInstance();
		
		$this->config = require dirname(__DIR__).'/config/config.php';

		//this allows to use absolute paths
		if(strpos($this->config["outputFolder"], "/") === 0)
		{
			$this->download_path = $this->config["outputFolder"];
		}
		else
		{
			$this->download_path = dirname(__DIR__).'/'.$this->config["outputFolder"];
		}

		$this->format = new Format($stream, $quality);
		$this->check_requirements();
		
		$urls = array_unique(array_filter(array_map('trim', explode("\r\n", $post))));
		
		foreach($urls as $url)
		{			
			if(!$this->is_valid_url($url))
			{
				$_SESSION['errors'][] = "\"".$url."\" is not a valid url !";
			}
			else
			{
				$this->urls[] = $url;
			}
		}

		if($this->IsManualFormatSelection())
		{
			$this->load_infos();
			$session->set('downloader', $this);
		}
	}
	
	public function IsManualFormatSelection()
	{
		return $this->format->quality == QualityEnum::Manual;
	}

	public static function background_jobs()
	{
		return shell_exec("ps aux | grep youtube-dl | grep -v grep | grep -v \"youtube-dl -U\" | wc -l");
	}

	public static function max_background_jobs()
	{
		$config = require dirname(__DIR__).'/config/config.php';
		return $config["max_dl"];
	}

	public static function get_current_background_jobs()
	{
		exec("ps -A -o user,pid,etime,cmd | grep youtube-dl | grep -v grep | grep -v \"youtube-dl -U\"", $output);

		$bjs = [];

		if(count($output) > 0)
		{
			foreach($output as $line)
			{
				$line = explode(' ', preg_replace ("/ +/", " ", $line), 4);
				$bjs[] = array(
					'user' => $line[0],
					'pid' => $line[1],
					'time' => $line[2],
					'cmd' => $line[3]
					);
			}

			return $bjs;
		}
		else
		{
			return null;
		}
	}

	public static function kill_them_all()
	{
		exec("ps -A -o pid,cmd | grep -v grep | grep youtube-dl | awk '{print $1}'", $output);

		if(count($output) <= 0)
			return;

		foreach($output as $p)
		{
			shell_exec("kill ".$p);
		}

		$config = require dirname(__DIR__).'/config/config.php';
		$folder = dirname(__DIR__).'/'.$config["outputFolder"].'/';

		foreach(glob($folder.'*.part') as $file)
		{
			unlink($file);
		}
	}

	public function downloadFormats($choosen_formats)
	{
		if(is_null($choosen_formats))
			return;
		
		foreach(array_keys($choosen_formats) as $video_index)
		{
			if(!array_key_exists($video_index, $this->medias))
			{
				$_SESSION['errors'][] = "Internal error on video selection.";
				continue;
			}
			
			$this->do_download($this->medias[$video_index]->media_info->webpage_url, $choosen_formats[$video_index]);
		}
	}
	
	private function check_requirements()
	{
		if(!$this->is_youtubedl_installed())
			throw new Exception("Youtube-dl is not installed, see <a>https://rg3.github.io/youtube-dl/download.html</a> !");

		$this->check_outuput_folder();

		if($this->format->NeedPostProcess() && !$this->is_extracter_installed())
			throw new Exception("Install an audio extracter (ex: avconv) !");
	}

	private function is_youtubedl_installed()
	{
		exec("which youtube-dl", $out, $r);
		return $r == 0;
	}

	private function is_extracter_installed()
	{
		exec("which ".$this->config["extracter"], $out, $r);
		return $r == 0;
	}

	private function is_valid_url($url)
	{
		return filter_var($url, FILTER_VALIDATE_URL);
	}

	private function check_outuput_folder()
	{
		//Folder doesn't exist
		if(!is_dir($this->download_path))
		{
			if(!mkdir($this->download_path, 0775))
				throw new Exception("Output folder doesn't exist and creation failed !");
		}
		//Exists but can I write ?
		else if(!is_writable($this->download_path))
		{
			throw new Exception("Output folder isn't writable !");
		}
	}

	public function download()
	{
		foreach($this->urls as $url)
		{
			if($this->config["max_dl"] > 0 && $this->background_jobs() >= $this->config["max_dl"])
				throw new Exception("Simultaneous downloads limit reached !");

			$this->do_download($url);
		}
	}
	
	private function do_download($url, $formats=array())
	{
		$cmd = "youtube-dl";
		$cmd .= " --output ".$this->config["outputFolder"]."/";
		$cmd .= escapeshellarg("%(title)s-%(uploader)s.%(ext)s");
	
		if($this->IsManualFormatSelection())
		{
			$this->format->SetFormatIndices($formats);
		}

		if(!$this->format->GetFormatOption($format_option))
			throw new Exception("No valid format has been set for video $url");
		
		$cmd .= " ".$format_option;
		$cmd .= " ".escapeshellarg($url);
		
		$cmd .= " --restrict-filenames"; // --restrict-filenames is for specials chars
		$cmd .= " > /dev/null & echo $!";
	
		shell_exec($cmd);
	}

	private function load_infos()
	{
		$cmd_root = "youtube-dl";
		$cmd_root .= " --dump-json";

		// Note: Each url has to be processed separately, otherwise, if one returns error, followings are ignored.
		foreach($this->urls as $url)
		{
			$cmd = $cmd_root." ".escapeshellarg($url);
			$result = shell_exec($cmd);
			$json = json_decode($result);
			
			if(!empty($json))
			{
				$this->medias[md5($json->webpage_url)] = new Media($json);
			}
			else
			{
				$_SESSION['warnings'][] = 'Unable to retrieve informations for "<a href="'.$url.'" target="_blank">'.$url.'</a>". Is this link correct?';
			}
		}
	}
}

?>
