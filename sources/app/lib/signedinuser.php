<?php

namespace App\Lib;

abstract class SignedInUser
{
	private static $session_key = 'SignedInUser';

	public static function SignIn(string $login, string $password)
	{
		// Signout current logged user if any
		self::IsUserSignedIn() && self::SignOut();

		if(is_null($user = \App\Models\Users::instance()->GetByName($login)))
		{
			throw new Exception('Unable to sign in: user "'.$login.'" not found.');
		}

		if(!password_verify($password, $user->password))
		{
			throw new Exception('Unable to sign in: wrong password for user "'.$login.'".');
		}

		Session::instance()->Set(self::$session_key, $user->_id);
		Session::instance()->Set('is_admin', $user->is_admin);
	}

	public static function SignOut()
	{
		if(self::IsUserSignedIn())
		{
			Session::instance()->Destroy();
		}
	}

	public static function IsUserSignedIn()
	{
		return Session::instance()->Exists(self::$session_key);
	}

	public static function IsAdmin()
	{
        return  Session::instance()->Get('is_admin');
	}

	public static function getSignedUserId()
	{
		return Session::instance()->Get(self::$session_key);
	}
}

?>
