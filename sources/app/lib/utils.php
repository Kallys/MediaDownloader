<?php

namespace App\Lib;

abstract class Utils
{
	public static function GetOutputFreeSpace()
	{
		return Human::GetFileSize(disk_free_space(\App\Models\Config::Get('download_path')));
	}
}