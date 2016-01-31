<?php

namespace MediaDownloader;

class Downloader
{
	public $medias = array();
	public $format;// Output format
	
	private $urls = [];
	private $download_mode = 'indirect';

	public function __construct($post, $stream, $quality, $download_mode)
	{
		$this->format = new Format($stream, $quality);
		$this->download_mode = $download_mode;

		$this->check_requirements();
		
		$urls = array_unique(array_filter(array_map('trim', explode("\r\n", $post))));
		
		foreach($urls as $url)
		{			
			if(!$this->is_valid_url($url))
			{
				$_SESSION['warnings'][] = "\"".$url."\" is not a valid url !";
			}
			else
			{
				$this->urls[] = $url;
			}
		}

		if($this->NeedSelectionPage())
		{
			$this->load_infos();
			Utils\Session::getInstance()->set('downloader', $this);
		}
	}
	
	public function NeedSelectionPage()
	{
		return $this->IsManualFormatSelection() || $this->IsDirectDownloadMode();
	}
	
	public function IsManualFormatSelection()
	{
		return $this->format->quality == QualityEnum::Manual;
	}
	
	public function IsDirectDownloadMode()
	{
		return $this->download_mode == 'direct';
	}

	public static function background_jobs()
	{
		return shell_exec("ps aux | grep youtube-dl | grep -v grep | grep -v \"youtube-dl -U\" | wc -l");
	}

	public static function max_background_jobs()
	{
		return Utils\Config::Get('max_dl');
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

		foreach(glob(Utils\Config::Get('output_folder').'*.part') as $file)
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
				Error::getInstance()->Warning("Internal error on video selection.");
				continue;
			}
			
			$this->do_download($this->medias[$video_index]->data->webpage_url, $choosen_formats[$video_index]);
		}
	}
	
	private function check_requirements()
	{
		if(!$this->is_youtubedl_installed())
			throw new \Exception("Youtube-dl is not installed, see <a>https://rg3.github.io/youtube-dl/download.html</a> !");

		if($this->format->NeedPostProcess() && !$this->isPostProcessorInstalled())
			throw new \Exception("Install an audio extracter (avconv or ffmpeg) !");
	}

	private function is_youtubedl_installed()
	{
		exec("which youtube-dl", $out, $r);
		return $r == 0;
	}

	private function isPostProcessorInstalled()
	{
		exec("which ".Utils\Config::Get('post_processor'), $out, $r);
		return $r == 0;
	}

	private function is_valid_url($url)
	{
		return filter_var($url, FILTER_VALIDATE_URL);
	}

	private function get_post_processor()
	{
		switch(Utils\Config::Get('post_processor'))
		{
			case 'avconv':
				return '--prefer-avconv';
			
			case 'ffmpeg':
				return '--prefer-ffmpeg';
		}
		
		throw new \Exception('Unsupported post-processor defined in config.php file. ('.Utils\Config::Get('post_processor').')');
	}

	public function download()
	{
		foreach($this->urls as $url)
		{
			if(Utils\Config::Get('max_dl') > 0 && $this->background_jobs() >= Utils\Config::Get('max_dl'))
				throw new \Exception("Simultaneous downloads limit reached !");

			$this->do_download($url);
		}
	}
	
	private function do_download($url, $formats=array())
	{
		$cmd = 'youtube-dl';
		$cmd .= ' --output '.escapeshellarg(Utils\Config::Get('output_folder').'%(title)s-%(uploader)s.%(ext)s');
	
		if($this->IsManualFormatSelection())
		{
			$this->format->SetFormatIndices($formats);
		}

		if(!$this->format->GetFormatOption($format_option))
			throw new \Exception("No valid format has been set for video $url");
		
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
		$cmd_root .= " --get-format";
		
		if(!$this->IsManualFormatSelection())
		{
			if(!$this->format->GetFormatOption($format_option))
				throw new \Exception("No valid format has been set for media $url");
					
			$cmd_root .= " ".$format_option;
		}
	
		// Note: Each url has to be processed separately, otherwise, if one returns error, followings are ignored.
		foreach($this->urls as $url)
		{
			$cmd = $cmd_root." ".escapeshellarg($url);
			$result = shell_exec($cmd);
			$lines = explode(PHP_EOL, $result, 2);
			$json = empty($lines[1]) ? null : json_decode($lines[1]);
			
			if(!empty($json))
			{
				$selected_format_name = $this->IsManualFormatSelection() ? null : $lines[0];
				$this->medias[md5($json->webpage_url)] = new MediaInfo($json, $selected_format_name);
			}
			else
			{
				$_SESSION['warnings'][] = 'Unable to retrieve informations for "<a href="'.$url.'" target="_blank">'.$url.'</a>". Is this link correct?';
			}
		}
	}
}

?>
