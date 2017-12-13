<?php

namespace App\Lib;

class Ex_CommandFailed extends Exception {}
class Ex_ProcessAlreadyRunning extends Exception {}

abstract class Process
{
	/**
	 * @return int pid of the background command.
	 */
	public static function RunBackground(Command $command, Command $command_on_success = null)
	{
		$cmd = '( ' . $command->GetCommand('/dev/null', '/dev/null');

		if(!is_null($command_on_success))
		{
			$cmd .= ' && ' . $command_on_success->GetCommand('/dev/null', '/dev/null');
		}

		$cmd .= ') > /dev/null 2> /dev/null &';
		$cmd .= ' echo $!'; // Return PID

		Logger::Info('Process::RunBackground: "' . $cmd . '"');

		exec($cmd, $output, $result);

		if($result != 0)
		{
			throw new Ex_CommandFailed($cmd, $output, $result);
		}

		return $output[0];// Pid
	}

	/**
	 * @return array output result of the command (line by line).
	 */
	public static function Run(Command $command)
	{
		Logger::Info('Process::Run: "' . $command->GetCommand() . '"');

		exec($command->GetCommand(null, '&1'), $output, $result);

		if($result !== 0)
		{
			throw new Ex_CommandFailed($command->GetCommand(), $output, $result);
		}

		return $output;
	}

	/**
	 * @return bool true if the process is running, false otherwise.
	 */
	public static function IsRunning(int $pid)
	{
		return $pid > 0 ? posix_kill(intval($pid), 0) : false;
	}

	/**
	 * @return bool true if the process id is the same as current process, false otherwise.
	 */
	public static function AmI(int $pid)
	{
		return $pid > 0 ? getmypid() === $pid : false;
	}

	public static function Kill(int $pid)
	{
		if(!self::IsRunning($pid))
		{
			throw new Exception('Trying to kill a non running process (PID=' . $pid . ')');
		}

		if(!posix_kill(intval($pid), 15))
		{
			throw new Exception('Unable to kill a process (PID=' . $pid . ')');
		}
	}

	public static function SearchPid(array $contains, int $uid = null)
	{
		$search = '';
		foreach($contains as $string)
		{
			$search .= ' | grep "' . $string . '"';
		}

		if(is_null($uid))
		{
			$uid = posix_getuid();
		}

		// Search process id
		$output = self::Run(new Command('ps -u ' . $uid . ' -o pid,cmd' . $search));

		return !preg_match('/^\s*(\d+)\s+/', $output[0], $matches) ? null : intval($matches[0]);
	}
}

?>