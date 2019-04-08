<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.03.2016 16:40
 * via Gii Module Generator
 */

namespace admin\modules\forum;

use Yii;
use yii\base\BootstrapInterface;
use yii\base\Event;
use app\helpers\ModuleHelper;
use app\helpers\RouteHelper;
use app\modules\admin\components\Module as BaseModule;
use site\modules\forum\models\Section;
use site\modules\forum\models\Subforum;
use admin\modules\forum\components\Observer;

/**
 * Relation queries for entities of this module (sections, subforums, topics, posts) are SECURE BY DEFAULT
 * You need to use code like below to achive system (non-user) speed operations:
 *
 * ...(secure)\ActiveRecord::secure(false);
 * // Code using forum entities relations here...
 * ...(secure)\ActiveRecord::secure(true);
 *
 * See more examples in forum components directory (e.g. Observer).
 *
 * @package admin\modules\forum
 */
class Module extends BaseModule implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $observer = $this->getObserver();
        Event::on(Section::className(), Section::EVENT_BEFORE_DELETE, [$observer, 'beforeSectionDelete']);
        Event::on(Subforum::className(), Subforum::EVENT_BEFORE_DELETE, [$observer, 'beforeSubforumDelete']);
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $sections = [
            'label' => Yii::t(ModuleHelper::FORUM, 'Sections'),
            'description' => Yii::t(ModuleHelper::ADMIN_FORUM, 'Sections management'),
            'url' => [RouteHelper::ADMIN_FORUM],
            'right' => [
                'label' => $this->siteModule->params['forum_sections_count']
            ]
        ];

        if (in_array('/' . Yii::$app->requestedRoute, [RouteHelper::ADMIN_FORUM_SECTIONS_UPDATE])) {
            $sections['active'] = true;
        }

        $subforums = [
            'label' => Yii::t(ModuleHelper::FORUM, 'Subforums'),
            'description' => Yii::t(ModuleHelper::ADMIN_FORUM, 'Subforums management'),
            'url' => [RouteHelper::ADMIN_FORUM_SUBFORUMS],
            'right' => [
                'label' => $this->siteModule->params['forum_subforums_count']
            ]
        ];

        if (in_array('/' . Yii::$app->requestedRoute, [RouteHelper::ADMIN_FORUM_SUBFORUMS_UPDATE])) {
            $subforums['active'] = true;
        }

        return [
            $sections,
            $subforums,
            [
                'label' => Yii::t('app', 'Create'),
                'url' => false,
                'items' => [
                    [
                        'label' => Yii::t(ModuleHelper::FORUM, 'Section'),
                        'url' => [RouteHelper::ADMIN_FORUM_SECTIONS_CREATE],
                        'icon' => 'plus',
                    ],
                    [
                        'label' => Yii::t(ModuleHelper::FORUM, 'Subforum'),
                        'url' => [RouteHelper::ADMIN_FORUM_SUBFORUMS_CREATE],
                        'icon' => 'plus',
                    ]
                ],
                'active' => Yii::$app->controller->module instanceof self
                    && Yii::$app->controller->action->id === 'create',
            ],

            // @todo topics and posts migration (2.0.2)

//            [
//                'label' => Yii::t('app', 'Migrate'),
//                'url' => false,
//                'items' => [
//                    [
//                        'label' => Yii::t(ModuleHelper::FORUM, 'Topic'),
//                        'url' => '#',
//                        'icon' => 'exchange',
//                    ],
//                    [
//                        'label' => Yii::t(ModuleHelper::FORUM, 'Post'),
//                        'url' => '#',
//                        'icon' => 'exchange',
//                    ]
//                ],
//                'active' => Yii::$app->controller->action->id === 'create',
//            ]
        ];
    }

    /**
     * Returns observer component which tracks events for forum entities (admin env).
     * (e.g. after section has been deleted, delete all related subforums)
     *
     * @return Observer
     */
    public function getObserver()
    {
        return $this->get('observer');
    }
}
