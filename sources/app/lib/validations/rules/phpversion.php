<?php

namespace App\Lib\Validations\Rules;

use Respect\Validation\Exceptions\ComponentException;
use Respect\Validation\Rules\AbstractRule;

class PhpVersion extends AbstractRule
{
	public $version;
	public $operator;

	public function __construct(string $version, string $operator = '>=')
	{
		$this->version = $version;
		$this->operator = $operator;
		if(!in_array($operator, ['<', '<=', '>', '>=', '=', '!=']))
		{
			throw new ComponentException(sprintf('unknown operator %s for validation', $operator));
		}
	}

	public function validate($version)
	{
		return version_compare($version, $this->version, $this->operator);
	}
}