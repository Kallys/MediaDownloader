<?php

namespace App\Models;

use Respect\Validation\Validator;

class Users extends Model
{
	private static $validator =  null;
	public static $IS_ADMIN = 1;

	public function __construct()
	{
		parent::__construct(\App\Models\Objects\User::class);

		$this->mapper->beforeupdate(function(\DB\Jig\Mapper $self, array $pkeys) {
			self::CheckValues($self);
			self::CheckUnique($self);
		});

		$this->mapper->beforeinsert(function(\DB\Jig\Mapper $self, array $pkeys) {
			self::CheckValues($self);
			self::CheckUnique($self);
		});

		self::$validator = Validator::key('name', Validator::stringType()->length(1))
			->key('password', Validator::stringType()->length(6))
		;
	}

	public function CheckValues(\DB\Jig\Mapper $self)
	{
		self::$validator->assert($self->cast());

		$self->set('password', password_hash($self->get('password'), PASSWORD_DEFAULT));
	}

	public function CheckUnique(\DB\Jig\Mapper $self)
	{
		if($self->findone(['@name = ?', $self->get('name')]) !== false)
		{
			throw new Ex_Duplicate($self->get('name'));
		}
	}

	public function GetByName(string $name)
	{
		return $this->NewObject($this->mapper->findone(['@name = ?', $name]));
	}

	public function GetById(string $id)
    {
        return $this->NewObject($this->mapper->findone(['@_id = ?', $id]));
    }

	public function New(string $name, string $password, int $is_admin = 0)
	{
		$this->mapper->reset();
		$this->mapper->name			= $name;
		$this->mapper->password 	= $password;
		$this->mapper->is_admin 	= $is_admin;
		$this->mapper->save();

		return $this->NewObject($this->mapper);
	}

	public function DelByName(string $name)
    {
        if(!$this->mapper->findone(['@name = ?', $name])) {
            throw new Ex_InvalidData();
        }
        return $this->mapper->erase(['@name = ?', $name]);
    }
}