<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.03.2016 16:50
 * via Gii Module Generator
 */

namespace site\modules\forum;

use Yii;
use yii\base\Event;
use yii\filters\AccessControl;
use app\helpers\RouteHelper;
use app\modules\site\components\Module as BaseModule;
use site\modules\forum\components\Observer;
use site\modules\forum\components\Counter;
use site\modules\forum\models\Topic;
use site\modules\forum\models\Post;
use admin\modules\users\helpers\RbacHelper;

class Module extends BaseModule
{
    public $moduleLayout = '/layouts/main';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        if ($this->breadcrumbs) {
            $behaviors['breadcrumbs']['routeCreator'] = function ($behavior) {
                return RouteHelper::SITE_FORUM;
            };
        }

        return array_merge($behaviors, [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['?', '@'],
                        'matchCallback' => function () {
                            $user = Yii::$app->getUser();

                            return $user->can(RbacHelper::FORUM_ACCESS)
                                || (($identity = $user->getIdentity()) && $identity->isAdmin);
                        },
                    ]
                ],
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $observer = $this->getObserver();
        Event::on(Topic::className(), Topic::EVENT_AFTER_INSERT, [$observer, 'afterTopicInsert']);
        Event::on(Topic::className(), Topic::EVENT_BEFORE_DELETE, [$observer, 'beforeTopicDelete']);
        Event::on(Topic::className(), Topic::EVENT_AFTER_DELETE, [$observer, 'afterTopicDelete']);
        Event::on(Post::className(), Post::EVENT_AFTER_INSERT, [$observer, 'afterPostInsert']);
        Event::on(Post::className(), Post::EVENT_AFTER_DELETE, [$observer, 'afterPostDelete']);
    }

    /**
     * Returns observer component which tracks events for forum entities.
     * (e.g. after post has been deleted, decrement posts count for related topic, subforum)
     *
     * @return Observer
     */
    public function getObserver()
    {
        return $this->get('observer');
    }

    /**
     * Perform counting views of topics (and another dynamic statistic data).
     * @return Counter
     */
    public function getCounter()
    {
        return $this->get('counter');
    }
}
