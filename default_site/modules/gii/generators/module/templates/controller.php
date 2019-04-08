<?php
/**
 * This is the template for generating a controller class within a module.
 */

use app\helpers\ModuleHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\module\Generator */

$isSiteSubmodule = isset($generator->parentID) && ModuleHelper::SITE === $generator->parentID;

echo "<?php\n";
?>

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: <?= date('d.m.Y H:i') . PHP_EOL ?>
 * via Gii <?= $generator->getName() . PHP_EOL ?>
 */

namespace <?= $generator->getControllerNamespace() ?>;

use yii\tools\crud\Action as BaseCrudAction;
<?php if ($isSiteSubmodule): ?>
use app\modules\site\components\Controller;
<?php else: ?>
use app\components\controllers\WebController;
<?php endif ?>

class DefaultController extends <?php if ($isSiteSubmodule): ?>Controller<?php else: ?>WebController<?php endif ?>
{
    public $modelClass = '';

   /**
    * @inheritdoc
    */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => 'yii\tools\crud\ReadAction',
                'modelPolicy' => BaseCrudAction::MODEL_POLICY_NONE,
                'beforeCallback' => function ($action) {
                    //$action->params['searchModel'] = new Search();
                    //$action->params['dataProvider'] = $action->params['searchModel']
                    //    ->search(Yii::$app->getRequest()->get());
                }
            ],
            'create' => [
                'class' => 'yii\tools\crud\CreateAction',
                'model' => $this->modelClass,
            ],
            'update' => [
                'class' => 'yii\tools\crud\UpdateAction',
                'model' => $this->modelClass,
            ],
            'delete' => [
                'class' => 'yii\tools\crud\DeleteAction',
                'model' => $this->modelClass,
            ]
        ]);
    }
}
