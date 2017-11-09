<?php

namespace shintio\profile;

use yii\helpers\ArrayHelper;

/**
 * Class Field
 * @package shintio\profile
 */
class Field
{
	/**
	 * @var int
	 */
	public $id;
	/**
	 * @var string
	 */
	public $code;
	/**
	 * @var string
	 */
	public $name;
	/**
	 * @var FieldType
	 */
	public $type;

	/**
	 * @var mixed
	 */
	private $value;

	/**
	 * Field constructor.
	 * @param array $config
	 */
	public function __construct(array $config=[])
	{
		$this->id=ArrayHelper::getValue($config,'id',0);
		$this->code=ArrayHelper::getValue($config,'code','field');
		$this->name=ArrayHelper::getValue($config,'name','field');
		$this->value=$config['value'];

		if(gettype($config['type'])==='object')
		{
			$this->type=$config['type'];
		}
		else
		{
			if(gettype($config['type'])==='string'||gettype($config['type'])==='array')
			{
				$this->type=new FieldType($config['type']);
			}
		}
	}

	/**
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @param mixed $value
	 */
	public function setValue($value)
	{
		// TODO: validate $value

		$this->value=$value;
	}
}