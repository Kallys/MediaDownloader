<?php

namespace App\Models;

class Ex_Duplicate extends \App\Lib\Exception {}
class Ex_InvalidData extends \App\Lib\Exception {}
class Ex_OutOfBounds extends Ex_InvalidData {}

abstract class Model extends \Prefab
{
	protected static $db = null;
	protected $mapper = null;
	private $object_class = null;

	protected function __construct(string $object_class = null)
	{
		$this->mapper = new \DB\Jig\Mapper(self::GetJIGDB(), $this->GetTableName());
		$this->object_class = $object_class;

		if(!$this->TableExists())
		{
			$this->onCreate();
		}
	}

	public static function Destroy()
	{
		if(self::TableExists())
		{
			unlink(self::getTablePath());
		}
	}

	// Called on first time table is create
	protected function onCreate()
	{
	}

	protected static function CheckBounds(int $value, int $min = null, int $max = null)
	{
		if(!is_null($min) && $value < $min)
		{
			throw new Ex_OutOfBounds($value . ' < ' . $min);
		}

		if(!is_null($max) && $value > $max)
		{
			throw new Ex_OutOfBounds($value . ' > ' . $max);
		}
	}

	// Lazzy DB connection : only connects if needed
	public static function GetJIGDB()
	{
		if(is_null(self::$db))
		{
			self::$db = new \DB\Jig(\App\DIR_DATABASES);
		}

		return self::$db;
	}


	private static function GetTableName()
	{
		return (new \ReflectionClass(static::class))->getShortName();
	}

	private static function getTablePath()
	{
		return \App\DIR_DATABASES . self::GetTableName();
	}

	protected function getFilter(array $fields)
	{
		$filter[] = '';

		foreach($fields as $field => $value)
		{
			if(is_null($value))
			{
				continue;
			}

			!empty($filter[0]) && $filter[0] .= ' && ';

			if(is_array($value))
			{
				if(!empty($value))
				{
					$filter[0] .= '(';
					$filter[0] .= '@'.$field.' == '.$value[0];
					for($i=1; isset($value[$i]); $i++)
					{
						$filter[0] .= ' || @'.$field.' == '.$value[$i];
					}
					$filter[0] .= ')';
				}
			}
			else
			{
				$filter[0] .= '@'.$field.' == ?';
				$filter[] = $value;
			}
		}

		if(empty($filter[0]))
			return null;

		return $filter;
	}

	public function GetAll()
	{
		return $this->NewObject($this->mapper->find());
	}

	public static function TableExists()
	{
		return file_exists(self::getTablePath());
	}


	protected function NewObject($mapper)
	{
		if(is_array($mapper))
		{
			$result = [];
			foreach($mapper as $m)
			{
				$result[] = $this->NewObject($m);
			}
			return $result;
		}

		return $mapper ? new $this->object_class($mapper) : null;
	}
}