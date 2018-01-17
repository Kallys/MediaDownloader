<?php

namespace App\Lib;

use App\Models\Config;
use App\Models\Objects\Media;
use App\Models\Objects\Download;

// Youtube-dl interface
abstract class YoutubeDl
{
	// Quality
	const
		QUALITY_BEST		= 0,
		QUALITY_BEST_EVER	= 1,
		QUALITY_WORST		= 2,
		QUALITY_MANUAL		= 3;

	// Streams
	const
		STREAM_BOTH		= 0,
		STREAM_AUDIO	= 1,
		STREAM_VIDEO	= 2;

	private static function GetEdgeCaseFormat(int $stream, string $edgecase, bool $ever = false)
	{
		switch($stream)
		{
			// Audio extraction
			case self::STREAM_AUDIO:
				return $edgecase . 'audio';

			// Video extraction
			case self::STREAM_VIDEO:
				return $edgecase . 'video';

			// Both Audio and Video
			case self::STREAM_BOTH:
				if($ever)
					return '(' . $edgecase . 'video+' . $edgecase . 'audio/' . $edgecase . ')';
				else
					return $edgecase;

			default:
				throw new Exception('Invalid stream');
		}
	}

	private static function GetFormatOption(int $quality, int $stream)
	{
		switch($quality)
		{
			// Best quality
			case self::QUALITY_BEST:
				return '\'' . self::GetEdgeCaseFormat($stream, 'best') . '\'';

			// Best quality
			case self::QUALITY_BEST_EVER:
				return '\'' . self::GetEdgeCaseFormat($stream, 'best', true) . '\'';

			// Worst quality
			case self::QUALITY_WORST:
				return '\'' . self::GetEdgeCaseFormat($stream, 'worst') . '\'';

			default:
				throw new Exception('Invalid quality');
		}
	}

	public static function Download(Download $download)
	{
		$cmd = Config::Get('youtubedl_path');
		$cmd .= ' --output ' . escapeshellarg(Config::instance()->download_path . '%(title)s-%(uploader)s-' . $download->format_id . '.%(ext)s');
		$cmd .= ' --restrict-filenames'; // --restrict-filenames is for specials chars
		$cmd .= ' --load-info-json ' . escapeshellarg($download->GetMedia()->GetInfoFilePath());
		$cmd .= ' --format ' . $download->format_id;
		$cmd .= ' --newline'; // Needed in order to read downloading infos
		$cmd .= ' ' . escapeshellarg(Config::Get('youtubedl_args'));

		$command = new Command($cmd, $download->GetLogFilePath(), $download->GetErrorFilePath());

		$cmd_on_success = DIR_BASE . 'mdc downloads ' . $download->_id . ' --state ' . Download::State_Finished;
		$command_on_success = new Command($cmd_on_success);

		// Run command
		Process::RunBackground($command, $command_on_succes);

		// Search for youtubedl subprocess pid
		if(is_null($pid = Process::SearchPid(['python ' . Config::Get('youtubedl_path'), $download->media_id, 'format ' . $download->format_id])))
		{
			throw new Exception('Unable to retrieve youtube-dl sub-process PID');
		}

		return $pid;
	}

	public static function GetInfos(string $url)
	{
		$cmd = Config::Get('youtubedl_path');
		$cmd .= ' --skip-download';
		$cmd .= ' --no-warnings'; // JSON parsing will fail if warnings are printed
		$cmd .= ' --output "%(extractor)s_%(id)s"';
		$cmd .= ' --restrict-filenames';
		$cmd .= ' --dump-json';
		$cmd .= ' ' . escapeshellarg($url);

		$output = Process::Run(new Command($cmd));

		if(empty($output[0]))
		{
			throw new \App\Lib\Exception('Can\'t load info for URL: ' . $url);
		}

		return $output[0];
	}

	/**
	 *
	 * @param Media $media
	 * @param int $quality
	 * @param int $stream
	 * @throws \App\Lib\Exception
	 * @return string describing format (ex: "158", "151+128"...)
	 */
	public static function QueryFormat(Media $media, int $quality, int $stream)
	{
		$cmd = Config::Get('youtubedl_path');
		$cmd .= ' --skip-download';
		$cmd .= ' --no-warnings'; // JSON parsing will fail if warnings are printed
		$cmd .= ' --output "%(format_id)s"';
		$cmd .= ' --dump-json';
		$cmd .= ' --load-info-json ' . escapeshellarg($media->GetInfoFilePath());
		$cmd .= ' --format ' . self::GetFormatOption($quality, $stream);

		$output = Process::Run(new Command($cmd));

		if(empty($output[0]) || empty($content = json_decode($output[0])) || empty($content->_filename))
		{
			throw new \App\Lib\Exception('Can\'t query format for URL: ' . $media->url);
		}

		return $content->_filename;
	}
}