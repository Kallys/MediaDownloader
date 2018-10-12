<?php

use \App\Lib\YoutubeDl;

return array(
	'DownloadState'	=> array(
		\App\Models\Objects\Download::State_Pending		=> 'Pending',
		\App\Models\Objects\Download::State_Downloading	=> 'Downloading',
		\App\Models\Objects\Download::State_Finished	=> 'Finished',
		\App\Models\Objects\Download::State_Error		=> 'Error',
		\App\Models\Objects\Download::State_Paused		=> 'Paused',
	),
	'Error'			=> 'Error',
	'LoginFailed'	=> 'Invalid login/password combination',
	'Login'			=> 'Login',
	'Password'		=> 'Password',
	'SignIn'		=> 'Sign in',
	'FormatQuality'	=> [
		YoutubeDl::QUALITY_BEST			=> 'Best',
		YoutubeDl::QUALITY_BEST_EVER	=> 'Best ever',
		YoutubeDl::QUALITY_WORST		=> 'Worst',
		YoutubeDl::QUALITY_MANUAL		=> 'Manual'
	],
	'FormatStream'	=> [
		YoutubeDl::STREAM_BOTH	=> 'Audio and Video',
		YoutubeDl::STREAM_AUDIO	=> 'Audio only',
		YoutubeDl::STREAM_VIDEO	=> 'Video only'
	],
	'DownloadFilters'	=> [
		\App\Controllers\Downloads::Filter_all		=> 'All',
		\App\Controllers\Downloads::Filter_active	=> 'Active only',
		\App\Controllers\Downloads::Filter_inactive	=> 'Inactive only'
	],
    'AddUser'               => 'Add User',
    'Username'              => 'Username',
    'PasswordConfirmation' => 'Password confirmation',
);