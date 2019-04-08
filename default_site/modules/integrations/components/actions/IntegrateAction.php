<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 20.02.16 16:17
 */

namespace app\modules\integrations\components\actions;

use Yii;
use yii\base\NotSupportedException;
use yii\helpers\VarDumper;
use app\helpers\ModuleHelper;
use yii\tools\components\Action as BaseAction;
use app\modules\integrations\exceptions\IntegrationException;

/**
 * Class IntegrateAction
 * @package app\modules\integrations\actions
 */
class IntegrateAction extends BaseAction
{
    /**
     * @var string
     */
    public $vendor;

    /**
     * @var string
     */
    public $operation;

    /**
     * @var string|array
     */
    public $redirect;

    /**
     * @var string|array
     */
    public $redirectSuccess;

    /**
     * @var string|array
     */
    public $redirectError;

    /**
     * @var array
     */
    public $config = [];

    /**
     * @inheritdoc
     * @throws IntegrationException
     */
    public function init()
    {
        if (!isset($this->vendor)) {
            throw new IntegrationException('Integration vendor must be defined');
        }

        if (!isset($this->operation)) {
            $this->operation = $this->id;
        }

        if (empty($this->config)) {
            $this->config = array_merge($_GET, $_POST);
        }

        if (isset($this->redirect)) {
            $this->redirectError = $this->redirect;
            $this->redirectSuccess = $this->redirect;
        }

        parent::init();
    }

    /**
     * @inheritdoc
     * @throws IntegrationException
     */
    protected function runInternal()
    {
        if (!in_array(YII_ENV, [YII_ENV_TEST, YII_ENV_PROD])) {
            throw new NotSupportedException('Integration operations supported only in test and prod environment');
        }

        $module = Yii::$app->getModule(ModuleHelper::INTEGRATIONS . '/' . $this->vendor);

        if (!$module instanceof \app\modules\integrations\interfaces\IntegrationModuleInterface) {
            throw new IntegrationException('Integration module must implement IntegrationModuleInterface');
        }

        try {
            $integrator = $module->configurate($this->operation, $this->config);
            if (!$integrator->integrate()) {
                if ($integrator->hasErrors()) {
                    Yii::error(VarDumper::dumpAsString($integrator->getErrors()), __METHOD__);
                }
            }
            $result = $integrator->getResult();
        } catch (\Exception $e) {
            Yii::error($e->__toString(), __METHOD__);
            Yii::$app->getSession()->setFlash('danger', Yii::t('errors', 'Internal error'));
        }

        if (isset($result)) {
            Yii::$app->getSession()->setFlash($result[0], $result[1]);
            if ($result[0] === 'success' && isset($this->redirectSuccess)) {
                return $this->controller->redirect($this->redirectSuccess);
            } elseif ($result[0] === 'danger' && isset($this->redirectError)) {
                return $this->controller->redirect($this->redirectError);
            }
        }

        return $this->controller->goBack();
    }
}
