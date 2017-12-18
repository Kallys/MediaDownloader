<?php

namespace App\Bin;

class Ex_UnknownDownload extends Ex_Bin {}

abstract class DownloadsManager extends Bin
{
	public static function Execute(\Commando\Command $commando)
	{
		$commando->argument()
			->require()
			->referToAs('download')
			->describedAs('Select a download using its id');

		$commando->option('s')
			->aka('state')
			->describedAs('Set state of selected download');

		$commando->option('i')
			->aka('do_not_update')
			->boolean()
			->describedAs('Set state of selected download');

		if(is_null($download = \App\Models\Downloads::instance()->GetById($commando[1])))
		{
			throw new Ex_UnknownDownload;
		}

		if($commando->hasOption('state'))
		{
			$download->SetState(intval($commando['state']));
		}

		if(!$commando['do_not_update'])
		{
			\App\Lib\DownloadsManager::Update();
		}
	}
}