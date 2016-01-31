<?php

namespace MediaDownloader;

class MediaInfo
{
	public $data;// Media Info Object returned by Youtube-dl
	
	private $formats = [];
	public $selected_format = null;
	private $root_format = null;
	
	public function __construct($media_info, $selected_format_name)
	{
		$this->data = $media_info;
		$this->root_format = new FormatInfo($media_info, $this);
		
		if(!empty($this->data->formats))
		{
			foreach($this->data->formats as $format)
			{
				$this->formats[] = new FormatInfo($format, $this);
				
				if(!empty($selected_format_name) && $selected_format_name == $format->format)
				{
					$this->selected_format = end($this->formats);
				}
			}
		}
	}
	
	public function IsPlaylist()
	{
		return $this->data->playlist != null;
	}
	
	public function HasAudioStream()
	{
		return isset($this->data->acodec) ? $this->data->acodec != 'none' : null;
	}
	
	public function HasVideoStream()
	{
		return isset($this->data->vcodec) ? $this->data->vcodec != 'none' : null;
	}
	
	public function GetTitle()
	{
		if($this->IsPlaylist())
			return $this->data->playlist.' - '.$this->data->title.' ('.$this->data->playlist_index.'/'.$this->data->n_entries.')';
		
		return $this->data->title;
	}
	
	public function GetFilename()
	{
		return $this->data->_filename;
	}

	public function GetFormatsInfoByStream()
	{
		if(count($this->formats) == 0)
		{
			return array($this->root_format->GetStreamType() => array($this->root_format));
		}
		else
		{
			$result = array();
			
			foreach($this->formats as $format_info)
			{
				$result[$format_info->GetStreamType()][] = $format_info;
			}
			
			return $result;
		}
	}
}