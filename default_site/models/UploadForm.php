<?php

namespace app\models;

use Yii;
use yii\helpers\BaseFileHelper;
use yii\helpers\VarDumper;
use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $imageFile;

    public $segments = [];
    public $imageName = '';
    public $uploadedUrl = '';

    const DIR_UPLOADS = 'assets/uploads';
    const DIR_IMAGES = 'images';

    public function rules()
    {
        return [
            [
                ['imageFile'], 'image',
                'skipOnEmpty' => false,
                'extensions' => 'png, jpg, jpeg, gif',
                'minWidth' => 100, 'maxWidth' => 400,
                'minHeight' => 100, 'maxHeight' => 400,
                'maxSize' => 100 * 1024,
            ],
        ];
    }

    public function addSegments($segments = [])
    {
        Yii::trace('Upload form segments: ' . VarDumper::dumpAsString($segments), __METHOD__);
        if (! is_array($segments)) {
            $segments = (array)$segments;
        }
        $this->segments = array_merge($this->segments, $segments);
        return $this;
    }

    public function uploadedUrl()
    {
        return \Yii::getAlias('@web') . $this->uploadedUrl;
    }

    public function upload()
    {
        if ($this->validate()) {
            $imageDir = \Yii::getAlias('@webroot');
            $this->uploadedUrl = implode(DIRECTORY_SEPARATOR, ['', self::DIR_UPLOADS, self::DIR_IMAGES, '']);
            foreach ($this->segments as $segment) {
                $this->uploadedUrl .= $segment . DIRECTORY_SEPARATOR;
            }
            BaseFileHelper::createDirectory($imageDir . $this->uploadedUrl, 0775, true);
            $this->uploadedUrl .= md5($this->imageName . time()) . '.' . $this->imageFile->extension;
            $this->imageFile->saveAs($imageDir . $this->uploadedUrl);
            return true;
        } else {
            Yii::warning('Upload form validation failed: ' . VarDumper::dumpAsString($this->getErrors()), __METHOD__);
            return false;
        }
    }
}
