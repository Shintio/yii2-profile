<?php

namespace shintio\profile;

use \yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * Class ProfileSearch
 * @package shintio\profile
 */
class ProfileSearch extends ActiveQuery
{
	/**
	 * @var Field[]
	 */
	private $profileFields;
	/**
	 * @var string
	 */
	private $tableName;
	/**
	 * @var string
	 */
	private $tableProfileName;

	/**
	 * ProfileSearch constructor.
	 * @param string $modelClass
	 * @param array $profileFields
	 * @param array $config
	 */
	public function __construct($modelClass,array $profileFields=[],array $config=[])
	{
		$this->profileFields=ArrayHelper::index($profileFields,'code');

		$this->tableName=$modelClass::tableName();
		$this->tableProfileName=$this->tableName.'_profile';

		parent::__construct($modelClass,$config);
	}

	/**
	 * @inheritdoc
	 * @return ProfileSearch
	 */
	public function where($condition,$params=[])
	{
		$condition=ArrayHelper::merge(['AND'],$this->normalizeCondition($condition));

		return parent::where($condition,$params);
	}

	/**
	 * @inheritdoc
	 * @return ProfileSearch
	 */
	public function andWhere($condition,$params=[])
	{
		$condition=ArrayHelper::merge(['AND'],$this->normalizeCondition($condition));

		return parent::andWhere($condition,$params);
	}

	/**
	 * @inheritdoc
	 * @return ProfileSearch
	 */
	public function orWhere($condition,$params=[])
	{
		$condition=ArrayHelper::merge(['AND'],$this->normalizeCondition($condition));

		return parent::orWhere($condition,$params);
	}

	/**
	 * @inheritdoc
	 * @return Profile[]
	 */
	public function all($db=null)
	{
		$models=parent::all($db);

		foreach($models as &$model)
		{
			$model=new Profile(['model'=>$model]);
		}

		return $models;
	}

	/**
	 * @inheritdoc
	 * @return Profile
	 */
	public function one($db=null)
	{
		return new Profile(['model'=>parent::one($db)]);
	}

	/**
	 * @param mixed $condition
	 * @param array $params
	 * @return array
	 */
	private function normalizeCondition($condition,$params=[])
	{
		$like=mb_strtoupper($condition[0])==='LIKE';
		$escape=$condition[3];

		$oldCondition=$like?[$condition[1]=>$condition[2]]:$condition;
		$condition=[];
		foreach($oldCondition as $key=>$item)
		{
			$fieldId=$this->profileFields[$key]['id'];

			if($fieldId!==null)
			{
				// TODO: Проверка типа поля

				$condition[]=$this->getCustomFieldFilter($fieldId,$item,$like,$escape);
			}
			else
			{
				$condition[]=$this->getSelfFieldFilter($key,$item,$like,$escape);
			}
		}

		return $condition;
	}

	/**
	 * @param string $key
	 * @param string $value
	 * @param bool $like
	 * @param bool $escape
	 * @return array
	 */
	private function getSelfFieldFilter($key,$value,$like=false,$escape=true)
	{
		if($like)
		{
			return [
				'LIKE',
				$this->tableName.'.'.$key,
				$value,
				$escape
			];
		}
		else
		{
			return [
				$this->tableName.'.'.$key=>$value
			];
		}
	}

	/**
	 * @param string $fieldId
	 * @param string $value
	 * @param bool $like
	 * @param bool $escape
	 * @return array
	 */
	private function getCustomFieldFilter($fieldId,$value,$like=false,$escape=true)
	{
		if($like)
		{
			return [
				'AND',
				[$this->tableProfileName.'.field_id'=>$fieldId],
				[
					'LIKE',
					$this->tableProfileName.'.value',
					$value,
					$escape
				]
			];
		}
		else
		{
			return [
				$this->tableProfileName.'.field_id'=>$fieldId,
				$this->tableProfileName.'.value'=>$value
			];
		}
	}
}