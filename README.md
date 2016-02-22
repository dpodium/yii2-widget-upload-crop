# Yii2 Upload Crop Widget
==========
Yii2 widget that enhance file input with crop and zoom features.

Based on package cyneek/yii2-widget-upload-crop by Joseba Juániz.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require dpodium/yii2-widget-upload-crop "*"
```

or add

```
"dpodium/yii2-widget-upload-crop": "*"
```

to the require section of your `composer.json` file.


Usage
_____
This widget is ready with all required scripts in it's assets, just call the widget to render it:

	echo \dpodium\yii2\widget\upload\crop\UploadCrop::widget(['form' => $form, 'model' => $model, 'attribute' => 'fieldName']);


## Widget method options

* model (string) (Mandatory)
> Defines the model that will be used to make the form input field. You may use DynamicForm for it.


* attribute (string) (Mandatory)
> Defines the model attribute that will be used to make de form input field. If using dynamicform, just create a temporary attribute, eg. 'image'.


* form (ActiveForm) (optional)
> Its the ActiveForm object that defines the form in which the widget it's included. It will be used to inherit the form config when making the input field.


* enableClientValidation (boolean) (optional)
> Used when the form option it's not defined. It establishes if it's or isn't activated in the widget input fields the Yii2 javaScript client validation.


* imageOptions (array) (optional)
> List with options that will be added to the image field that will be used to define the crop data in the modal. The format should be ['option' => 'value'].

* jcropOptions (array) (optional)
> List with options that will be added in javaScript while creating the crop object. For more information about which options can be added you can [read this web](https://github.com/fengyuanchen/cropper#options).

* maxSize (integer) (optional)
> Being 300 by default, it's the attribute that defines the max-height and max-width that will be applied to the preview image that it's shown after selecting a crop zone.


## Recovering form image and crop data

The form will send to the server the data this way:

* Image file: it must be assigned to the model attribute itself in the usual way. [For example](http://stackoverflow.com/questions/23592125/how-to-upload-a-file-to-directory-in-yii2?answertab=active#tab-top)

* Cropping values: they will be sent to Yii 2 in array form:


		["cropping"]=>
		  array(4) {
			["x"]=>
				string(1) "12"
			["width"]=>
				string(3) "400"
			["y"]=>
				string(1) "0"
			["height"]=>
				string(3) "297"
		  }

## Example
```php
...
	use yii\base\DynamicModel;
	use yii\web\UploadedFile;
	use yii\imagine\Image;
	use Imagine\Image\Box;
...
	$uploadParam = 'avatar';
	$maxSize = 2097152;
	$extensions = 'jpeg, jpg, png, gif';
	$width = 200;
	$height = 200;
	if (Yii::$app->request->isPost) {
		$model = new DynamicModel([$uploadParam]);
		$model->load(Yii::$app->request->post());
		$model->{$uploadParam} = UploadedFile::getInstance($model, $uploadParam);
		$model->addRule($uploadParam, 'image', [
			'maxSize' => $maxSize,
			'extensions' => explode(', ', $extensions),
		])->validate();

		if ($model->hasErrors()) {
			Yii::$app->session->setFlash("warning", $model->getFirstError($uploadParam));
		} else {
			$name = Yii::$app->user->id . '.png';
			$cropInfo = Yii::$app->request->post('cropping');

			try {
				$image = Image::crop(
					$model->{$uploadParam}->tempName,
					intval($cropInfo['width']),
					intval($cropInfo['height']),
					[$cropInfo['x'], $cropInfo['y']]
				)->resize(
					new Box($width, $height)
				);
			} catch (\Exception $e) {
				Yii::$app->session->setFlash("warning", $e->getMessage());
			}

			//upload and save db
			$path = 'images/'.$name;
			if (isset($image) && $image->save($path)) {
				...
				//store your image info to db here
				...
				Yii::$app->session->setFlash("success", Yii::t('alert', 'Avatar upload success.'));
			} else {
				Yii::$app->session->setFlash("warning", Yii::t('alert', 'Avatar upload failed.'));
			}
		}
		return $this->redirect(['account/index']);
	} else {
		throw new BadRequestHttpException(Yii::t('cropper', 'ONLY_POST_REQUEST'));
	}
```
