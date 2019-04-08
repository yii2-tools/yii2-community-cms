<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 08.02.2016 14:19
 * via Gii Module Generator
 */

namespace admin\modules\setup;

use Yii;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\base\ActionEvent;
use app\modules\admin\components\Module as BaseModule;
use yii\tools\params\models\ActiveParam;
use yii\tools\crud\Action as BaseCrudAction;
use admin\modules\users\models\RoleForm;

class Module extends BaseModule implements BootstrapInterface
{
    public function bootstrap($app)
    {
        parent::bootstrap($app);

        Yii::$container->setSingleton(Finder::className(), []);

        Event::on(BaseCrudAction::className(), BaseCrudAction::EVENT_AFTER_CRUD_ACTION, [$this, 'afterCrudAction']);
    }

    public function afterCrudAction(ActionEvent $event)
    {
        if ($event->action->type === 'update' && $event->action->model instanceof RoleForm) {
            $params = ActiveParam::find()
                ->names(['default_role', 'guest_role'])
                ->value($event->action->model->getOldAttribute('name'))
                ->all();

            foreach ($params as $param) {
                $param->value = $event->action->model->name;
                $param->save(false);
            }
        }
    }

    public function sidebar()
    {
        return [];
    }
}
