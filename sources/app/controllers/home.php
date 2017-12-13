<?php

namespace App\Controllers;

class Home extends Controller
{
	public function Get($f3)
	{
		echo \Template::instance()->render('home.html');
	}
}