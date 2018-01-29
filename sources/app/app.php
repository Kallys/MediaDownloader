<?php

namespace App;

// Define paths (absolute)
define('DIR_BASE', realpath(__DIR__ . '/../../') . '/');

// Define public paths (relative)
const
	SUBDIR_PUBLIC_DEBUGBAR	= 'debugbar/';

const
	DIR_SRC						= DIR_BASE . 'sources/',
	DIR_APP						= DIR_SRC . 'app/',
	DIR_APP_BIN					= DIR_APP . 'bin/',
	DIR_APP_LIB					= DIR_APP . 'lib/',
	DIR_APP_DICT				= DIR_APP . 'dict/',
	DIR_APP_DICT_L10N			= DIR_APP_DICT . 'l10n/',
	DIR_APP_DICT_SETTINGS		= DIR_APP_DICT . 'settings/',
	DIR_APP_VIEWS				= DIR_APP . 'views/',
	DIR_LIB						= DIR_SRC . 'lib/',
	DIR_TEMP					= DIR_BASE . 'temp/',
	DIR_CACHE					= DIR_TEMP . 'cache/',
	DIR_RESOURCES				= DIR_BASE . 'resources/',
	DIR_CONF					= DIR_BASE . 'config/',
	DIR_TEMP_UPLOADS			= DIR_TEMP . 'uploads/',
	DIR_DATABASES				= DIR_RESOURCES . 'databases/',
	DIR_LOGS					= DIR_RESOURCES . 'logs/',
	DIR_SESSIONS				= DIR_RESOURCES . 'sessions/',
	DIR_PUBLIC					= DIR_BASE . 'public/',
	DIR_PUBLIC_CSS				= DIR_PUBLIC . 'css/',
	DIR_PUBLIC_IMG				= DIR_PUBLIC . 'img/',
	DIR_PUBLIC_JS				= DIR_PUBLIC . 'js/',
	DIR_PUBLIC_FONT				= DIR_PUBLIC . 'fonts/',
	DIR_PUBLIC_DONWLOADS		= DIR_PUBLIC . 'downloads/';

// Load composer dependencies
require DIR_LIB . 'composer/autoload.php';

use App\Models\Config;

// App can't be a \Prefab since it will instanciate \Base
class App
{
	static private $instance = null;
	private $cli = false;
	private $commando = null;
	private $f3 = null;
	private $config = null;
	private $request_start_time = 0;

	public static function instance()
	{
		if(is_null(self::$instance))
		{
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function __construct()
	{
		// Get Request Start Time
		$this->request_start_time = isset($_SERVER['REQUEST_TIME_FLOAT']) ? $_SERVER['REQUEST_TIME_FLOAT'] : microtime(true);

		if($this->cli = PHP_SAPI == 'cli')
		{
			// Command must be instantiate before FatFree (because FatFree emulates HTTP request on PHP_SAPI CLI mode)
			$this->commando = new \Commando\Command;
		}

		// Load FatFree Framework
		$this->f3 = \Base::instance();

		// Set Fat-Free paths
		$this->f3->mset([
			'AUTOLOAD'	=> DIR_BASE,
			'UI'		=> DIR_APP_VIEWS,
			'TEMP'		=> DIR_TEMP,
			'LOCALES'	=> DIR_APP_DICT_L10N,
			'LOGS'		=> DIR_LOGS,
			'UPLOADS'	=> DIR_TEMP_UPLOADS,
			// Custom
			'DIR_SETTINGS' => DIR_APP_DICT_SETTINGS
			]
		);

		// Load settings
		$this->f3->config(DIR_APP_DICT_SETTINGS . 'f3.ini', true);

		// Extend hive with URL path
		$this->f3->mset([
			'URL_BASE'				=> ($this->f3->get('SCHEME') . '://' . $this->f3->get('HOST') . (in_array($this->f3->get('PORT'), [80, 443]) ? '' : ':' . $this->f3->get('PORT')) .	$this->f3->get('BASE')),
		]);

		// Load configurations
		if(!Config::TableExists())
		{
			if($this->f3->get('PATH') !== $this->f3->alias('install'))
			{
				$this->f3->reroute('@install');
			}

			return;
		}
		$this->config = Config::instance();

		// Set Fat-Free dynamic parameters
		$this->f3->mset([
			'CACHE'		=> $this->config->cache ? 'folder=' . DIR_CACHE : false,
			'DEBUG'		=> $this->config->debug_level
			]
		);

		// Configure debug mode
		if($this->config->debug_level > 2)
		{
			assert_options(ASSERT_ACTIVE,		true);
			assert_options(ASSERT_BAIL,			true);
			assert_options(ASSERT_WARNING,		true);
			assert_options(ASSERT_QUIET_EVAL,	false);
		}
	}

	public function Run()
	{
		// Load session if any (must be done before rendering)
		\App\Lib\Session::instance();

		if($this->f3->get('PATH') !== $this->f3->alias('install'))
		{
			// Configure debug mode
			if($this->config->debug_level > 2)
			{
				// Start debugger
				\App\Lib\Debugger::instance($this->f3, $this->request_start_time);
			}

			// Authentication is required
			if($this->f3->get('PATH') !== $this->f3->alias('signin') && !\App\Lib\SignedInUser::IsUserSignedIn())
			{
				$this->f3->reroute('@signin');
			}
		}

		return $this->f3->run();
	}

	public function RunConsole()
	{
		// Define first option
		$this->commando->argument()
			->require()
			->referToAs('command')
			->describedAs('The command name');

		// Do not read access $command or command definition won't work
		$command = $this->f3->get('SERVER.argv.1');

		if($this->f3->exists('Console.Commands.' . strtolower($command), $bin_class))
		{
			return $bin_class::Run($this->commando);
		}
		else if($command != '/')
		{
			$this->commando->error(new \Exception('Unknown command'));
		}
	}
}
