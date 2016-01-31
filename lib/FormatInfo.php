<?php

namespace MediaDownloader;

class FormatInfo
{
	public $data;
	private $media_info;
	
	public function __construct($format_data, $media_info)
	{
		$this->data = $format_data;
		$this->media_info = $media_info;
	}

	public function GetStreamType()
	{
		// Beware : could return null
		$audio = $this->HasAudioStream();
		$video = $this->HasVideoStream();
	
		if($video == false && $audio)
			return StreamEnum::Audio_only;
	
		if($audio == false && $video)
			return StreamEnum::Video_only;

		return StreamEnum::Both;
	}
	
	public function HasAudioStream()
	{
		return isset($this->data->acodec) ? $this->data->acodec != 'none' : $this->media_info->HasAudioStream();
	}
	
	public function HasVideoStream()
	{
		return isset($this->data->vcodec) ? $this->data->vcodec != 'none' : $this->media_info->HasVideoStream();
	}
	
	public function GetFilename()
	{
		return isset($this->data->ext) ? $this->media_info->data->title.'.'.$this->data->ext : $this->media_info->GetFilename();
	}
	
	public function GetFileSize()
	{
		if(isset($this->data->filesize) && !empty($this->data->filesize))
			return Utils\HumanReadable::GetFileSize($this->data->filesize);
	
		if(isset($this->data->filesize_approx) && !empty($this->data->filesize_approx))
			return '~'.Utils\HumanReadable::GetFileSize($this->data->filesize_approx);

		return '?';
	}
}