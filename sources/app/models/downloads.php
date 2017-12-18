<?php

namespace App\Models;

class Downloads extends Model
{
	public function __construct()
	{
		parent::__construct(\App\Models\Objects\Download::class);

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
		if(is_null($media = Media::instance()->GetById($self->get('media_id'))))
		{
			throw new \App\Lib\Exception('Unknown media');
		}

		if(is_null($media->GetFormatInfosById($self->get('format_id'))))
		{
			throw new \App\Lib\Exception('Unknown format');
		}
	}

	public function CheckUnique(\DB\Jig\Mapper $self)
	{
		if($self->findone(['@_id != ? AND @media_id = ? AND @format_id = ?', $self->get('_id'), $self->get('media_id'), $self->get('format_id')]) !== false)
		{
			throw new Ex_Duplicate($self->get('media_id') . ' & ' . $self->get('format_id'));
		}
	}

	public function GetAllByMediaId(string $media_id = null)
	{
		return $this->NewObject($this->mapper->find(['@media_id = ?', $media_id]));
	}

	public function GetAllByState(int $state)
	{
		return $this->NewObject($this->mapper->find(['@state = ?', $state]));
	}

	public function GetAllOthersByState(int $state)
	{
		return $this->NewObject($this->mapper->find(['@state != ?', $state]));
	}

	public function CountByState(int $state = null)
	{
		if(is_null($state))
		{
			$result = [];
			foreach(\Base::instance()->constants(\App\Models\Objects\Download::class, 'State_') as $state)
			{
				$result[$state] = $this->mapper->count(['@state = ?', $state]);
			}
			return $result;
		}
		else
		{
			return $this->mapper->count(['@state = ?', $state]);
		}
	}

	public function GetByFormatId(string $media_id, int $format_id)
	{
		return $this->NewObject($this->mapper->findone(array('@media_id = ? AND @format_id = ?', $media_id, $format_id)));
	}

	public function GetById(string $id)
	{
		return $this->NewObject($this->mapper->findone(array('@_id = ?', $id)));
	}

	public function New(string $media_id, int $format_id, int $process_id = 0, string $output = null, int $state = \App\Models\Objects\Download::State_Pending)
	{
		$this->mapper->reset();
		$this->mapper->media_id		= $media_id;
		$this->mapper->format_id 	= $format_id;
		$this->mapper->process_id	= $process_id;
		$this->mapper->output		= $output;
		$this->mapper->state		= $state;
		$this->mapper->save();

		return $this->NewObject($this->mapper);
	}
}