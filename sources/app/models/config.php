<?php

namespace App\Models;

use App\Lib\YoutubeDl;
use Respect\Validation\Validator;

class Config extends Model
{
	private static $validator =  null;

	public function __construct()
	{
		parent::__construct();

		$this->mapper->beforeinsert(function(\DB\Jig\Mapper $self, array $pkeys) {
			self::$validator->assert($self->cast());
		});

		Validator::with('App\\Lib\\Validations\\Rules\\');
		self::$validator = Validator::key('cache', Validator::boolType())
									->key('debug_level', Validator::intType()->between(0, 3))
									->key('default_quality', Validator::intType()->in(\Base::instance()->constants(YoutubeDl::class, 'QUALITY_')))
									->key('default_stream', Validator::intType()->in(\Base::instance()->constants(YoutubeDl::class, 'STREAM_')))
									->key('download_path', Validator::stringType()->directory()->readable()->writable())
									->key('max_concurrents', Validator::intType()->between(0))
									->key('max_simultaneous', Validator::intType()->between(0))
									->key('youtubedl_args', Validator::stringType())
									->key('youtubedl_path', Validator::stringType()->file()->readable()->executable()->YoutubeDl())
		;

		$this->mapper->load();
	}

	public function onCreate()
	{
		$this->mapper->reset();
		$this->mapper->cache			= false;
		$this->mapper->debug_level		= 3;
		$this->mapper->default_quality	= YoutubeDl::QUALITY_BEST_EVER;
		$this->mapper->default_stream	= YoutubeDl::STREAM_BOTH;
		$this->mapper->download_path	= \App\DIR_PUBLIC_DONWLOADS;
		$this->mapper->max_concurrents	= 1;
		$this->mapper->max_simultaneous	= 3;
		$this->mapper->youtubedl_args	= '';
		$this->mapper->youtubedl_path	= '/usr/local/bin/youtube-dl';
		$this->mapper->save();
	}

	// Multi-variable assignment using associative array
	public function MSet(array $values)
	{
		// Validate new values before assignment
		self::$validator->assert($values + $this->Dump());
		$this->mapper->copyfrom($values);
		$this->mapper->save();
	}

	public function __get(string $name)
	{
		return $this->__isset($name) ? $this->mapper->get($name) : null;
	}

	public function __isset(string $name)
	{
		return $this->mapper->exists($name);
	}

	public static function Get(string $name)
	{
		return self::instance()->__get($name);
	}

	public function Dump()
	{
		return $this->mapper->cast();
	}
}