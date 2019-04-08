<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 02.05.16 18:40
 */

namespace site\modules\forum\controllers;

use app\helpers\RouteHelper;
use yii\tools\components\Action;
use site\modules\forum\components\Controller;
use site\modules\forum\Finder;
use site\modules\forum\models\Entity;
use site\modules\forum\models\Section;
use site\modules\forum\models\Subforum;
use site\modules\forum\models\Topic;
use site\modules\users\interfaces\ObserverInterface;
use site\modules\users\Finder as UsersFinder;

abstract class EntityController extends Controller
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
     * @param Entity $entity
     */
    public function configureBreadcrumbs(Entity $entity = null)
    {
        if (empty($entity)) {
            return;
        }

        if ($this instanceof SectionController) {
            $this->configureSectionBreadcrumb($entity);
        } elseif ($this instanceof SubforumController) {
            $this->configureSubforumBreadcrumb($entity);
        } elseif ($this instanceof TopicController) {
            $this->configureTopicBreadcrumb($entity);
        }
    }

    /**
     * @param Section $section
     */
    public function configureSectionBreadcrumb(Section $section = null)
    {
        if (empty($section)) {
            return;
        }

        $breadcrumb = $this instanceof SectionController
            ? $section->title
            : [
                    'url' => [
                        RouteHelper::SITE_FORUM_SECTIONS_SHOW,
                        'section' => $section->slug
                    ],
                    'label' => $section->title
              ];

        $this->registerBreadcrumb($breadcrumb);
    }

    /**
     * @param Subforum $subforum
     */
    public function configureSubforumBreadcrumb(Subforum $subforum = null)
    {
        if (empty($subforum)) {
            return;
        }

        $this->configureSectionBreadcrumb($subforum->section);

        $breadcrumb = $this instanceof SubforumController
            ? $subforum->title
            : [
                    'url' => [
                        RouteHelper::SITE_FORUM_SUBFORUMS_SHOW,
                        'section' => $subforum->section->slug,
                        'subforum' => $subforum->slug,
                    ],
                    'label' => $subforum->title
              ];

        $this->registerBreadcrumb($breadcrumb);
    }

    /**
     * @param Topic $topic
     * @param bool $isActive
     * @param bool|string $anchor
     */
    public function configureTopicBreadcrumb(Topic $topic = null, $isActive = false, $anchor = false)
    {
        if (empty($topic)) {
            return;
        }

        $this->configureSubforumBreadcrumb($topic->subforum);

        $breadcrumb = $isActive
            ? $topic->title
            : [
                'url' => [
                    RouteHelper::SITE_FORUM_TOPICS_SHOW,
                    'section' => $topic->subforum->section->slug,
                    'subforum' => $topic->subforum->slug,
                    'topic' => $topic->slug,
                ],
                'label' => $topic->title
            ];

        if (!$isActive && is_string($anchor)) {
            $breadcrumb['url']['#'] = $anchor;
        }

        $this->registerBreadcrumb($breadcrumb);
    }

    /**
     * @param string|array $breadcrumb
     */
    public function registerBreadcrumb($breadcrumb)
    {
        $this->getView()->params['breadcrumbs'][] = $breadcrumb;
    }
}
