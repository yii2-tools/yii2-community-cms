<?php

namespace admin\modules\users\widgets;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use admin\modules\users\components\DbManager;
use admin\modules\users\models\Assignment;

/**
 * This widget may be used in user update form and provides ability to assign
 * multiple auth items to the user.
 */
class Assignments extends Widget
{
    /** @var integer ID of the user to whom auth items will be assigned. */
    public $userId;
    
    /** @var DbManager */
    protected $manager;
    
    /** @inheritdoc */
    public function init()
    {
        parent::init();
        $this->manager = Yii::$app->authManager;
        if ($this->userId === null) {
            throw new InvalidConfigException('You should set ' . __CLASS__ . '::$userId');
        }
    }
    
    /** @inheritdoc */
    public function run()
    {
        $model = Yii::createObject([
            'class'   => Assignment::className(),
            'user_id' => $this->userId,
        ]);
        
        if ($model->load(\Yii::$app->request->post())) {
            $model->updateAssignments();
        }
        
        return $this->render('form', [
            'model' => $model,
        ]);
    }
}
