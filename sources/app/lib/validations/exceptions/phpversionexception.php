<?php

namespace App\Lib\Validations\Exceptions;

use \Respect\Validation\Exceptions\ValidationException;

class PhpVersionException extends ValidationException
{
	const EQUAL = 1;
	const NOT_EQUAL = 2;
	const LOWER = 3;
	const LOWER_EQUAL = 4;
	const GREATER = 5;
	const GREATER_EQUAL = 6;

	public static $defaultTemplates = [
		self::MODE_DEFAULT => [
			self::STANDARD		=> '{{name}} must be {{operator}} {{version}}.',
			self::EQUAL			=> '{{name}} must be equal to {{version}}.',
			self::NOT_EQUAL		=> '{{name}} must be not equal to {{version}}.',
			self::LOWER			=> '{{name}} must be lower than {{version}}.',
			self::LOWER_EQUAL	=> '{{name}} must be lower than or equal to {{version}}.',
			self::GREATER		=> '{{name}} must be greater than {{operator}} {{version}}.',
			self::GREATER_EQUAL	=> '{{name}} must be greater than or equal to {{version}}.'
		],
		self::MODE_NEGATIVE => [
			self::STANDARD		=> '{{name}} must not be {{operator}} {{version}}.',
			self::EQUAL			=> '{{name}} must not be equal to {{version}}.',
			self::NOT_EQUAL		=> '{{name}} must not be not equal to {{version}}.',
			self::LOWER			=> '{{name}} must not be lower than {{version}}.',
			self::LOWER_EQUAL	=> '{{name}} must not be lower than or equal to {{version}}.',
			self::GREATER		=> '{{name}} must not be greater than {{operator}} {{version}}.',
			self::GREATER_EQUAL	=> '{{name}} must not be greater than or equal to {{version}}.'
		],
	];

	public function chooseTemplate()
	{
		switch($this->getParam('operator'))
		{
			case '=': return static::EQUAL;
			case '!=': return static::NOT_EQUAL;
			case '<': return static::LOWER;
			case '<=': return static::LOWER_EQUAL;
			case '>': return static::GREATER;
			case '>=': return static::GREATER_EQUAL;
		}
		return static::STANDARD;
	}
}