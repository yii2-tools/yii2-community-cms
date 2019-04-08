<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 01.05.16 13:41
 */

namespace site\modules\forum\components;

use Yii;
use app\helpers\ModuleHelper;
use app\modules\site\components\Controller as BaseController;
use site\modules\forum\assets\ForumAsset;
use site\modules\users\interfaces\ObserverInterface;
use site\modules\users\Finder as UsersFinder;
use site\modules\users\Module as UsersModule;

class Controller extends BaseController
{
    /**
     * @var ObserverInterface
     */
    public $onlineObserver;

    /**
     * @var UsersModule
     */
    public $usersModule;

    /**
     * @var UsersFinder
     */
    public $usersFinder;

    /**
     * @inheritdoc
     */
    public function __construct(
        $id,
        $module,
        ObserverInterface $onlineObserver,
        UsersFinder $usersFinder,
        $config = []
    ) {
        $this->onlineObserver = $onlineObserver;
        $this->usersFinder = $usersFinder;
        parent::__construct($id, $module, $config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->usersModule = Yii::$app->getModule(ModuleHelper::USERS);
    }

    /**
     * @inheritdoc
     */
    public function render($view, $params = [])
    {
        ForumAsset::register($this->getView());

        return parent::render($view, array_merge($params, $this->resolveGlobalParams()));
    }

    /**
     * @return array
     */
    protected function resolveGlobalParams()
    {
        $identities = $this->usersFinder->findUsersByActivity(time() - 60 * 10);
        $identitiesCount = count($identities);
        $onlineCount = $this->onlineObserver->getOnlineCount();

        return [
            'stats' => [
                'users' => $this->usersModule->params['users_count'],
                'topics' => $this->module->params['forum_topics_count'],
                'posts' => max(
                    0,
                    $this->module->params['forum_posts_count'] - $this->module->params['forum_topics_count']
                ),
                'username' => $this->usersModule->params['last_user'],
            ],
            'online' => [
                'guests_num' => max(0, $onlineCount - $identitiesCount),
                'users_num' => $identitiesCount,
                'identities' => $identities,
            ],
        ];
    }
}
