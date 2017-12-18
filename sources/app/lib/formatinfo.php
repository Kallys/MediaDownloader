<?php

namespace App\Lib;

class FormatInfo
{
	private $data;

	public function __construct(\stdClass $format_data)
	{
		$this->data = $format_data;
	}

	private static function ToText($value, bool $prerequisite = true)
	{
		return $prerequisite ? (empty($value) ? '?' : $value) : '-';
	}

	public function GetFormatId()
	{
		return $this->data->format_id;
	}

	public function GetUrl()
	{
		return $this->data->url;
	}

	public function GetFormatName()
	{
		return empty($this->data->format) ? $this->data->format_note : $this->data->format;
	}

	public function GetStreamType()
	{
		if($this->HasVideoStream() && $this->HasAudioStream())
		{
			return YoutubeDl::STREAM_BOTH;
		}

		return $this->HasVideoStream() ? YoutubeDl::STREAM_VIDEO : YoutubeDl::STREAM_AUDIO;
	}

	public function HasAudioStream()
	{
		return isset($this->data->acodec) && $this->data->acodec != 'none';
	}

	public function HasVideoStream()
	{
		return isset($this->data->vcodec) && $this->data->vcodec != 'none';
	}

	public function GetResolution()
	{
		return $this->HasVideoStream() ? self::ToText($this->data->width) . 'x' . self::ToText($this->data->height) : '-';
	}

	public function GetVideoCodec()
	{
		return self::ToText($this->data->vcodec, $this->HasVideoStream());
	}

	public function GetAudioCodec()
	{
		return self::ToText($this->data->acodec, $this->HasAudioStream());
	}

	public function GetExtension()
	{
		return self::ToText($this->data->ext);
	}

	public function GetFPS()
	{
		return self::ToText($this->data->fps, $this->HasVideoStream());
	}

	public function GetTBR()
	{
		return self::ToText($this->data->tbr, $this->HasVideoStream());
	}

	public function GetABR()
	{
		return self::ToText($this->data->abr, $this->HasAudioStream());
	}

	public function GetFileSize()
	{
		if(isset($this->data->filesize) && !empty($this->data->filesize))
			return \App\Lib\Human::GetFileSize($this->data->filesize);

		if(isset($this->data->filesize_approx) && !empty($this->data->filesize_approx))
			return '~' . \App\Lib\Human::GetFileSize($this->data->filesize_approx);

		return '?';
	}
}