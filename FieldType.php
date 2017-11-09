<?php

namespace shintio\profile;

use yii\helpers\ArrayHelper;

/**
 * Class FieldType
 * @package shintio\profile
 */
class FieldType
{
	/**
	 * @var array
	 */
	public $type;
	/**
	 * @var string
	 */
	public $visible;

	/**
	 * @var string
	 */
	private $json;

	/**
	 * FieldType constructor.
	 * @param array $config
	 */
	public function __construct($config=[])
	{
		if(!is_array($config))
		{
			$json=json_decode($config,true);

			if(!is_array($json))
			{
				$config=['type'=>$config];
			}
			else
			{
				$this->json=$config;
			}
		}

		$this->type=ArrayHelper::getValue($config,'type','text');
		$this->visible=ArrayHelper::getValue($config,'visible','100');

		if($this->json===null)
		{
			$this->json=json_encode($config,JSON_UNESCAPED_UNICODE);
		}
	}

	/**
	 * @return FieldType with type=>text
	 */
	public static function text()
	{
		return new FieldType('text');
	}
}