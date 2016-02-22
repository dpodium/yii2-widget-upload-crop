<?php

namespace app\models;

use yii\db\ActiveRecord;


/**
 * Class Examplemodel
 * @package app\models
 *
 * @property integer $id
 * @property string $name
 */
class ExampleModel extends ActiveRecord
{

	
	public $file_attribute;

	/**
	 * @return array the validation rules.
	 */
	public function rules()
	{
		return [
			[['name', 'file_attribute'], 'required'],
			[['name'], 'string'],
			[['name', 'file_attribute'], 'safe'],
			[['file_attribute'], 'file', 'extensions' => 'jpg', 'mimeTypes' => 'image/jpeg']
		];
	}
}
