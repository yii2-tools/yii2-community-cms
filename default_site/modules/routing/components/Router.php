<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 25.01.16 23:42
 */

namespace app\modules\routing\components;

use Yii;
use yii\base\Component;
use yii\caching\DbDependency;
use app\modules\routing\models\Route;

/**
 * Class Router
 * Note: this router deals only with custom (dynamic) routes from database.
 *
 * @package app\modules\routing\components
 * @since 2.0.0
 */
class Router extends Component
{
    const URL_PATTERN_DEFAULT = '/^\w+[\/\-\w]+$/ui';
    const URL_PATTERN_PARAMETRIZED = '/<(.*):(.*)>/';

    /**
     * Returns associative array of routes with collision support.
     * Example:
     *
     * ```
     * [
     *     '/some/route/1' => Route object,
     *     '/some/route/2' => [Route object, Route object]
     * ]
     * ```
     *
     * @var Route[] indexedBy route (string)
     */
    private $routes;

    /**
     * Local cache of custom url rules.
     * @var Array
     */
    private $urlRules;

    /**
     * @return Array|mixed
     */
    public function getUrlRules()
    {
        Yii::trace('Retreiving custom URL rules', __METHOD__);

        if (!isset($this->urlRules)) {
            $routes = $this->getRoutes();
            $this->urlRules = [];

            foreach ($routes as $customRoute) {
                $collisionRoutes = is_array($customRoute) ? $customRoute : [$customRoute];
                foreach ($collisionRoutes as $route) {
                    $this->urlRules[$route->url_pattern] = $route->route_pattern ?: $route->route;
                }
            }
        }

        return $this->urlRules;
    }

    /**
     * Returns route object for current request.
     *
     * @return Route|null
     */
    public function getCurrentRoute()
    {
        return $this->getRoute(Yii::$app->requestedRoute);
    }

    /**
     * Returns all custom routes registered within application.
     *
     * ATTENTION:
     * 1. For 1 route can be many records with same route value but different params.
     *      (e.g. for pages show route)
     *    Some array records can be NESTED!
     *
     * @return Route[]
     */
    public function getRoutes()
    {
        if (!isset($this->routes)) {
            $routes = Route::getDb()->cache(function () {
                return Route::find()->orderBy(['id' => SORT_ASC])->all();
            }, null, new DbDependency(['sql' => Route::CACHE_DEPENDENCY, 'reusable' => true]));

            $this->routes = $this->createCollisionIndex($routes);
        }

        return $this->routes;
    }

    /**
     * Returns array of already complete routes which don't require any params injecting.
     *
     * @return array
     */
    public function getCompleteRoutes()
    {
        $routes = $this->getRoutes();
        $result = [];

        foreach ($routes as $route) {
            $collisionRoutes = is_array($route) ? $route : [$route];
            foreach ($collisionRoutes as $collisionRoute) {
                if ($collisionRoute->route_pattern
                    || preg_match(static::URL_PATTERN_PARAMETRIZED, $collisionRoute->url_pattern)
                ) {
                    continue;
                }

                $result[] = $collisionRoute;
            }
        }

        return $result;
    }

    /**
     * @param string $route
     * @param string $urlPattern null means current path info
     * @return Route|null
     */
    public function getRoute($route, $urlPattern = null)
    {
        $routes = $this->getRoutes();
        $normalizedKey = '/' !== $route[0] ? '/' . $route : $route;

        if (!isset($routes[$normalizedKey])) {
            return null;
        }
        $routeRecord = $routes[$normalizedKey];
        if (!is_array($routeRecord)) {
            return $routeRecord;
        }
        if (!isset($urlPattern)) {
            $urlPattern = Yii::$app->getRequest()->getPathInfo();
        }

        return isset($routeRecord[$urlPattern]) ? $routeRecord[$urlPattern] : null;
    }

    /**
     * @param string $urlPattern
     * @return Route|null
     */
    public function getByUrlPattern($urlPattern)
    {
        return $this->getByKey($this->getRoutes(), 'url_pattern', $urlPattern);
    }

    /**
     * @param int $id
     * @return Route|null
     */
    public function getById($id)
    {
        return $this->getByKey($this->getRoutes(), 'id', $id);
    }

    /**
     * @param int $id
     * @return bool|false|int
     */
    public function deleteRouteById($id)
    {
        if ($route = $this->getById($id)) {
            return $route->delete();
        }

        return false;
    }

    /**
     * @param array $array
     * @param $key
     * @param $value
     * @return null
     */
    private function getByKey(array $array, $key, $value)
    {
        foreach ($array as $record) {
            if (is_array($record) && ($result = $this->getByKey($record, $key, $value))) {
                return $result;
            }
            if (isset($record->$key) && $record->$key === $value) {
                return $record;
            }
        }

        return null;
    }

    /**
     * @param array $routes
     * @param string $indexBy
     * @param string $collisionIndexBy
     * @return array
     */
    private function createCollisionIndex(array $routes, $indexBy = 'route', $collisionIndexBy = 'url_pattern')
    {
        $collisionIndex = [];

        foreach ($routes as $route) {
            $key = $route->$indexBy;
            $collisionKey = $route->$collisionIndexBy;
            if (isset($collisionIndex[$key])) {
                if (!is_array($collisionIndex[$key])) {
                    $collisionIndex[$key] = [$collisionIndex[$key]->$collisionIndexBy => $collisionIndex[$key]];
                }
                $collisionIndex[$key][$collisionKey] = $route;

                continue;
            }
            $collisionIndex[$key] = $route;
        }

        return $collisionIndex;
    }
}
