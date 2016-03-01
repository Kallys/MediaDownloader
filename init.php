<?php
namespace MediaDownloader;

//define('DEBUG', true);
if(defined('DEBUG'))
{
	error_reporting(E_ALL);
	ini_set("display_errors", 1);
}

define('DIR_BASE', __DIR__);
define('DIR_LIB', DIR_BASE . '/lib/');
define('DIR_CONF', DIR_BASE . '/config/');

// PSR-4 Autoload
spl_autoload_register(function ($class)
{
    // project-specific namespace prefix
    $prefix = 'MediaDownloader\\';

    // base directory for the namespace prefix
    $base_dir = __DIR__ . '/lib/';

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_class = substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

// Last chance manual autoloader
spl_autoload_register(function ($class)
{
	$manual = array(
		'MediaDownloader\StreamEnum' => __DIR__ . '/lib/Format.php',
		'MediaDownloader\QualityEnum' => __DIR__ . '/lib/Format.php',
	);
	
	if(!empty($manual[$class]))
		require $manual[$class];
});

if(Utils\Config::GetInstance()->GetRequestPage() != 'login.php')
{
	if(!Utils\Session::getInstance()->is_logged_in())
	{
		header("Location: login.php");
		exit(0);
	}
	
	try
	{
		Utils\Config::GetInstance()->Check();
	}
	catch(\Exception $e)
	{
		$requestPage = Utils\Config::GetInstance()->GetRequestPage();
		if(!empty($requestPage) && $requestPage !== 'index.php')
		{
			header("Location: index.php");
			exit(0);
		}
		
		Utils\Error::getInstance()->Error($e->getMessage());
	}
}

?>