<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 02.05.16 12:26
 */

namespace site\modules\forum\controllers;

use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use app\helpers\RouteHelper;
use app\helpers\ModuleHelper;
use yii\tools\crud\Action as BaseCrudAction;
use site\modules\forum\models\TopicForm;
use site\modules\forum\models\Topic;
use site\modules\forum\models\PostForm;
use site\modules\forum\models\Post;
use admin\modules\users\helpers\RbacHelper;

//
// @todo: remove this nested hell :)
//
class TopicController extends EntityController
{
    /**
     * @var string
     */
    public $modelClass = 'site\modules\forum\models\Topic';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['create'],
                'rules' => [
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                        'matchCallback' => function () {
                            $user = Yii::$app->getUser();

                            return $user->can(RbacHelper::FORUM_TOPICS_ADD)
                                || (($identity = $user->getIdentity()) && $identity->isAdmin);
                        },
                    ],
                    // 'update' access control implemented in action directly.
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
            'show' => [
                'class' => 'yii\tools\crud\ReadAction',
                'modelPolicy' => BaseCrudAction::MODEL_POLICY_NONE,
                'beforeCallback' => function ($action) {
                    $sectionSlug = Yii::$app->getRequest()->getQueryParam('section');

                    if ($section = $this->finder->findSection(['slug' => $sectionSlug])) {
                        $subforumSlug = Yii::$app->getRequest()->getQueryParam('subforum');

                        if ($subforum = $this->finder->findSubforum(['slug' => $subforumSlug])) {
                            $slug = Yii::$app->getRequest()->getQueryParam('topic');

                            if ($action->params['topic'] = $this->finder->findTopic(['slug' => $slug])) {
                                Yii::$container->set(PostForm::className(), [
                                    'model' => Yii::createObject([
                                        'class' => Post::className(),
                                        'topic_id' => $action->params['topic']->id
                                    ]),
                                ]);
                                $action->controller->configureTopicBreadcrumb($action->params['topic'], true);

                                $totalCount = $action->params['topic']->getPosts()->count();
                                Yii::$container->set(Pagination::className(), [
                                    'totalCount' => $totalCount,
                                    'defaultPageSize' => Topic::POST_PER_PAGE,
                                ]);

                                if ($this->module->getCounter()->increment(
                                    Topic::className() . $action->params['topic']->id
                                )) {
                                    ++$action->params['topic']->views_num;
                                    $action->params['topic']->save(false);
                                }

                                return;
                            }
                        }
                    }

                    if (!empty($subforum)) {
                        $action->controller->configureSubforumBreadcrumb($subforum);
                    } elseif (!empty($section)) {
                        $action->controller->configureSectionBreadcrumb($section);
                    }

                    $action->controller->registerBreadcrumb(
                        Yii::t(ModuleHelper::FORUM, "Topic doesn't exists or access has been denied")
                    );
                }
            ],
            'create' => [
                'class' => 'yii\tools\crud\CreateAction',
                'model' => $this->modelClass,
                'beforeCallback' => function ($action) {
                    $subforumId = Yii::$app->getRequest()->get('subforum_id');
                    if (!($subforum = $action->controller->finder->findSubforum(['id' => $subforumId]))
                        || !$subforum->section
                    ) {
                        throw new NotFoundHttpException(
                            Yii::t(ModuleHelper::FORUM, "Topic doesn't exists or access has been denied")
                        );
                    }

                    Yii::$container->set(TopicForm::className(), [
                        'model' => Yii::createObject(['class' => $this->modelClass, 'subforum_id' => $subforumId])
                    ]);

                    $action->controller->configureSubforumBreadcrumb($subforum);
                    $action->controller->registerBreadcrumb(Yii::t(ModuleHelper::FORUM, 'New topic'));
                },
                'redirectAction' => 'show',
                'redirectParams' => function ($action) {
                    return [
                        'section' => $action->model->subforum->section->slug,
                        'subforum' => $action->model->subforum->slug,
                        'topic' => $action->model->slug,
                    ];
                },
            ],
            'update' => [
                'class' => 'yii\tools\crud\UpdateAction',
                'model' => $this->modelClass,
                'beforeCallback' => function ($action) {
                    $topicId = Yii::$app->getRequest()->get('id');
                    if (!($topic = $action->controller->finder->findTopic(['id' => $topicId]))
                        || !$topic->subforum
                        || !$topic->subforum->section
                    ) {
                        throw new NotFoundHttpException(
                            Yii::t(ModuleHelper::FORUM, "Topic doesn't exists or access has been denied")
                        );
                    }

                    $user = Yii::$app->getUser();
                    if (!$user->can(RbacHelper::FORUM_TOPICS_UPDATE, ['entity' => $action->model])) {
                        if (!($identity = $user->getIdentity()) || !$identity->isAdmin) {
                            throw new ForbiddenHttpException(
                                Yii::t('yii', 'You are not allowed to perform this action.')
                            );
                        }
                    }
                    $action->controller->configureTopicBreadcrumb($action->model);
                    $action->controller->registerBreadcrumb(Yii::t(ModuleHelper::FORUM, 'Edit topic'));
                },
                'beforeRenderCallback' => function ($action) {
                    Yii::$container->set(TopicForm::className(), [
                        'model' => $action->model,
                    ]);
                }
            ],
            'delete' => [
                'class' => 'yii\tools\crud\DeleteAction',
                'model' => $this->modelClass,
                'beforeCallback' => function ($action) {
                    $topicId = Yii::$app->getRequest()->get('id');
                    if (!($topic = $action->controller->finder->findTopic(['id' => $topicId]))
                        || !$topic->subforum
                        || !$topic->subforum->section
                    ) {
                        throw new NotFoundHttpException(
                            Yii::t(ModuleHelper::FORUM, "Topic doesn't exists or access has been denied")
                        );
                    }

                    $user = Yii::$app->getUser();
                    if (!$user->can(RbacHelper::FORUM_TOPICS_DELETE, ['entity' => $action->model])) {
                        if (!($identity = $user->getIdentity()) || !$identity->isAdmin) {
                            throw new ForbiddenHttpException(
                                Yii::t('yii', 'You are not allowed to perform this action.')
                            );
                        }
                    }
                },
                'redirectAction' => RouteHelper::SITE_FORUM_SUBFORUMS_SHOW,
                'redirectParams' => function ($action) {
                    return [
                        'section' => $action->model->subforum->section->slug,
                        'subforum' => $action->model->subforum->slug,
                    ];
                },
                'redirectErrorAction' => RouteHelper::SITE_FORUM_TOPICS_SHOW,
                'redirectErrorParams' => function ($action) {
                    return [
                        'section' => $action->model->subforum->section->slug,
                        'subforum' => $action->model->subforum->slug,
                        'topic' => $action->model->slug,
                    ];
                },
            ]
        ]);
    }
}
