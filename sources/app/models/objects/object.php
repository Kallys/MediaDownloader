<?php

namespace App\Models\Objects;

abstract class Object
{
	//! Database mapper
	protected $mapper = null;

	public function __construct(\DB\Jig\Mapper $mapper)
	{
		$this->mapper = $mapper;
	}

	public function __isset(string $name)
	{
		return $this->mapper->exists($name) || $name === '_id';
	}

	public function __get(string $name)
	{
		return $this->mapper->get($name);
	}
}
