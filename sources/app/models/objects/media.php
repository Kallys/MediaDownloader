<?php

namespace App\Models\Objects;

use \App\Lib\FormatInfo;
use App\Lib\YoutubeDl;

class Ex_NoInfo extends \App\Lib\Exception {}

class Media extends MObject
{
	//! Media Info Object returned by Youtube-dl
	private $infos = null;

	//! List of downloads
	private $downloads = null;

	//! List of formats
	private $formats = [];

	//private $root_format = null;

	public function __isset(string $name)
	{
		return parent::__isset($name) || $this->InfoExists($name);
	}

	public function __get(string $name)
	{
		if(parent::__isset($name))
		{
			return parent::__get($name);
		}

		$val = null;
		return $this->InfoExists($name, $val) ? $val : null;
	}

	// Lazzy load infos ('coz I can be long)
	private function GetInfos()
	{
		if(is_null($this->infos))
		{
			$this->loadInfos();
		}

		return $this->infos;
	}

	private function InfoExists(string $name, &$val = null)
	{
		$val = $this->GetInfos()->{$name};
		return property_exists($this->infos, $name);

	}

	public function GetInfoFilePath()
	{
		return \App\DIR_TEMP . $this->mapper->_id . '.json';
	}

	public function GetDownloads()
	{
		if(is_null($this->downloads))
		{
			$this->downloads = [];

			if(!is_null($downloads = \App\Models\Downloads::instance()->GetAllByMediaId($this->mapper->_id)))
			{
				foreach($downloads as $download)
				{
					$this->downloads[$download->format_id] = $download;
				}
			}
		}

		return $this->downloads;
	}

	public function QueryFormat(int $quality, int $stream)
	{
		// Be sure info file exists
		if(!file_exists($this->GetInfoFilePath()))
		{
			$this->loadInfos();
		}
		return YoutubeDl::QueryFormat($this, $quality, $stream);
	}

	public function Download(string $format_id, string $download_path)
	{
		return \App\Models\Downloads::instance()->New($this->mapper->_id, $format_id, $download_path);
	}

	public function GetDownloadByFormatId(string $format_id)
	{
		return array_key_exists($format_id, $this->GetDownloads()) ? $this->downloads[$format_id] : null;
	}

	private function loadInfos()
	{
		// Load media infos from cached file (json)
		if(is_readable($this->GetInfoFilePath()) && ($json = file_get_contents($this->GetInfoFilePath())) !== false)
		{
			$this->infos = $this->ParseJsonInfos($json);
		}
		else
		{
			try {
				$json = YoutubeDl::GetInfos($this->mapper->url);
			}
			catch(\App\Lib\Ex_CommandFailed $e)
			{
				throw new Ex_NoInfo(null, $e->getDetail());
			}

			$this->infos = $this->ParseJsonInfos($json);

			// Write infos to file
			if(file_put_contents($this->getInfoFilePath(), $json) === false)
			{
				throw new \App\Lib\Exception('Unable to write info file for URL: ' . $this->mapper->url);
			}
		}

		//$this->root_format = new FormatInfo($this->GetInfos());

		if(!empty($this->infos->formats))
		{
			foreach($this->infos->formats as $format)
			{
				$this->formats[$format->format_id] = new FormatInfo($format);
			}
		}
	}

	private function ParseJsonInfos(string $json_string)
	{
		if(empty($content = json_decode($json_string)))
		{
			throw new Ex_NoInfo('Unable to parse JSON infos.');
		}

		return $content;
	}

	public function GetTitle()
	{
		return $this->IsPlaylist() ?
			$this->infos->playlist . ' - ' . $this->infos->title . ' (' . $this->infos->playlist_index . '/' . $this->infos->n_entries . ')' :
			$this->infos->title;
	}

	public function GetFilename()
	{
		return $this->GetInfos()->_filename;
	}

	public function IsPlaylist()
	{
		return !is_null($this->GetInfos()->playlist);
	}

	public function GetFormatInfosById(string $format_id)
	{
		if(is_int($format_id))
		{
			return array_key_exists($format_id, $this->GetFormatsInfos()) ? [$this->formats[$format_id]] : null;
		}
		else if(preg_match('/(\d+)\+(\d+)/', $format_id, $matches) && array_key_exists($matches[1], $this->GetFormatsInfos()) && array_key_exists($matches[2], $this->GetFormatsInfos()))
		{
			return [$this->formats[$matches[1]], $this->formats[$matches[2]]];
		}
		return null;
	}

	public function GetFormatsInfos()
	{
		$this->GetInfos();
		return $this->formats;
	}
}

?>
