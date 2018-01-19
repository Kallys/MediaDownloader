<?php

namespace App\Models\Objects;

use App\Lib\YoutubeDl;
use App\Lib\Process;

class Ex_UnknownState extends \App\Lib\Exception {}

class Download extends Object
{
	private $media = null;

	// State values
	const
		State_Pending		= 0,
		State_Downloading	= 1,
		State_Finished		= 2,
		State_Error			= 3,
		State_Paused		= 4;

	public function GetErrorFileContent()
	{
		if(is_readable($this->GetErrorFilePath()))
		{
			return file_get_contents($this->GetErrorFilePath());
		}

		return null;
	}

	public function GetLogFileContent()
	{
		if(is_readable($this->GetLogFilePath()))
		{
			return file_get_contents($this->GetLogFilePath());
		}

		return null;
	}

	public function GetDownloadingStats()
	{
		$result = array(
			'progression'	=> '?',
			'speed'			=> '-',
			'ETA'			=> '-'
		);

		switch($this->mapper->state)
		{
			case self::State_Finished:
				$result['progression'] = '100%';
				break;

			case self::State_Pending:
			case self::State_Downloading:
			case self::State_Paused:
			case self::State_Error:
				if(empty($this->mapper->output) || !is_file($this->mapper->output . '.part'))
				{
					$result['progression'] = '0.0%';
				}

				if(is_readable($this->GetLogFilePath()))
				{
					$logs = file($this->GetLogFilePath());

					foreach(array_reverse($logs) as $log)
					{
						if(preg_match("/^\[download\]\s+([0-9]{1,3}\.[0-9]{1,2}%) of\s+.* at\s+(.*) ETA\s+(.*)$/", $log, $matches))
						{
							$result['progression']	= $matches[1];

							if($this->mapper->state === self::State_Downloading)
							{
								$result['speed']	= $matches[2];
								$result['ETA']		= $matches[3];
							}
							break;
						}
					}
				}
				break;
		}

		return $result;
	}

	public function GetMedia()
	{
		if(is_null($this->media))
		{
			$this->media = \App\Models\Media::instance()->GetById($this->mapper->media_id);
		}

		return $this->media;
	}

	public function GetFormat()
	{
		return $this->GetMedia()->GetFormatInfosById($this->mapper->format_id);
	}

	public function GetLogFilePath()
	{
		return \App\DIR_TEMP . $this->mapper->media_id . '.' . $this->mapper->format_id . '.log';
	}

	public function GetErrorFilePath()
	{
		return \App\DIR_TEMP . $this->mapper->media_id . '.' . $this->mapper->format_id . '.err';
	}

	public function GetTempOutputFilePath()
	{
		return empty($this->mapper->output) ? null : $this->mapper->output . '.part';
	}

	private function GetMutexId()
	{
		return __CLASS__ . $this->mapper->media_id . $this->mapper->format_id;
	}

	public function GetHash()
	{
		return preg_replace("/[^[:alnum:][:space:]]/u", "", $this->mapper->_id);
	}

	public function SetState(int $state)
	{
		if(!in_array($state, \Base::instance()->constants($this, 'State_')))
		{
			throw new Ex_UnknownState;
		}

		if($state == self::State_Finished)
		{
			// Remove files
			is_file($this->GetErrorFilePath()) && unlink($this->GetErrorFilePath());
			is_file($this->GetLogFilePath()) && unlink($this->GetLogFilePath());
		}

		$this->mapper->state = $state;
		$this->mapper->save();
	}

	// Should be called only on downloading downloads
	public function Update()
	{
		if($this->mapper->state !== self::State_Downloading)
		{
			return;
		}

		// Parse output file name if not already done
		if(empty($this->mapper->output) && is_readable($this->GetLogFilePath()))
		{
			$logs = file($this->GetLogFilePath());

			// Prevent potential conflicts
			if(preg_match('/^\[download\]\s+(.*)\s+has already been downloaded and merged$/', $logs[count($logs) - 1], $matches))
			{
				$this->mapper->output = $matches[1];
				$this->mapper->save();
			}
			// In case of merging, read backward and search for merging log line
			else if(preg_match('/(\d+)\+(\d+)/', $this->mapper->format_id, $format_matches))
			{
				foreach(array_reverse($logs) as $log)
				{
					if(preg_match('/^\[ffmpeg\]\s+Merging formats into\s+"(.*)"$/', $log, $matches))
					{
						$this->mapper->output = $matches[1];
						$this->mapper->save();
						break;
					}
				}
			}
			else
			{
				foreach($logs as $log)
				{
					if(preg_match('/^\[download\]\s+Destination:\s+(.*)$/', $log, $matches))
					{
						$this->mapper->output = $matches[1];
						$this->mapper->save();
						break;
					}
				}
			}
		}

		// Check if process is still running (process id is the same as current running process when downloading process calls on success callback, ie it finished)
		if(!empty($this->mapper->output) && (!Process::IsRunning($this->mapper->process_id) || Process::AmI($this->mapper->process_id)))
		{
			$this->SetState(is_file($this->mapper->output) ? self::State_Finished : self::State_Error);
			$this->mapper->save();
		}
	}

	public function Download()
	{
		return \Base::instance()->mutex($this->GetMutexId(), function($download, $cmd)
		{
			if(in_array($download->mapper->state, [self::State_Downloading, self::State_Finished]))
			{
				return true;
			}

			$process_id = YoutubeDl::Download($this);
			$download->mapper->process_id = $process_id;
			$download->mapper->state = self::State_Downloading;

			return $download->mapper->save();
		}, [$this, $cmd]);
	}

	public function Resume()
	{
		return \Base::instance()->mutex($this->GetMutexId(), function($download)
		{
			if(!in_array($download->mapper->state, [self::State_Paused, self::State_Error]))
			{
				return false;
			}

			$download->mapper->process_id = 0;
			$download->mapper->state = self::State_Pending;
			return $download->mapper->save();
		}, $this);
	}

	public function Pause()
	{
		return \Base::instance()->mutex($this->GetMutexId(), function($download)
		{
			if(in_array($download->mapper->state, [self::State_Paused, self::State_Error, self::State_Finished]))
			{
				return true;
			}

			if(Process::IsRunning($download->mapper->process_id))
			{
				Process::Kill($download->mapper->process_id);
				$download->mapper->process_id = 0;
			}

			$download->mapper->state = self::State_Paused;
			$download->mapper->save();
		}, $this);
	}

	public function Cancel()
	{
		return \Base::instance()->mutex($this->GetMutexId(), function($download)
		{
			if(Process::IsRunning($download->mapper->process_id))
			{
				Process::Kill($download->mapper->process_id);
			}

			// Remove files
			is_file($download->GetErrorFilePath()) && unlink($download->GetErrorFilePath());
			is_file($download->GetLogFilePath()) && unlink($download->GetLogFilePath());

			if(!empty($download->mapper->output))
			{
				is_file($download->mapper->output) && unlink($download->mapper->output);
				is_file($download->GetTempOutputFilePath()) && unlink($download->GetTempOutputFilePath());
			}

			// Remove download from DB
			return $download->mapper->erase();
		}, $this);
	}
}
