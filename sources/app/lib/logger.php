<?php

namespace App\Lib;

use App\Models\Config;

class Logger extends \Prefab
{
	const
		TYPE_DEBUG	= 3,
		TYPE_INFO	= 2,
		TYPE_WARN	= 1,
		TYPE_ERROR	= 0;

	private $log = null;
	private $titles	= [];

	public function __construct()
	{
		$this->log = new \Log('mediadownloader.log');
		$this->titles = array_flip(\Base::instance()->constants($this, 'TYPE_'));
	}

	public function log(int $type, string $message, $detail = null)
	{
		if(Config::Get('debug_level') >= $type)
		{
			$this->log->write($this->titles[$type] . ' - ' . $message . (is_null($detail) ? '' : PHP_EOL . var_export($detail, true)));
		}
	}

	public static function Warning(string $message, $detail = null)
	{
		self::instance()->log(self::TYPE_WARN, $message, $detail);
	}

	public static function Info(string $message, $detail = null)
	{
		self::instance()->log(self::TYPE_INFO, $message, $detail);
	}

	public static function Error(string $message, $detail = null)
	{
		self::instance()->log(self::TYPE_ERROR, $message, $detail);
	}

	public static function Debug(string $message, $detail = null)
	{
		self::instance()->log(self::TYPE_DEBUG, $message, $detail);
	}
}

?>
