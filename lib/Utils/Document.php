<?php

namespace MediaDownloader\Utils;

class Document
{
	public $src_js = [];
	public $need_jquery = false;
	
	private static $_instance = null;
	
	private function __construct()
	{
	}
	
	public static function getInstance()
	{
		if(is_null(self::$_instance))
		{
			self::$_instance = new Document();
		}
	
		return self::$_instance;
	}
}