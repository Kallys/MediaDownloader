<?php

namespace App\Lib;

abstract class Human
{
	const FACTORS = ['', 'k', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y'];
	const UNIT_SIZES = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

	public static function GetFileSize(int $bytes, int $decimals = 2)
	{
		$factor = floor((strlen($bytes) - 1) / 3);
		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . self::UNIT_SIZES[$factor];
	}

	public static function GetFactor(int $bytes, int $decimals = 0)
	{
		$factor = floor((strlen($bytes) - 1) / 3);
		return sprintf("%.{$decimals}f", $bytes / pow(1000, $factor)) . self::FACTORS[$factor];
	}
}