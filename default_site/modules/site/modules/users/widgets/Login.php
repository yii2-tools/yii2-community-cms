<?php

namespace site\modules\users\widgets;

use Yii;
use yii\base\Widget;
use site\modules\users\models\LoginForm;

class Login extends Widget
{
    /** @var bool */
    public $validate = true;

    /** @inheritdoc */
    public function run()
    {
        $model  = Yii::createObject(LoginForm::className());
        $action = $this->validate ? null : ['site/users/security/login'];

        if ($this->validate && $model->load(Yii::$app->request->post()) && $model->login()) {
            return Yii::$app->response->redirect(Yii::$app->user->returnUrl);
        }

        return $this->render('login', [
            'model'  => $model,
            'action' => $action,
        ]);
    }
}
