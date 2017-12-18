<?php

namespace App\Lib\Validations\Exceptions;

use \Respect\Validation\Exceptions\ValidationException;

class YoutubeDlException extends ValidationException
{
	public static $defaultTemplates = [
		self::MODE_DEFAULT => [
			self::STANDARD => '{{name}} must be a path to youtube-dl binary.',
		],
		self::MODE_NEGATIVE => [
			self::STANDARD => '{{name}} must not be a path to youtube-dl binary.',
		],
	];
}