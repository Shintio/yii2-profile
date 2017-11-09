<?php

namespace shintio\profile;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class Profile.
 * @package shintio\profile
 */
class Profile
{
	/**
	 * Base model with additional profile table.
	 * @var ActiveRecord
	 */
	private $model;
	/**
	 * Name of table of base model.
	 * @var string
	 */
	public $modelName='profile';

	/**
	 * Profile of base model.
	 * @var Field[]
	 */
	private $profile;

	/**
	 * Indexing of profile array.
	 * @var string
	 * @values 'code','name','id'
	 */
	private $profileIndexing='code';

	/**
	 * Static function for find some/one profiles.
	 * Search by model and additional fields.
	 * @inheritdoc
	 * @return ProfileSearch
	 */
	public static function find($modelName)
	{
		$modelProfileName=$modelName.'Profile';
		$modelProfileFieldName=$modelName.'ProfileField';

		$modelFields=$modelProfileFieldName::find()->asArray()->all();

		$query=new ProfileSearch($modelName,$modelFields);

		$tableName=$modelName::tableName();
		$tableProfileName=$modelProfileName::tableName();

		$query->leftJoin($tableProfileName,$tableProfileName.'.'.$tableName.'_id'.'='.$tableName.'.id');

		return $query;
	}

	/**
	 * Profile constructor.
	 * Class name for create new model.
	 * ActiveRecord for use existing model.
	 * Array for config.
	 * @param array $config
	 */
	public function __construct($config=[])
	{
		if(is_string($config))
		{
			$config=['model'=>new $config()];
		}
		else
		{
			if(is_object($config))
			{
				$config=['model'=>$config];
			}

			$this->validateModel($config['model']);
		}

		$this->model=ArrayHelper::getValue($config,'model');
		$this->setProfileIndexing($config['profileIndexing']);
		$this->modelName=$config['model']::className();

		$generateProfile=ArrayHelper::getValue($config,'generateProfile',true);
		if($generateProfile)
		{
			$this->generateProfile();
		}
	}

	/**
	 * Set base model.
	 * @param ActiveRecord $model
	 * @param bool $generateProfile
	 */
	public function setModel($model,$generateProfile=true)
	{
		$this->model=$model;

		if($generateProfile)
		{
			$this->generateProfile();
		}
	}

	/**
	 * Get base model.
	 * @return ActiveRecord
	 */
	public function getModel()
	{
		return $this->model;
	}

	/**
	 * Get profile in array of values(true) or array of Field's(none/false).
	 * @param bool $assoc
	 * @return array|Field[]
	 */
	public function getProfile($assoc=false)
	{
		if($this->profile===null)
		{
			$this->generateProfile();
		}

		return $assoc?ArrayHelper::toArray($this->profile):$this->profile;
	}

	/**
	 * Get profile in array of Field's.
	 * @return array
	 */
	public function getProfileInArray()
	{
		return $this->getProfile(true);
	}

	/**
	 * Set $value of $field by $profileIndexing.
	 * $value will be validated
	 * @param string $field
	 * @param mixed $value
	 * @return bool if applied
	 */
	public function setField($field,$value)
	{
		$result=true;

		if($this->profile[$field]!==null)
		{
			$this->profile[$field]->setValue($value);
		}
		else
		{
			$this->model->setAttribute($field,$value);
		}

		return $result;
	}

	/**
	 * Get field value by $profileIndexing.
	 * @param string $field
	 * @param bool $valueOnly
	 * @return Field|mixed
	 */
	public function getField($field,$valueOnly=true)
	{
		return $valueOnly?$this->profile[$field]->getValue():$this->profile[$field];
	}

	/**
	 * Get field value by $parameter.
	 * Not recommended. getField() faster. Uses additional ArrayHelper::index().
	 * @param string $parameter 'code','name','id'
	 * @param string $field
	 * @param bool $valueOnly
	 * @return Field|mixed
	 */
	public function getFieldByParameter($parameter,$field,$valueOnly=true)
	{
		if($parameter==='code'||$parameter==='name'||$parameter==='id')
		{
			/**
			 * @var $field Field
			 */
			$field=ArrayHelper::index($this->profile,$parameter)[$field];
			return $valueOnly?$field->getValue():$field;
		}
	}

	/**
	 * Set $profileIndexing.
	 * @param string $parameter 'code','name','id'
	 */
	public function setProfileIndexing($parameter)
	{
		if($parameter==='code'||$parameter==='name'||$parameter==='id')
		{
			$this->profileIndexing=$parameter;
		}
	}

	/**
	 * Get $profileIndexing.
	 * @return string
	 */
	public function getProfileIndexing()
	{
		return $this->profileIndexing;
	}

	/**
	 * Save base model and additional fields.
	 * @return bool
	 */
	public function save()
	{
		$result=$this->model->save();

		$tableName=$this->model->tableName();
		$modelProfileName=$this->modelName.'Profile';

		foreach($this->profile as $field)
		{
			$modelProfile=$modelProfileName::findOne([
				'field_id'=>$field->id,
				$tableName.'_id'=>$this->model->id
			]);
			if($modelProfile===null)
			{
				$modelProfile=new $modelProfileName();
				$modelProfile->field_id=$field->id;
				$modelProfile->setAttribute($tableName.'_id',$this->model->id);
			}

			$modelProfile->value=$field->getValue();

			$modelProfile->save();
		}

		return $result;
	}

	/**
	 * Generate array of Field's for base model.
	 * @return Field[]
	 */
	private function generateProfile()
	{
		/**
		 * @var $profile Field[]
		 */
		$profile=[];

		$tableName=$this->model->tableName();
		$tableProfileName=$tableName.'_profile';
		$tableProfileFieldName=$tableName.'_profile_field';

		$fields=(new Query())->from($tableProfileName)->where([$tableName.'_id'=>$this->model->id])->all();
		$profileFields=(new Query())->from($tableProfileFieldName)->all();

		foreach($profileFields as $field)
		{
			//$field->type=json_decode($field->type,true);
			$profile[$field['id']]=new Field($field);
		}

		foreach($fields as $field)
		{
			$profile[$field['field_id']]->setValue($field['value']);
		}

		$profile=array_values($profile);

		$model=[];
		foreach(ArrayHelper::toArray($this->model) as $key=>$item)
		{
			$model[]=new Field([
				'id'=>0,
				'code'=>$key,
				'name'=>$key,
				'type'=>[
					'type'=>'base',
					'visible'=>'100'
				],
				'value'=>$item
			]);
		}

		$this->profile=ArrayHelper::merge($model,$profile);

		$this->profile=ArrayHelper::index($this->profile,$this->profileIndexing);

		return $this->profile;
	}

	/**
	 * Validate $model.
	 * @param mixed $model
	 * @throws ModelException
	 */
	private function validateModel($model)
	{
		if($model===null)
		{
			throw new ModelException('101');
		}

		if(get_parent_class($model)!==ActiveRecord::class)
		{
			throw new ModelException('102');
		}
	}
}
