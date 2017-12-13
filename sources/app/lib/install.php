<?php

namespace App\Lib;

class Ex_Config_NotFound extends Exception {}

class Install extends \Prefab
{
	private $configurations = null;

	public function __construct(string $file_path, \Base $f3)
	{
		if(!is_readable($file_path))
		{
			throw new Ex_Config_NotFound($file_path);
		}

		$config = require $file_path;

		// Check debug level
		$config['debug_level'] = isset($config['debug_level']) && $config['debug_level'] >= 0 && $config['debug_level'] <= 4 ? $config['debug_level'] : 0;

		// URI settings
		$config['url_base'] = $config['url_base'] ?
			rtrim($config['url_base'], '/') :
			$f3->get('SCHEME') . '://' . $f3->get('HOST') .
			(
				in_array($f3->get('PORT'), array(80, 443)) ?
				'' :
				':' . $f3->get('PORT')
			) .	$f3->get('BASE');

		$this->configurations = $config;
	}

	public function GetAll()
	{
		return $this->configurations;
	}

	public function __get($name)
	{
		return array_key_exists($name, $this->configurations) ? $this->configurations[$name] : null;
	}

	public function __isset($name)
	{
		return isset($this->configurations[$name]);
	}
}

?>