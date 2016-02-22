<?php

namespace app\controllers;

use app\models\ExampleModel;
use app\models\ExampleUploadModel;
use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;

class UploadController extends Controller
{

	public function actions()
	{
		return [
			'error' => [
				'class' => 'yii\web\ErrorAction',
			],
		];
	}


	public function actionIndex()
	{

		$object = new ExampleModel();

		if (Yii::$app->request->isPost)
		{

			// get post data
			$object->load(Yii::$app->request->post());


			$object->file_attribute = UploadedFile::getInstance($object, 'file_attribute');

			if ($object->validate())
			{

				$object->save();

				// lets get the image and crop it with the data passed via post from the form
				// this example is, for obvious reasons, only valid for jpeg image files. 
				// This is already setted as a rule, so no sweat about it.
				$newimage = 'images/' . $object->file_attribute->baseName . '.' . $object->file_attribute->extension;

				$object->file_attribute->saveAs($newimage);

				$cropping_data = Yii::$app->request->post('file_attribute-cropping');

				$im = imagecreatefromjpeg($newimage);

				$thumb_im = imagecrop($im, $cropping_data);

				imagejpeg($thumb_im, $newimage, 100);

				return 'File cropped successfully!';
			}

		} else
		{
			return $this->render('index', ['object' => $object]);
		}
	}


}
