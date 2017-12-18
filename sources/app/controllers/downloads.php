<?php

namespace App\Controllers;

use \App\Models\Objects\Download;

class Downloads extends Controller
{
	const
		Filter_all		= 'all',
		Filter_active	= 'active',
		Filter_inactive	= 'inactive';

	public function Get(\Base $f3, array $routes)
	{
		\App\Lib\DownloadsManager::Update();

		$downloads = [];
		switch($routes['filter'])
		{
			case self::Filter_all:
				$downloads = \App\Models\Downloads::instance()->GetAll();
				break;

			case self::Filter_active:
				$downloads = \App\Models\Downloads::instance()->GetAllByState(Download::State_Downloading);
				break;

			case self::Filter_inactive:
				$downloads = \App\Models\Downloads::instance()->GetAllOthersByState(Download::State_Downloading);
				break;

			default:
				$f3->error(404);
		}

		$counts = \App\Models\Downloads::instance()->CountByState();

		$f3->set('View.DownloadsCount', [
			self::Filter_all		=> array_sum($counts),
			self::Filter_active		=> $counts[Download::State_Downloading]
		]);
		$f3->set('View.DownloadsCount.' . self::Filter_inactive, $f3->get('View.DownloadsCount.' . self::Filter_all) - $f3->get('View.DownloadsCount.' . self::Filter_active));
		$f3->set('View.Downloads', $downloads);

		echo \Template::instance()->render('downloads.html');
	}

	public function Post(\Base $f3, array $routes)
	{
		if($f3->exists('POST', $post))
		{
			// Actions on selected downloads
			if(is_array($post['downloads']))
			{
				foreach($post['downloads'] as $download_id => $download_action)
				{
					if($download = \App\Models\Downloads::instance()->GetById($download_id))
					{
						try
						{
							switch($download_action)
							{
								case 'delete':
									$download->Cancel();
									break;

								case 'pause':
									$download->Pause();
									break;

								case 'retry':
								case 'resume':
									$download->Resume();
									break;

								case 'download':
									if($download->state == Download::State_Finished)
									{
										\Web::instance()->send($download->output);
									}
									break;

								default:
									\App\Lib\Alerter::Warning('Unable to perform unknown action "' . $download_action . '" on download.');
							}
						}
						catch(\App\Lib\Exception $e)
						{
							\App\Lib\Alerter::Error('Unable to perform action "' . $download_action . '" on download. (' . $e->getMessage() . ')');
						}
					}
				}
			}
			else
			{
				switch($post['action'])
				{
					case 'pause_all_downloading':
						$downloads = \App\Models\Downloads::instance()->GetAllByState(Download::State_Downloading);

						foreach($downloads as $download)
						{
							$download->Pause();
						}
						break;

					case 'remove_all_finished':
						$downloads = \App\Models\Downloads::instance()->GetAllByState(Download::State_Finished);

						foreach($downloads as $download)
						{
							$download->Cancel();
						}
						break;

					default:
						\App\Lib\Alerter::Warning('Unable to perform unknown action "' . $post['action'] . '"');
				}
			}
		}

		$f3->reroute('@downloads');
	}
}