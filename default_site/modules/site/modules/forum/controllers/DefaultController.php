<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.03.2016 16:50
 * via Gii Module Generator
 */

namespace site\modules\forum\controllers;

use yii\tools\crud\Action as BaseCrudAction;
use site\modules\forum\components\Controller;
use site\modules\forum\Finder;
use site\modules\users\interfaces\ObserverInterface;
use site\modules\users\Finder as UsersFinder;

class DefaultController extends Controller
{
    /**
     * @var Finder
     */
    public $finder;

    /**
     * @inheritdoc
     */
    public function __construct(
        $id,
        $module,
        Finder $finder,
        ObserverInterface $onlineObserver,
        UsersFinder $usersFinder,
        $config = []
    ) {
        $this->finder = $finder;
        parent::__construct($id, $module, $onlineObserver, $usersFinder, $config);
    }

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
                    $action->params['sections'] = $this->finder->findSection([], true, true);
                }
            ],
        ]);
    }
}
