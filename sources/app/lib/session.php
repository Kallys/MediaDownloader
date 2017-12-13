<?php

namespace App\Lib;

// Caution: Session loading will fail if started at render time
// Hive "SESSION" value should never be used directly, use this class instead.
class Session extends \Prefab
{
	private $session = null;

	public function __construct()
	{
		// Start SQL Session (create new one if not exist, load if exists)
		$this->session = new \DB\Jig\Session(\App\Models\Model::GetJIGDB());
	}

	public function Get($name)
	{
		return \Base::instance()->get('SESSION.'.$name);
	}

	public function Set($name, $value)
	{
		return \Base::instance()->set('SESSION.'.$name, $value);
	}

	public function Push($name, $value)
	{
		return \Base::instance()->push('SESSION.'.$name, $value);
	}

	public function Clear($name)
	{
		return \Base::instance()->clear('SESSION.'.$name);
	}

	public function Exists($name)
	{
		return \Base::instance()->exists('SESSION.'.$name);
	}

	public function Destroy()
	{
		return \Base::instance()->clear('SESSION');
	}
}