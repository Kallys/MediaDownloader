<?php

namespace App\Controllers;

class Home extends Controller
{
	public function Get($f3)
	{
		$f3->set('View.Version', \App\Lib\Git::GetVersion() ?: 'v1');
		echo \Template::instance()->render('home.html');
	}
}