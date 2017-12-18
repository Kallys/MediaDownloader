<?php

namespace App\Lib;

// Note: Can't implement ArrayAccess since increment operators are not supported ($keys_counter[$key]++ won't call ::offsetSet)
class KeysCounter
{
	private $data = [];

	public function Set($key, $value)
	{
		$this->data[$key] = $value;
	}

	public function Add($key)
	{
		$this->Set($key, $this->Get($key) + 1);
	}

	public function Get($key)
	{
		if(!array_key_exists($key, $this->data))
		{
			$this->data[$key] = 0;
		}

		return $this->data[$key];
	}
}
