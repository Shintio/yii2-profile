<?php

namespace shintio\profile;

/**
 * Class Exception
 * @package shintio\profile
 */
class Exception extends \Exception
{
	/**
	 * Error list for Profile.
	 * @var array
	 */
	protected $errors=[
		'101'=>'No model.',
		'102'=>'Model is not child of ActiveRecord',
	];

	/**
	 * Exception constructor.
	 *
	 * @param null|string $message
	 * @param int $code
	 */
	public function __construct($code=0,$message=null)
	{
		if(isset($this->errors[$code]))
		{
			$message=$this->errors[$code];
		}

		parent::__construct($message,$code);
	}
}
