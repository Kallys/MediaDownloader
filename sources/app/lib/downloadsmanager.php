<?php

namespace App\Lib;

use \App\Models\Config;
use \App\Models\Objects\Download;

class DownloadsManager
{
	// Update downloads list
	// Non-reentrant
	public static function Update()
	{
		return \Base::instance()->mutex(__METHOD__, function()
		{
			Logger::Debug('DownloadsManager::Update');

			$stopped_downloads = 0;
			$started_downloads = 0;
			$running_downloads = 0;
			$concurrents_downloads = new KeysCounter;

			// 1) Update and count all downloads in progress
			$downloads = \App\Models\Downloads::instance()->GetAllByState(Download::State_Downloading);

			foreach($downloads as $download)
			{
				// Update download state
				$download->Update();

				// Download is still in progress
				if($download->state === Download::State_Downloading)
				{
					$concurrents_downloads->Add($download->GetMedia()->extractor_key);
					$running_downloads++;
				}
				else
				{
					$stopped_downloads++;
				}
			}

			// 2) Download all pending downloads (if possible)
			if(Config::Get('max_simultaneous') === 0 || $running_downloads < Config::Get('max_simultaneous'))
			{
				$downloads = \App\Models\Downloads::instance()->GetAllByState(Download::State_Pending);

				foreach($downloads as $download)
				{
					// No more slot available
					if(Config::Get('max_simultaneous') > 0 && $running_downloads >= Config::Get('max_simultaneous'))
					{
						break;
					}

					$extractor_key = $download->GetMedia()->extractor_key;

					// Concurrent downloads limit reached
					if(Config::Get('max_concurrents') > 0 && $concurrents_downloads->Get($extractor_key) >= Config::Get('max_concurrents'))
					{
						continue;
					}

					// Start download
					if($download->Download())
					{
						$started_downloads++;
						$running_downloads++;
						$concurrents_downloads->Add($extractor_key);
					}
				}
			}

			return [
				'running'	=> $running_downloads,
				'started'	=> $started_downloads,
				'stopped'	=> $stopped_downloads
			];
		});
	}
}
