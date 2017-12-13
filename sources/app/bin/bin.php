<?php

namespace App\Bin;

class Ex_Bin extends \App\Lib\Exception {}

abstract class Bin
{
	public static function Run(\Commando\Command $commando)
	{
		return static::Execute($commando);
	}

	public static abstract function Execute(\Commando\Command $commando);
}