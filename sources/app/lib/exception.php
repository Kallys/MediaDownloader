<?php

namespace App\Lib;

class Exception extends \Exception
{
	private $detail = null;

	public function __construct($message = null, $detail = null, $code = null, $previous = null)
	{
		parent::__construct(get_called_class() . ': ' . $message, $code, $previous);
		$this->detail = $detail;
		Logger::Error($this->getMessage() . PHP_EOL . $this->getTraceAsString(), $detail);
	}

	public function getDetail()
	{
		return $this->detail;
	}
}

?>
