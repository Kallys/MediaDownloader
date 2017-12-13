<?php

namespace App\Lib;

abstract class Alerter
{
	const
		Level_Warning = 'warning',
		Level_Error = 'danger';

	private static $session_key = 'Alerter';

	private static function addMessage(string $message, string $type=self::Level_Warning, string $title = null, $object = null)
	{
		\Base::instance()->mutex(__CLASS__, function($message, $title, $type, $object)
		{
			Session::instance()->Push(self::$session_key, array(
				'type'		=> $type,
				'title'		=> $title,
				'message'	=> $message,
				'object'	=> $object));
		}, array($message, $title, $type, $object));
	}

	public static function Warning($message, string $title = null, $object=null)
	{
		self::addMessage($message, self::Level_Warning, $title, $object);
	}

	public static function Error($message, string $title = null, $object=null)
	{
		self::addMessage($message, self::Level_Error, $title, $object);
	}

	public static function GetMessages()
	{
		return \Base::instance()->mutex(__CLASS__, function() {
			$messages = Session::instance()->Get(self::$session_key);
			Session::instance()->Clear(self::$session_key);
			return $messages;
		});
	}
}

?>
