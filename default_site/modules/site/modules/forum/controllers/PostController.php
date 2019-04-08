<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 04.05.16 17:33
 */

namespace site\modules\forum\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use app\helpers\RouteHelper;
use app\helpers\ModuleHelper;
use site\modules\forum\models\PostForm;
use site\modules\forum\models\Topic;
use admin\modules\users\helpers\RbacHelper;

//
// @todo: refactor duplicate access checking code in actions.
//
class PostController extends EntityController
{
    /**
     * @var string
     */
    public $modelClass = 'site\modules\forum\models\Post';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = array_replace_recursive(parent::behaviors(), [
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

                            return $user->can(RbacHelper::FORUM_POSTS_ADD)
                                || (($identity = $user->getIdentity()) && $identity->isAdmin);
                        },
                    ],
                    // 'update' access control implemented in action directly.
                ],
            ],
        ]);

        $behaviors['verbs']['actions']['create'] = ['post'];

        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'create' => [
                'class' => 'yii\tools\crud\CreateAction',
                'model' => $this->modelClass,
                'beforeCallback' => function ($action) {
                    $topicId = Yii::$app->getRequest()->get('topicId');
                    if (!($topic = $action->controller->finder->findTopic(['id' => $topicId]))
                        || !$topic->subforum
                        || !$topic->subforum->section
                    ) {
                        throw new NotFoundHttpException(
                            Yii::t(ModuleHelper::FORUM, "Post doesn't exists or access has been denied")
                        );
                    }
                },
                'redirectAction' => RouteHelper::SITE_FORUM_TOPICS_SHOW,
                'redirectParams' => function ($action) {
                    $params = [
                        'section' => $action->model->topic->subforum->section->slug,
                        'subforum' => $action->model->topic->subforum->slug,
                        'topic' => $action->model->topic->slug,
                        '#' => $action->model->id,
                    ];

                    if (1 < ($page = ceil($action->model->topic->getPosts()->count() / Topic::POST_PER_PAGE))) {
                        $params['page'] = $page;
                    }

                    return $params;
                },
            ],
            'update' => [
                'class' => 'yii\tools\crud\UpdateAction',
                'model' => $this->modelClass,
                'beforeCallback' => function ($action) {
                    $postId = Yii::$app->getRequest()->get('id');
                    if (!($post = $action->controller->finder->findPost(['id' => $postId]))
                        || !$post->topic
                        || !$post->topic->subforum
                        || !$post->topic->subforum->section
                    ) {
                        throw new NotFoundHttpException(
                            Yii::t(ModuleHelper::FORUM, "Post doesn't exists or access has been denied")
                        );
                    }

                    if (!$action->model->is_first) {
                        $user = Yii::$app->getUser();
                        if ($user->can(RbacHelper::FORUM_POSTS_UPDATE, ['entity' => $action->model])
                            || (($identity = $user->getIdentity()) && $identity->isAdmin)
                        ) {
                            $action->controller->configureTopicBreadcrumb($action->model->topic);
                            $action->controller->registerBreadcrumb('#' . $action->model->id);

                            return;
                        }
                    }

                    throw new ForbiddenHttpException(
                        Yii::t('yii', 'You are not allowed to perform this action.')
                    );
                },
                'beforeRenderCallback' => function ($action) {
                    $action->params['post'] = $action->model;
                    Yii::$container->set(PostForm::className(), [
                        'model' => $action->model,
                    ]);
                }
            ],
            'delete' => [
                'class' => 'yii\tools\crud\DeleteAction',
                'model' => $this->modelClass,
                'beforeCallback' => function ($action) {
                    $postId = Yii::$app->getRequest()->get('id');
                    if (!($post = $action->controller->finder->findPost(['id' => $postId]))
                        || !$post->topic
                        || !$post->topic->subforum
                        || !$post->topic->subforum->section
                    ) {
                        throw new NotFoundHttpException(
                            Yii::t(ModuleHelper::FORUM, "Post doesn't exists or access has been denied")
                        );
                    }

                    if (!$action->model->is_first) {
                        $user = Yii::$app->getUser();
                        if ($user->can(RbacHelper::FORUM_POSTS_DELETE, ['entity' => $action->model])
                            || (($identity = $user->getIdentity()) && $identity->isAdmin)
                        ) {
                            return;
                        }
                    }

                    throw new ForbiddenHttpException(
                        Yii::t('yii', 'You are not allowed to perform this action.')
                    );
                },
                'redirectAction' => RouteHelper::SITE_FORUM_TOPICS_SHOW,
                'redirectParams' => function ($action) {
                    return [
                        'section' => $action->model->topic->subforum->section->slug,
                        'subforum' => $action->model->topic->subforum->slug,
                        'topic' => $action->model->topic->slug,
                    ];
                },
            ]
        ]);
    }
}
