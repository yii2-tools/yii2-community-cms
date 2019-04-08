<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 24.04.16 9:54
 */

namespace app\modules\routing\behaviors;

use Yii;
use yii\helpers\Json;
use yii\base\Behavior;
use yii\base\ModelEvent;
use yii\base\InvalidConfigException;
use yii\behaviors\SluggableBehavior;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\widgets\ActiveForm;
use app\helpers\ModuleHelper;
use app\modules\routing\components\Router;
use app\modules\routing\models\Route;

/**
 * Class RoutableBehavior
 *
 * ATTENTION!
 * 1. Owner class should be with declared transactions() with OP_ALL flags for all scenarios.
 * 2. [refactoring needed] This behavior conflicts with blameable (updated_by will be set during $owner's INSERT event)
 *      (how to fix it see SecureBehavior preventConflicts method logic)
 *
 * @package app\modules\routing\behaviors
 */
class RoutableBehavior extends Behavior
{
    /**
     * @var ActiveRecord
     */
    public $owner;

    /**
     * @var bool
     */
    public $required = true;

    /**
     * Attribute containing by $owner.
     * @var string
     */
    public $routeAttribute = 'route_id';

    /**
     * e.g. site/pages
     * @var string
     */
    public $routeModule;

    /**
     * e.g. 'site/pages/show'
     * @var string
     */
    public $routeAction;

    /**
     * e.g. 'Page "{routeDescriptionParam}"', where:
     * {routeParam} - will be replaced by real value of $routeParam for concrete route.
     * @var string
     */
    public $routeDescription;

    /**
     * e.g. "My page title".
     * If not specified, real value of $routeParam will be used.
     * @var string
     */
    public $routeDescriptionParam;

    /**
     * If false, route params will be empty {}.
     * If string, route params will be array, where key is 'routeParam' and value
     * is real value of attribute of $owner with the same name.
     * @var string|false
     */
    public $routeParam = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (!isset($this->routeModule)) {
            throw new InvalidConfigException("Property 'routeModule' must be set");
        }

        if (!isset($this->routeAction)) {
            throw new InvalidConfigException("Property 'routeAction' must be set");
        }

        if (!isset($this->routeDescription)) {
            throw new InvalidConfigException("Property 'routeDescription' must be set");
        }

        if (!isset($this->routeDescriptionParam)) {
            $this->routeDescriptionParam = $this->routeParam;
        }
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return array_merge(parent::events(), [
            BaseActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
            BaseActiveRecord::EVENT_AFTER_INSERT => 'createRoute',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'updateRoute',
            BaseActiveRecord::EVENT_AFTER_DELETE => 'deleteRoute',
        ]);
    }

    /**
     * Renders $routeAttribute field within active form.
     *
     * Note what route_id will be string field with value considered as $routeParam (url pattern),
     * before insert/update stage value of this field will be converted
     * to the corresponding real id of the route within application instance.
     * @param ActiveForm $form
     * @return \yii\widgets\ActiveField
     */
    public function renderRouteUrlField(ActiveForm $form)
    {
        if ($route = Yii::$app->router->getById($this->owner->getAttribute($this->routeAttribute))) {
            $this->owner->setAttribute($this->routeAttribute, $route->url_pattern);
        }

        return $form->field($this->owner, $this->routeAttribute, [
            'inputOptions' => [
                'class' => 'form-control',
                'placeholder' => Yii::t('app', 'Will be generated'),
            ],
        ]);
    }

    /**
     * $owner's relation to maintainable route instance.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRoute()
    {
        return $this->owner->hasOne(Route::className(), [Route::primaryKey()[0] => $this->routeAttribute]);
    }

    /**
     * @param ModelEvent $event
     */
    public function beforeValidate(ModelEvent $event)
    {
        $urlPattern = $this->resolveUrlPattern();

        if (!$this->ensureUrlPatternValid($urlPattern) || !$this->ensureRouteUnique($urlPattern)) {
            $event->isValid = false;
        }
    }

    /**
     * @param string $urlPattern
     * @return bool
     */
    public function ensureUrlPatternValid($urlPattern)
    {
        if (!preg_match(Router::URL_PATTERN_DEFAULT, $urlPattern)) {
            $this->owner->addError(
                $this->routeAttribute,
                Yii::t('errors', 'Incorrect route value')
            );

            return false;
        }

        return true;
    }

    /**
     * @param string $urlPattern
     * @return bool
     */
    public function ensureRouteUnique($urlPattern)
    {
        if ($route = Yii::$app->router->getByUrlPattern($urlPattern)) {
            $routeId = $this->owner->getOldAttribute($this->routeAttribute);

            if (empty($routeId) || $routeId !== $route->getPrimaryKey()) {
                $this->owner->addError(
                    $this->routeAttribute,
                    Yii::t('errors', 'Route "{0}" already has been taken', [$urlPattern])
                );

                return false;
            }
        }

        return true;
    }

    /**
     * Creates route instance for $owner and establishes relation between them.
     */
    public function createRoute()
    {
        if (!$this->owner->isTransactional(ActiveRecord::OP_INSERT)) {
            throw new \LogicException("Operation INSERT should be transactional for owner");
        }

        if (($urlPattern = $this->resolveUrlPattern()) === null) {
            return;
        }

        /** @var Route $route */
        $route = Yii::createObject([
            'class' => Route::className(),
            'module' => $this->routeModule,
            'default_url_pattern' => $urlPattern,
            'url_pattern' => $urlPattern,
            'route' => $this->routeAction,
            'params' => $this->resolveRouteParams(),
            'description' => Yii::t(
                'app',
                $this->routeDescription,
                ['routeDescriptionParam' => $this->owner->getAttribute($this->routeDescriptionParam)]
            ),
        ]);

        if (!$route->save(false)) {
            throw new \Exception('Unexpected error during route creation for owner');
        }

        $this->owner->link('route', $route);
    }

    /**
     * Updates route instance for $owner.
     */
    public function updateRoute()
    {
        if (!$this->owner->isTransactional(ActiveRecord::OP_UPDATE)) {
            throw new \LogicException("Operation UPDATE should be transactional for owner");
        }

        if (($urlPattern = $this->resolveUrlPattern()) === null) {
            return;
        }

        $routeId = $this->owner->getOldAttribute($this->routeAttribute);

        /** @var Route $route */
        if ($route = Route::findOne($routeId)) {
            $route->setAttributes([
                'url_pattern' => $urlPattern,
                'params' => Json::encode([$this->routeParam => $this->owner->getAttribute($this->routeParam)]),
            ]);
            if (!$route->save(false)) {
                throw new \Exception('Unexpected error during route updating for owner');
            }

            $this->owner->setAttribute($this->routeAttribute, $routeId);
        }
    }

    /**
     * Updates route maintained for $owner.
     */
    public function deleteRoute()
    {
        if (!$this->owner->isTransactional(ActiveRecord::OP_DELETE)) {
            throw new \LogicException("Operation DELETE should be transactional for owner");
        }

        Yii::$app->router->deleteRouteById($this->owner->getAttribute($this->routeAttribute));
    }

    /**
     * Determines and returns value of urlPattern
     * used for maintenance route and routeAction's param.
     *
     * @return string|null
     * @throws \yii\base\InvalidConfigException
     */
    protected function resolveUrlPattern()
    {
        $form = Yii::$app->getRequest()->getBodyParam($this->owner->formName());

        if (!empty($form) && isset($form[$this->routeAttribute]) && !empty($form[$this->routeAttribute])) {
            return $form[$this->routeAttribute];
        }

        if (($sluggable = $this->owner->getBehavior('sluggable')) && $sluggable instanceof SluggableBehavior) {
            if ($urlPattern = $this->owner->getAttribute($this->owner->slugAttribute)) {
                return $urlPattern;
            }
        }

        if ($this->required) {
            throw new InvalidConfigException('Owner must use renderRouteField in create/update forms'
                . PHP_EOL . ' or have SluggableBehavior');
        }

        return null;
    }

    /**
     * @return string
     */
    protected function resolveRouteParams()
    {
        $params = [];

        if (false !== $this->routeParam) {
            $params[$this->routeParam] = $this->owner->getAttribute($this->routeParam);
        }

        return Json::encode($params);
    }
}
