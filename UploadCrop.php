<?php
/**
 * UploadCrop Widget
 *
 * @author Joseba Juaniz <joseba.juaniz@gmail.com>
 * @since 1.0
 */

namespace dpodium\yii2\widget\upload\crop;

use Yii;
use yii\base\InvalidParamException;
use yii\base\Widget;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

class UploadCrop extends Widget
{
	/** @var \yii\db\ActiveRecord */
	var $model;

	/** @var  string */
	var $attribute;

	/* @var ActiveForm */
	var $form = NULL;

	/** @var boolean */
	var $enableClientValidation;

	/** @var array */
	var $imageOptions;

	/** @var array */
	var $jcropOptions = array();

	/** @var integer */
	var $maxSize = 300;

	var $title = 'Crop image';

	var $changePhotoTitle = '';
	var $imageSrc;
	/**
	 * only call this method after a form closing and
	 *    when user hasn't used in the widget call the parameter $form
	 *    this adds to every form in the view the field validation.
	 *
	 * @param array $config
	 * @return string
	 * @throws \yii\base\InvalidConfigException
	 */
	static function manualValidation($config = [])
	{

		if (!array_key_exists('model', $config) || !array_key_exists('attribute', $config)) {
			throw new InvalidParamException('Config array must have a model and attribute.');
		}

		$view = Yii::$app->getView();
		$field_id = Html::getInputId($config['model'], $config['attribute']);
		$view->registerJs('$("#' . $field_id . '").urlParser("launchValidation");');
	}


	/**
	 * Renders the field.
	 */
	public function run()
	{
		if (is_null($this->imageOptions))
		{
			$this->imageOptions = [
				'alt' => 'Crop this image'
			];
		}

		$this->imageOptions['id'] = Yii::$app->getSecurity()->generateRandomString(10);

		$inputField = Html::getInputId($this->model, $this->attribute, ['data-image_id' => $this->imageOptions['id']]);

		$default_jcropOptions = [
								'dashed' => FALSE,
								'rotatable' => FALSE];


		$this->jcropOptions = array_merge($default_jcropOptions, $this->jcropOptions);


		if (is_null($this->form))
		{
			$this->form = new ActiveForm();

			if (!is_null($this->enableClientValidation))
			{
				$this->form->enableClientValidation = $this->enableClientValidation;
			}
		}

		$view = $this->getView();

		$assets = UploadCropAsset::register($view);

		echo Html::beginTag('div', ['class' => 'uploadcrop']);
		if ($this->imageSrc == '') {
 			$img = $assets->baseUrl . '/img/nophoto.png';
		} else {
			$img = $this->imageSrc;
		}
		echo $this->form->field($this->model, $this->attribute)->fileInput(['style'=>'cursor:pointer;'])->label(Html::img($img, ['width'=>200,'height'=>200]));
		echo Html::beginTag('span', ['class'=>'upload-title']);
		echo $this->changePhotoTitle;
		echo Html::endTag('span');
//			echo Html::beginTag('div', ['id' => 'preview-pane']);
//				echo Html::beginTag('div', ['class' => 'preview-container']);
//					echo Html::img('', ['class' => 'preview_image']);
//				echo Html::endTag('div');
//			echo Html::endTag('div');

		Modal::begin([
			'id' => 'cropper-modal-'. $this->imageOptions['id'],
			'header' => '<h3>'.$this->title.'</h3>',
			'closeButton' => [],
			'footer' => Html::button('Cancel', ['id' => $this->imageOptions['id'].'_button_cancel', 'class' => 'btn btn-default', 'data-dismiss' => 'modal']) . Html::button('Crop & Save', ['id' => $this->imageOptions['id'].'_button_accept', 'class' => 'btn btn-success cropper-done']),
			'size' => Modal::SIZE_LARGE,
		]);

				echo Html::beginTag('div', ['class' => 'spinner']);
					echo Html::beginTag('div', ['class' => 'bounce1']);
					echo Html::endTag('div');
					echo Html::beginTag('div', ['class' => 'bounce2']);
					echo Html::endTag('div');
					echo Html::beginTag('div', ['class' => 'bounce3']);
					echo Html::endTag('div');
				echo Html::endTag('div');
		echo Html::beginTag('div', ['id' => 'image-source']);
					echo Html::img('', $this->imageOptions);
				echo Html::endTag('div');
				echo Html::beginTag('div', ['style' => 'display: block;margin: 0 auto;text-align: center;']);
					echo Html::beginTag('button', ['title' => 'Zoom In', 'class' => 'btn btn-primary', 'type' => 'button', 'id'=>'zoom-in']);
						echo Html::beginTag('span', ['class' => 'fa fa-search-plus']);
						echo Html::endTag('span');
					echo Html::endTag('button');
					echo Html::beginTag('button', ['title' => 'Zoom Out', 'class' => 'btn btn-primary', 'type' => 'button', 'id'=>'zoom-out']);
						echo Html::beginTag('span', ['class' => 'fa fa-search-minus']);
						echo Html::endTag('span');
					echo Html::endTag('button');
				echo Html::endTag('div');


				echo html::hiddenInput('cropping[x]', '', ['id' => $inputField.'-x']);
				echo html::hiddenInput('cropping[width]','', ['id' => $inputField.'-width']);
				echo html::hiddenInput('cropping[y]', '', ['id' => $inputField.'-y']);
				echo html::hiddenInput('cropping[height]','', ['id' => $inputField.'-height']);

		Modal::end();

		echo Html::endTag('div');

		$jcropOptions = ['inputField' => $inputField, 'jcropOptions' => $this->jcropOptions];

		$jcropOptions['maxSize'] = $this->maxSize;

		$jcropOptions['formId'] = $this->form->id;

		$jcropOptions = Json::encode($jcropOptions);

		$view->registerJs('jQuery("#'.$inputField.'").uploadCrop('.$jcropOptions.');');
	}
}