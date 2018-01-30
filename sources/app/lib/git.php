<?php

namespace App\Lib;

// Git interface
abstract class Git
{
	const
		DIR_GIT = \DIR_BASE. '.git/',
		FILE_VERSION = \APP\DIR_TEMP . 'GIT_VERSION';
	
	private static function GetCommand(string $action)
	{
		$cmd = 'git';
		$cmd .= ' --git-dir=' . escapeshellarg(self::DIR_GIT);
		$cmd .= ' --work-tree=' . escapeshellarg(\DIR_BASE);
		$cmd .= ' ' . $action;
		
		return $cmd;
	}
	
	public static function GetVersion(bool $use_cache = true)
	{
		// Return cached version if enabled and DIR_GIT not modified since last caching
		if($use_cache && file_exists(self::FILE_VERSION) && filemtime(self::FILE_VERSION) > filemtime(self::DIR_GIT))
		{
			return file_get_contents(self::FILE_VERSION);
		}
		
		$version = null;
		
		try
		{
			list($version) = Process::Run(new Command(self::GetCommand('describe --tags')));
		}
		catch(\App\Lib\Ex_CommandFailed $e)
		{}
		
		// Cache version
		file_put_contents(self::FILE_VERSION, $version);

		return $version;
	}
}