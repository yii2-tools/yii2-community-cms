<?php

namespace site\modules\users\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use app\helpers\ModuleHelper;
use app\helpers\RouteHelper;
use yii\tools\traits\AjaxValidationTrait;
use app\modules\site\components\Controller;
use site\modules\design\interfaces\ContextInterface;
use site\modules\users\Finder;
use site\modules\users\models\RecoveryForm;
use site\modules\users\models\Token;

class RecoveryController extends Controller
{
    use AjaxValidationTrait;

    /** @var Finder */
    protected $finder;

    /**
     * @param string           $id
     * @param \yii\base\Module $module
     * @param Finder           $finder
     * @param array            $config
     */
    public function __construct($id, $module, ContextInterface $designContext, Finder $finder, $config = [])
    {
        $this->finder = $finder;
        parent::__construct($id, $module, $designContext, $config);
    }

    /** @inheritdoc */
    public function behaviors()
    {
        return array_replace_recursive(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    ['allow' => true, 'actions' => ['request', 'reset'], 'roles' => ['?']],
                ],
            ],
        ]);
    }

    /**
     * Shows page where user can request password recovery.
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionRequest()
    {
        if (!$this->module->enablePasswordRecovery) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        /** @var RecoveryForm $model */
        $model = Yii::createObject([
            'class'    => RecoveryForm::className(),
            'scenario' => 'request',
        ]);

        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->end(0, $this->performAjaxValidation($model));
            }
            $result = $model->sendRecoveryMessage();
            Yii::$container->set(RecoveryForm::className(), $model);
            if ($result) {
                return $this->redirect([RouteHelper::SITE_USERS_LOGIN]);
            }
        }

        return $this->render('request');
    }

    /**
     * Displays page where user can reset password.
     *
     * @param int    $id
     * @param string $code
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionReset($key)
    {
        if (!$this->module->enablePasswordRecovery) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        parse_str(base64_decode($key));
        Yii::info('Token data received and parsed.' . PHP_EOL . 'id=' . $id . ', code=' . $code, __METHOD__);

        /** @var Token $token */
        $token = $this->finder->findToken(['user_id' => $id, 'code' => $code, 'type' => Token::TYPE_RECOVERY])->one();

        if ($token === null || $token->isExpired || $token->user === null) {
            Yii::$app->session->setFlash(
                'danger',
                Yii::t(ModuleHelper::USERS, 'Recovery link is invalid or expired. Please try requesting a new one.')
            );
            return $this->redirect([RouteHelper::SITE_USERS_RECOVERY_REQUEST]);
        }

        /** @var RecoveryForm $model */
        $model = Yii::createObject([
            'class'    => RecoveryForm::className(),
            'scenario' => 'reset',
        ]);

        if ($model->load(Yii::$app->getRequest()->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->end(0, $this->performAjaxValidation($model));
            }
            $result = $model->resetPassword($token);
            Yii::$container->set(RecoveryForm::className(), $model);
            if ($result) {
                return $this->redirect([RouteHelper::SITE_USERS_LOGIN]);
            }
        }

        return $this->render('reset');
    }
}
