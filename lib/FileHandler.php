<?php

namespace MediaDownloader;

class FileHandler
{
	public function __construct()
	{
	}

	public function listFiles()
	{
		$files = array();
		$folder = Utils\Config::Get('output_folder');

		foreach(glob($folder.'*.*', GLOB_BRACE) as $filename)
		{
			$files[] = array(
				"name" => str_replace($folder, "", $filename),
				"size" => Utils\HumanReadable::GetFileSize(filesize($filename))
			);
		}
		
		return $files;
	}

	public function delete($ids)
	{
		$folder = Utils\Config::Get('output_folder');
		$i = 0;
		$files_to_remove = array();

		foreach(glob($folder.'*.*', GLOB_BRACE) as $file)
		{
			if(in_array($i, $ids))
			{
				$files_to_remove[] = $file;
			}
			$i++;
		}
		
		foreach($files_to_remove as $file)
		{
			unlink($file);
		}
	}

	public static function free_space()
	{
		if(Utils\Config::GetInstance()->DoesOutputFolderExist())
			return Utils\HumanReadable::GetFileSize(disk_free_space(Utils\Config::Get('output_folder')));
		else
			return '?';
	}
}

?>