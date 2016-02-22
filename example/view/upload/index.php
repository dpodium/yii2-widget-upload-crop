<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

	/* @var $object \app\models\ExampleModel */
	
	$form = ActiveForm::begin([
		'options' => ['enctype' => 'multipart/form-data'] // important
	]);
	
	
		echo $form->field($object, 'name')->textInput();
		
		// adds the upload crop widget
		echo \dpodium\yii2\widget\upload\crop\UploadCrop::widget(['form' => $form, 'model' => $object, 'attribute' => 'file_attribute']);
	
	echo Html::submitButton($object->isNewRecord ? 'Upload' : 'Update', [
			'class' => $object->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
	);
	
	ActiveForm::end();

