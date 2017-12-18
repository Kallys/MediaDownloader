<?php

namespace App\Models;

class Ex_InvalidURL extends \App\Lib\Exception {}

class Media extends Model
{
	public function __construct()
	{
		parent::__construct(\App\Models\Objects\Media::class);

		$this->mapper->beforeupdate(function(\DB\Jig\Mapper $self, array $pkeys) {
			self::CheckValues($self);
			self::CheckUnique($self);
		});

		$this->mapper->beforeinsert(function(\DB\Jig\Mapper $self, array $pkeys) {
			self::CheckValues($self);
			self::CheckUnique($self);
		});
	}

	public function CheckValues(\DB\Jig\Mapper $self)
	{
		// Check URL validity
		if(filter_var($self->get('url'), FILTER_VALIDATE_URL) === false)
		{
			throw new Ex_InvalidURL($self->get('url'));
		}
	}

	public function CheckUnique(\DB\Jig\Mapper $self)
	{
		if($self->findone(['@_id = ? AND @url = ?', $self->get('_id'), $self->get('url')]) !== false)
		{
			throw new Ex_Duplicate;
		}
	}

	public function GetById(string $id)
	{
		return $this->NewObject($this->mapper->findone(['@_id = ?', $id]));
	}

	public function GetByUrl(string $url)
	{
		return $this->NewObject($this->mapper->findone(['@url = ?', $url]));
	}

	public function New(string $url)
	{
		$this->mapper->reset();
		$this->mapper->url = $url;
		$this->mapper->save();

		return $this->NewObject($this->mapper);
	}
}