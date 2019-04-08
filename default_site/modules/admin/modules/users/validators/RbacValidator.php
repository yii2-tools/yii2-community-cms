<?php

namespace admin\modules\users\validators;

use Yii;
use yii\validators\Validator;
use app\helpers\ModuleHelper;

/**
 * Class RbacValidator
 * @package admin\modules\users\validators
 */
class RbacValidator extends Validator
{
    /** @var \admin\modules\users\components\DbManager */
    protected $manager;
    
    /** @inheritdoc */
    public function init()
    {
        parent::init();
        $this->manager = \Yii::$app->authManager;
    }
    
    /** @inheritdoc */
    protected function validateValue($value)
    {
        if (!is_array($value)) {
            return [Yii::t(ModuleHelper::ADMIN_USERS, 'Invalid value'), []];
        }
        
        foreach ($value as $val) {
            if ($this->manager->getItem($val) == null) {
                return [Yii::t(ModuleHelper::ADMIN_USERS, 'There is neither role nor permission with name "{0}"', [$val]), []];
            }
        }
    }
}
