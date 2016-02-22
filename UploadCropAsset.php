<?php
/**
 * Asset bundle for uploadCrop Widget
 *
 * @author Joseba Juaniz <joseba.juaniz@gmail.com>
 * @since 1.0
 */

namespace dpodium\yii2\widget\upload\crop;

use yii\web\AssetBundle;

class UploadCropAsset extends AssetBundle
{

	public $depends = [
		'yii\web\JqueryAsset'
	];

	public function init()
	{
		$this->sourcePath = __DIR__ . '/assets';

		$this->js[] = (YII_DEBUG ? 'js/uploadcrop.js' : 'js/uploadcrop.min.js');
		$this->js[] = (YII_DEBUG ? 'js/cropper.js' : 'js/cropper.min.js');

		$this->css[] = (YII_DEBUG ? 'css/cropper.css' : 'css/cropper.min.css');
		$this->css[] = (YII_DEBUG ? 'css/uploadcrop.css' : 'css/uploadcrop.min.css');
		$this->css[] = 'css/range.css';

		parent::init();
	}
}