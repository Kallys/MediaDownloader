<?php

namespace App\Controllers;

class Sign extends Controller
{
	public function SignIn(\Base $f3, array $routes)
	{
		if(\App\Lib\SignedInUser::IsUserSignedIn())
		{
			$f3->reroute('@home');
		}

		if($f3->get('VERB') == 'POST')
		{
			$post = $f3->get('POST');

			try {
				\App\Lib\SignedInUser::SignIn($post['login'], $post['password']);
				$f3->reroute('@home');
			}
			catch(\App\Lib\Exception $e)
			{
				$f3->set('View.LoginFailed', true);
			}
		}

		echo \Template::instance()->render('login.html');
	}

	public function SignOut(\Base $f3, array $routes)
	{
		if(!\App\Lib\SignedInUser::IsUserSignedIn())
		{
			$f3->reroute('@home');
		}

		\App\Lib\SignedInUser::SignOut();
		$f3->reroute('@home');
	}
}