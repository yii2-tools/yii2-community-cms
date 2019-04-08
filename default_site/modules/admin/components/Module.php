<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 25.02.16 20:45
 */

namespace app\modules\admin\components;

use Yii;
use app\components\Module as BaseModule;
use app\helpers\ModuleHelper;
use app\helpers\RouteHelper;

/**
 * Class Module
 *
 * @property string $contentLayout     read-only
 * @package app\modules\admin\components
 */
class Module extends BaseModule
{
    /**
     * @var \app\components\Module
     */
    public $siteModule;

    /**
     * @var bool Is current module admin submodule
     */
    protected $isSubmodule;

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        if (isset($behaviors['breadcrumbs'])) {
            if ($this->isSubmodule) {
                unset($behaviors['breadcrumbs']);
            } else {
                $behaviors['breadcrumbs']['label'] = $this->siteModule->params['name'];
            }
        }

        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->siteModule = Yii::$app->getModule(ModuleHelper::SITE . '/' . ModuleHelper::id($this->getUniqueId(), 2));

        if ($this->module instanceof self) {
            $this->isSubmodule = true;
        }

        parent::init();
    }

    /**
     * @return string
     */
    public function getContentLayout()
    {
        return $this->module->contentLayout;
    }

    /**
     * Returns top-level module of current hierarchy of modules to which current module belongs.
     *
     * @return Module|null
     */
    public function getTopParent()
    {
        $module = $this;

        while ($module instanceof self && $module->isSubmodule) {
            $module = $module->module;
        }

        return $module;
    }

    /**
     * List of actions which module can perform
     * Used in: app/modules/admin/views/default/index.php (main admin page)
     *
     * ```
     * [
     *     [
     *         'label' => '...',
     *         'url' => '...'
     *     ]
     * ]
     * ```
     *
     * Note: Overwritten method should merge parent::actions() due to append possible child modules actions
     *
     * @return array
     */
    public function actions()
    {
        return $this->getChildActions($this);
    }

    /**
     * Returns all actions in hierarchy of modules to which current module belongs.
     *
     * @return array
     */
    public function actionsHierarchy()
    {
        $module = $this->isSubmodule ? $this->getTopParent() : $this;

        return $module->actions();
    }

    /**
     * @param Module $module
     * @return array
     */
    protected function getChildActions($module)
    {
        $submodules = $module->getModules(false, true);
        $childActions = [];

        foreach ($submodules as $submodule) {
            $childActions = array_merge($childActions, $submodule->actions());
        }

        return $childActions;
    }

    /**
     * List of menu items with actions which module can perform
     *
     * ```
     * [
     *     [
     *         'label' => '...',
     *         'url' => '...'
     *     ]
     * ]
     * ```
     *
     * Note: this method may be overridden only in top-level admin module
     *
     * @return array
     */
    public function menu()
    {
        $menu = $this->actionsHierarchy();

        // Setup params available for main admin module only
        // But all params of admin module submodules also will be displayed
        $module = $this->getTopParent();

        if ($module->siteModule instanceof BaseModule && $module->siteModule->params->safeActiveCount() > 0) {
            $menu[] = [
                'label' => Yii::t(ModuleHelper::ADMIN_SETUP, 'Setup'),
                'url' => [RouteHelper::ADMIN_SETUP, 'module' => $module->id],
            ];
        }

        return $menu;
    }

    /**
     * List of sidebar items for this module
     * Used in: app/modules/admin/views/layouts/sidebar.php (sidebar template)
     *
     * ```
     * [
     *     'label' => 'Users Management',
     *     'icon' => 'dashboard'
     *     'url' => false|[RouteHelper::SOME_CONST],
     *     'items' => [...]
     * ]
     * ```
     *
     * Note: items will be filled as menu() if exists (in main admin layout)
     * Note: this method may be overridden only in top-level admin module
     *
     * @return array
     */
    public function sidebar()
    {
        if (!($items = $this->sidebarItems())) {
            return [];
        }

        return [
            'label' => $this->siteModule->params['name'],
            'icon' => $this->params['icon'],
            'url' => false,
            'items' => $items
        ];
    }

    /**
     * Note: this method may be overridden only in top-level admin module
     * @return array
     */
    public function sidebarItems()
    {
        return ($menu = $this->menu()) ? $menu : [];
    }
}
