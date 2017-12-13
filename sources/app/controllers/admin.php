<?php

namespace App\Controllers;

use App\Models\Config;

class Admin extends Controller
{
	public function Get(\Base $f3, array $routes)
	{
		$f3->set('View.Config', $f3->get('POST') + Config::instance()->Dump());
		echo \Template::instance()->render('admin.html');
	}

	public function Post(\Base $f3, array $routes)
	{
		if($f3->exists('POST', $post))
		{
			try {
				Config::instance()->MSet([
					'cache'				=> !empty($post['cache']),
					'debug_level'		=> intval($post['debug_level']),
					'default_quality'	=> intval($post['default_quality']),
					'default_stream'	=> intval($post['default_stream']),
					'download_path'		=> rtrim($post['download_path'], '/') . '/',
					'max_concurrents'	=> intval($post['max_concurrents']),
					'max_simultaneous'	=> intval($post['max_simultaneous']),
					'youtubedl_path'	=> $post['youtubedl_path']
				]);
			}
			catch(\Respect\Validation\Exceptions\NestedValidationException $e)
			{
				// Implementation of NestedValidationException::getMessagesIndexedByName (not yet released)
				// See: https://github.com/Respect/Validation/pull/773/commits/8b1a7e7079b9471e55ec666f077e927b9f0e8975
				$messages = [];
				$exceptions = $e->getIterator();

				foreach($exceptions as $exception)
				{
					if($exceptions[$exception]['depth'] != 1 && preg_match('/^' . $exception->getName() . '(.*)$/', $exception->getMessage(), $matches))
					{
						$messages[$exception->getName()][] = $matches[1];
					}
				}

				$f3->set('View.Validation', $messages);
				return $this->Get($f3, $routes);
			}
		}
		$f3->reroute();
	}
}