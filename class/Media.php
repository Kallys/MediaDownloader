<?php

require_once 'class/Format.php';

class Media
{
	public $data;// Media Info Object returned by Youtube-dl
	
	public static $format_table_header = array(
		StreamEnum::Both => array(
			'Format',
			'Extension',
			'Resolution',
			'Filesize',
			'Video codec',
			'Audio codec',
			'FPS'
		),
		StreamEnum::Video_only => array(
			'Format',
			'Extension',
			'Resolution',
			'Filesize',
			'Video codec',
			'FPS',
			'TBR'
		),
		StreamEnum::Audio_only => array(
			'Format',
			'Extension',
			'Filesize',
			'Audio codec',
			'TBR',
			'ABR'
		)
	);
	
	public function __construct($media_info)
	{
		$this->data = $media_info;
	}
	
	public function IsPlaylist()
	{
		return $this->data->playlist != null;
	}
	
	public function GetStreamType($format_id)
	{
		// Beware : could return null
		$audio = $this->HasAudioStream($format_id);
		$video = $this->HasVideoStream($format_id);
		
		if($video == false && $audio)
			return StreamEnum::Audio_only;
		
		if($audio == false && $video)
			return StreamEnum::Video_only;

		return StreamEnum::Both;
	}
	
	public function HasAudioStream($format_id)
	{
		if(!isset($this->data->formats[$format_id]))
		{
			return isset($this->data->acodec) ? $this->data->acodec != 'none' : null;
		}
		
		return isset($this->data->formats[$format_id]->acodec) ? $this->data->formats[$format_id]->acodec != 'none' : null;
	}
	
	public function HasVideoStream($format_id)
	{
		if(!isset($this->data->formats[$format_id]))
		{
			return isset($this->data->vcodec) ? $this->data->vcodec != 'none' : null;
		}
		
		return isset($this->data->formats[$format_id]->vcodec) ? $this->data->formats[$format_id]->vcodec != 'none' : null;
	}
	
	public function GetFormatTable(&$header, &$body)
	{
		$header = $body = array();
		
		if($this->IsPlaylist())
			$header['title'] = $this->data->playlist.' - '.$this->data->title.' ('.$this->data->playlist_index.'/'.$this->data->n_entries.')';
		else
			$header['title'] = $this->data->title;
		
		if(!isset($this->data->formats) || count($this->data->formats) == 0)
		{
			$this->GetFormatTableBody(0, $this->data, $body);
		}
		else 
		{
			foreach($this->data->formats as $format_id=>$format)
			{
				$this->GetFormatTableBody($format_id, $format, $body);
			}
		}
	}
	
	private function GetFileSize($format)
	{
		if(isset($format->filesize) && !empty($format->filesize))
			return HumanReadable::GetFileSize($format->filesize);
		
		if(isset($format->filesize_approx) && !empty($format->filesize_approx))
			return '~'.HumanReadable::GetFileSize($format->filesize_approx);
		
		return '?';
	}
	
	private function GetFormatTableBody($format_id, $format, &$body)
	{
		switch($this->GetStreamType($format_id))
		{
			case StreamEnum::Audio_only:
				$body[StreamEnum::Audio_only][] = array(
				$format,
				(empty($format->format) ? $format->format_note : $format->format),
				$format->ext,
				$this->GetFileSize($format),
				(!empty($format->acodec) ? $format->acodec : '?'),
				(empty($format->tbr) ? '?' : $format->tbr),
				(empty($format->abr) ? '?' : $format->abr)
				);
				break;
					
			case StreamEnum::Video_only:
				$body[StreamEnum::Video_only][] = array(
				$format,
				(empty($format->format) ? $format->format_note : $format->format),
				$format->ext,
				(!empty($format->width) ? $format->width.'x'.$format->height : '?'),
				$this->GetFileSize($format),
				(!empty($format->vcodec) ? $format->vcodec : '?'),
				(!empty($format->fps) ? $format->fps : '?'),
				(empty($format->tbr) ? '?' : $format->tbr)
				);
				break;
					
			case StreamEnum::Both:
				$body[StreamEnum::Both][] = array(
				$format,
				(empty($format->format) ? $format->format_note : $format->format),
				$format->ext,
				(!empty($format->width) ? $format->width.'x'.$format->height : '?'),
				$this->GetFileSize($format),
				(!empty($format->vcodec) ? $format->vcodec : '?'),
				(!empty($format->acodec) ? $format->acodec : '?'),
				(!empty($format->fps) ? $format->fps : '?')
				);
				break;
		}
	}
}