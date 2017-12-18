<?php

namespace App\Lib\Traits;

trait ReadAccess
{
	public function __get($name)
	{
		return $this->__isset($name) ? $this->{$name} : null;
	}
	
	public function __isset($name)
	{
		return isset($this->{$name});
	}
}

?>
