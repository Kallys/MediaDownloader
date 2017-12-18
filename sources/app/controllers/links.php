<?php

namespace App\Controllers;

use App\Lib\YoutubeDl;
use App\Lib\Alerter;
use App\Models\Media;

class Links extends Controller
{
	public function Get(\Base $f3, array $routes)
	{
		echo \Template::instance()->render('links-add.html');
	}

	public function Post(\Base $f3, array $routes)
	{
		$data = $f3->get('POST');

		if($f3->exists('POST.urls', $urls))
		{
			// Index defined => we're browsing
			if($f3->exists('POST.url_index', $index))
			{
				return $this->BrowseLinks($f3, $urls, $index);
			}

			// Trim and remove duplicates from urls
			$urls = array_unique(array_filter(array_map('trim', explode("\r\n", $urls))));

			if(empty($urls))
			{
				Alerter::Error('<b>Any of the following given urls are valid:</b><br>- ' . implode('<br>- ', $urls), 'No valid link!');
			}
			else
			{
				// Need selection page
				if($data['quality'] == YoutubeDl::QUALITY_MANUAL)
				{
					return $this->BrowseLinks($f3, $urls);
				}
				else
				{
					return $this->DownloadLinks($f3, $urls, $data['quality'], $data['stream']);
				}
			}
		}

		return $this->Get($f3, $routes);
	}

	private function BrowseLinks(\Base $f3, array $urls, int $index = 0)
	{
		$url = $urls[$index];
		$no_info = null;

		try {
			if(is_null($media = Media::instance()->GetByUrl($url)))
			{
				$media = Media::instance()->New($url);
			}
			$media->GetFilename();// Force lazzy load infos
		}
		catch(\App\Models\Ex_InvalidURL $e)
		{
			$no_info = $e->getMessage();
		}
		catch(\App\Models\Objects\Ex_NoInfo $e)
		{
			$no_info = $e->getDetail();
		}

		$f3->set('View.Urls', $urls);
		$f3->set('View.UrlIndex', $index);
		$f3->set('View.Media', $media);
		$f3->set('View.NoInfo', $no_info);

		echo \Template::instance()->render('links-browse.html');
	}

	private function DownloadLinks(\Base $f3, array $urls, int $quality, int $stream)
	{
		$result = [];

		foreach($urls as $url)
		{
			$no_info = false;
			$status = 'success';
			$message = 'Added to downloads';

			try
			{
				if(is_null($media = \App\Models\Media::instance()->GetByUrl($url)))
				{
					$media = \App\Models\Media::instance()->New($url);
				}

				$media->Download($media->QueryFormat($quality, $stream));
			}
			catch(\App\Models\Ex_Duplicate $e)
			{
				$status = 'warning';
				$message = 'This format has been already downloaded.';
			}
			catch(\App\Models\Objects\Ex_NoInfo $e)
			{
				$status = 'danger';
				$no_info = true;
				$message = $e->getDetail();
			}
			catch(\App\Lib\Exception $e)
			{
				$status = 'danger';
				$message = $e->getMessage();
			}

			$result[] = [
				'media'		=> $media,
				'url'		=> $url,
				'status'	=> $status,
				'message'	=> $message,
				'no_info'	=> $no_info
			];
		}

		$f3->set('View.Result', $result);

		// Update Manager in order to start downloads
		\App\Lib\DownloadsManager::Update();

		echo \Template::instance()->render('links-download.html');
	}
}