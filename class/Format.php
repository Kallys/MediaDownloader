<?php

abstract class QualityEnum
{
	const Default_Quality = 0;
	
	const Best = 0;
	const Best_ever = 1;
	const Worst = 2;
	const Manual = 3;
	
	public static function IsValid($index)
	{
		return $index >= QualityEnum::Best && $index <= QualityEnum::Manual;
	}
}

abstract class StreamEnum
{
	const Default_Stream = 0;

	const Both			= 0;
	const Audio_only	= 1;
	const Video_only	= 2;
	
	public static $names = array(
		StreamEnum::Both		=> "Audio and Video",
		StreamEnum::Audio_only	=> "Audio only",
		StreamEnum::Video_only	=> "Video only"		
	);
	
	public static function IsValid($index)
	{
		return $index >= StreamEnum::Both && $index <= StreamEnum::Video_only;
	}
}

class Format
{
	public $stream;
	public $quality;
	public $format_indices = array();
	
	public function __construct($stream, $quality)
	{
		$this->stream = StreamEnum::IsValid($stream) ? $stream : StreamEnum::Default_Stream;
		$this->quality = QualityEnum::IsValid($quality) ? $quality : QualityEnum::Default_Quality;
	}
	
	private function GetEdgeCaseFormat($edgecase, $ever=false)
	{
		switch($this->stream)
		{
			// Audio extraction
			case StreamEnum::Audio_only:
				return $edgecase."audio";
					
			// Video extraction
			case StreamEnum::Video_only:
				return $edgecase."video";
		
			// Both Audio and Video
			case StreamEnum::Both:
				if($ever)
					return "(".$edgecase."video+".$edgecase."audio/".$edgecase.")";
					
				return $edgecase;
		}
	}
	
	public function SetFormatIndices($indices)
	{
		if($this->quality != QualityEnum::Manual)
			return false;
		
		$this->format_indices = array_filter($indices);
		return true;
	}
	
	public function NeedPostProcess()
	{
		return $this->quality == QualityEnum::Best_ever;
	}
	
	public function GetFormatOption(&$option)
	{
		$option = "--format ";
		
		switch($this->quality)
		{
			// Best quality
			case QualityEnum::Best:
				$option .= "'".$this->GetEdgeCaseFormat('best')."'";
				break;
				
			// Best quality
			case QualityEnum::Best_ever:
				$option .= "'".$this->GetEdgeCaseFormat('best', true)."'";
				break;
				
			// Worst quality
			case QualityEnum::Worst:
				$option .= "'".$this->GetEdgeCaseFormat('worst')."'";
				break;
					
			// Manually set quality
			case QualityEnum::Manual:
				{
					if(count($this->format_indices) == 0)
						return false;
					
					$option .= implode(",", array_map('escapeshellarg', $this->format_indices));
				}
				break;
		}

		return true;
	}
}