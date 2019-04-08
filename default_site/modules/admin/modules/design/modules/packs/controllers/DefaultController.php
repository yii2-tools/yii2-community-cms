<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 04.04.16 13:21
 */

namespace admin\modules\design\modules\packs\controllers;

use Yii;
use yii\helpers\FileHelper;
use yii\web\Response;
use app\helpers\RouteHelper;
use app\helpers\ArchiveHelper;
use app\modules\admin\components\Controller;
use site\modules\design\helpers\ModuleHelper;
use design\modules\packs\Finder;
use design\modules\packs\models\DesignPack;

class DefaultController extends Controller
{
    /** @var Finder */
    public $finder;

    /**
     * @inheritdoc
     */
    public function __construct($id, $module, Finder $finder, $config = [])
    {
        $this->finder = $finder;
        parent::__construct($id, $module, $config);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_replace_recursive(parent::behaviors(), [
            'verbs' => [
                'actions' => [
                    'import'  => ['post'],
                    'export'  => ['post'],
                ],
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => 'yii\tools\components\Action',
                'beforeCallback' => function ($action) {
                    $action->response = Yii::$app->getResponse()->redirect([RouteHelper::ADMIN_DESIGN]);
                },
            ],
        ]);
    }

    public function actionImport()
    {
        $model = Yii::createObject('admin\modules\design\modules\packs\models\UploadForm');

        if (!Yii::$app->request->isPost) {
            return $this->redirect([RouteHelper::ADMIN_DESIGN_PACKS]);
        }

        if ($result = $model->upload()) {
            Yii::$app->getSession()->setFlash(
                'success',
                Yii::t(ModuleHelper::ADMIN_DESIGN, 'Design pack successfully uploaded')
            );

            return $this->redirect([RouteHelper::ADMIN_DESIGN_PACKS]);
        }

        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;
        $response->data = [
            'status' => $result,
            'errors' => $model->getErrors(),
            'data' => []
        ];

        Yii::$app->end();
    }

    public function actionExport()
    {
        $designPack = $this->receiveDesignPack($response);
        if (!empty($response)) {
            return $response;
        }

        $filepath = ($tmpDir = $designPack->getTmpDir()) . DIRECTORY_SEPARATOR . $designPack->name . '.zip';

        if (file_exists($filepath)) {
            unlink($filepath);
        }

        FileHelper::createDirectory($tmpDir);
        ArchiveHelper::create($designPack->getSourceDir(), $filepath);

        return Yii::$app->getResponse()->sendFile($filepath);
    }

    public function actionDelete()
    {
        $designPack = $this->receiveDesignPack($response);
        if (!empty($response)) {
            return $response;
        }

        if (Yii::$app->getModule(ModuleHelper::DESIGN_PACKS)->params['design_pack'] === $designPack->name) {
            Yii::$app->getSession()->setFlash(
                'error',
                Yii::t(ModuleHelper::ADMIN_DESIGN, "Active design pack can't be deleted")
            );

            return $this->redirect([RouteHelper::ADMIN_DESIGN_PACKS]);
        }

        $transaction = DesignPack::getDb()->beginTransaction();
        $sourceDir = $designPack->getSourceDir();
        $tmpDir = $designPack->getTmpDir();
        $designPack->backup();

        try {
            $designPack->delete();
            FileHelper::removeDirectory($sourceDir);
            $transaction->commit();

            try {
                FileHelper::removeDirectory($tmpDir);
                Yii::$app->getSession()->setFlash(
                    'success',
                    Yii::t(ModuleHelper::ADMIN_DESIGN, 'Design pack successfully deleted')
                );
            } catch (\Exception $e) {
                Yii::error($e, __METHOD__);
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            $designPack->backupRestore();

            Yii::error($e, __METHOD__);
            Yii::$app->getSession()->setFlash('error', Yii::t('errors', 'Engine error'));
        }

        return $this->redirect([RouteHelper::ADMIN_DESIGN_PACKS]);
    }

    /**
     * @param $response
     * @return Response
     */
    protected function receiveDesignPack(&$response = null)
    {
        $name = Yii::$app->getRequest()->getBodyParam('name');

        /** @var DesignPack $designPack */
        $designPack = $this->finder->findDesignPack(['=', 'name', $name]);

        if (!$designPack) {
            Yii::$app->getSession()->setFlash(
                'error',
                Yii::t(ModuleHelper::ADMIN_DESIGN, "Design pack doesn't exists")
            );

            $response = $this->redirect([RouteHelper::ADMIN_DESIGN_PACKS]);

            return null;
        }

        return $designPack;
    }
}
