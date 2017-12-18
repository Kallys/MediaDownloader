<?php

namespace App\Lib;

use App\Models\Config;

class Debugger extends \Prefab
{
	private $debugger;
	private $renderer;

	public function __construct(\Base $f3 = null, float $request_start_time = null)
	{
		if(!is_null($f3) && class_exists('\DebugBar\DebugBar'))
		{
			$this->debugger = new \DebugBar\DebugBar;
			$this->debugger->addCollector(new \DebugBar\DataCollector\PhpInfoCollector);
			$this->debugger->addCollector(new \DebugBar\DataCollector\TimeDataCollector($request_start_time));
			$this->debugger->addCollector(new \DebugBar\DataCollector\MemoryCollector);
			$this->debugger->addCollector(new \DebugBar\DataCollector\ExceptionsCollector);
			$this->debugger->addCollector(new \DebugBar\DataCollector\ConfigCollector(Config::instance()->Dump()));

			$this->renderer = $this->debugger->getJavascriptRenderer($f3->get('URL_BASE') . '/' . \App\SUBDIR_PUBLIC_DEBUGBAR);
		}
	}

	public function RenderHead()
	{
		return is_null($this->renderer) ? null : $this->renderer->renderHead();
	}

	public function Render()
	{
		// Collects F3's hive juste before rendering
		!is_null($this->debugger) && $this->debugger->addCollector(new \DebugBar\DataCollector\ConfigCollector(\Base::instance()->hive(), 'F3->Hive'));

		return is_null($this->renderer) ? null : $this->renderer->render();
	}
}
