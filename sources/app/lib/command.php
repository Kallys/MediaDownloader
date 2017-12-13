<?php

namespace App\Lib;

class Command
{
	private $command = null;
	private $log_file_path = null;
	private $error_file_path = null;

	public function __construct(string $command, string $log_file_path = null, string $error_file_path = null)
	{
		$this->command = $command;
		$this->log_file_path = $log_file_path;
		$this->error_file_path = $error_file_path;
	}

	public function GetCommand(string $default_log_file_path = null, string $default_error_file_path = null)
	{
		$command = $this->command;

		$log_file_path = is_null($this->log_file_path) ? $default_log_file_path : $this->log_file_path;
		$error_file_path = is_null($this->error_file_path) ? $default_error_file_path : $this->error_file_path;

		return
			$this->command .
			(is_null($log_file_path) ? '' : ' > ' . escapeshellarg($log_file_path)) .
			(is_null($error_file_path) ? '' : ($default_error_file_path == '&1' ? ' 2>&1' : ' 2> ' . escapeshellarg($error_file_path))
		);
	}
}

?>