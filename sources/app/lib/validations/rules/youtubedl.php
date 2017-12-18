<?php

namespace App\Lib\Validations\Rules;

use Respect\Validation\Rules\AbstractRule;

class YoutubeDl extends AbstractRule
{
	public function validate($path)
	{
		$cmd = escapeshellarg($path);
		$cmd .= ' --help';

		exec($cmd, $output, $result);

		return $result == 0 && !empty($output[0]) && $output[0] == 'Usage: youtube-dl [OPTIONS] URL [URL...]';
	}
}