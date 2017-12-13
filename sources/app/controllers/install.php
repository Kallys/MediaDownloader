<?php

namespace App\Controllers;

use App\Models\Config;
use Respect\Validation\Validator;
use App\Models\Users;

class Install extends Controller
{
	public function __construct(\Base $f3, array $routes)
	{
		if(Config::TableExists())
		{
			$f3->error(403);
		}
	}

	public function Get(\Base $f3, array $routes)
	{
		$this->CheckRequirements($f3);
		echo \Template::instance()->render('install.html');
	}

	private function CheckRequirements(\Base $f3)
	{
		$messages = [];
		Validator::with('App\\Lib\\Validations\\Rules\\');
		$values = [
			'php_version'	=> PHP_VERSION,
			'mdc'			=> DIR_BASE . 'mdc',
			'logs'			=> \App\DIR_LOGS,
			'databases'		=> \App\DIR_DATABASES,
			'sessions'		=> \App\DIR_SESSIONS,
			'temp'			=> \App\DIR_TEMP
		];

		try
		{
			Validator::key('php_version', Validator::PhpVersion('7.0.0'))
					 ->key('mdc', Validator::stringType()->file()->readable()->executable())
					 ->key('logs', Validator::stringType()->directory()->readable()->writable()->executable())
					 ->key('databases', Validator::stringType()->directory()->readable()->writable()->executable())
					 ->key('sessions', Validator::stringType()->directory()->readable()->writable()->executable())
					 ->key('temp', Validator::stringType()->directory()->readable()->writable()->executable())
				->assert($values);
		}
		catch(\Respect\Validation\Exceptions\NestedValidationException $e)
		{
			// Implementation of NestedValidationException::getMessagesIndexedByName (not yet released)
			// See: https://github.com/Respect/Validation/pull/773/commits/8b1a7e7079b9471e55ec666f077e927b9f0e8975
			$exceptions = $e->getIterator();

			foreach($exceptions as $exception)
			{
				$messages[$exception->getName()] = $exception->getMessage();
			}
		}

		$f3->set('View.Requirements', [
			'Values'		=> $values,
			'Validation'	=> $messages
		]);
		return empty($messages);
	}

	public function Post(\Base $f3, array $routes)
	{
		if($f3->exists('POST', $post))
		{
			$messages = [];

			try
			{
				Validator::key('password_confirm', Validator::stringType()->equals($post['password']))
					->assert($post);

				// Remove possible users table
				Users::Destroy();

				// Create administrator
				Users::instance()->New($post['name'], $post['password']);

				// Create configs
				Config::instance()->MSet([
					'download_path'		=> rtrim($post['download_path'], '/') . '/',
					'youtubedl_path'	=> $post['youtubedl_path']
				]);

				// Log in administrator
				\App\Lib\SignedInUser::SignIn($post['name'], $post['password']);

				// Reroute to admin
				$f3->reroute('@admin');
			}
			catch(\Respect\Validation\Exceptions\NestedValidationException $e)
			{
				// Remove config on error
				Config::Destroy();

				$messages = $e->findMessages([
					'name'				=> 'Name must contain at least one caracter',
					'password'			=> 'Password must contain at least six caracters',
					'password_confirm'	=> 'Password confirmation is different from password',
					'download_path'		=> 'Download path is not a writeable folder',
					'youtubedl_path'	=> 'Youtube-dl path is invalid'
				]);
			}
			catch(\App\Models\Ex_Duplicate $e)
			{
				$messages['name'] = 'A user with same name already exists';
			}

			$f3->set('View.Form', [
				'Values'		=> $post,
				'Validation'	=> $messages
			]);
		}

		$this->Get($f3, $routes);
	}
}

