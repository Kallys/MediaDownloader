<?php

namespace App\Controllers;

class Link extends Controller
{
	private $media;

	public function __construct(\Base $f3, array $routes)
	{
		if(is_null($this->media = \App\Models\Media::instance()->GetById($routes['media_id'])))
		{
			$f3->error(404);
		}
	}

	public function Get(\Base $f3, array $routes)
	{
		$no_info = null;
		try {
			$this->media->GetFilename();
		}
		catch(\App\Models\Objects\Ex_NoInfo $e)
		{
			$no_info = $e->getDetail();
		}

		$f3->set('View.Media', $this->media);
		$f3->set('View.NoInfo', $no_info);

		echo \Template::instance()->render('link.html');
	}


	public function Post(\Base $f3, array $routes)
	{
		if($f3->exists('POST.format_id', $format_id))
		{
			if(!is_null($this->media->Download((int)$format_id)))
			{
				echo json_encode(true);
				return;
			}
		}
		echo json_encode(false);
	}
}