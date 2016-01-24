<?php

abstract class HumanReadable
{
	private static $filesize = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');

	public static function GetFileSize($bytes, $decimals=2)
	{
		$factor = floor((strlen($bytes) - 1) / 3);
		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @HumanReadable::$filesize[$factor];
	}
}