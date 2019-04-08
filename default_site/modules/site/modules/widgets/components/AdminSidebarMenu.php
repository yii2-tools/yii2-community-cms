<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 27.02.16 14:31
 */

namespace site\modules\widgets\components;

use Yii;
use app\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use dmstr\widgets\Menu as BaseMenu;

/**
 * Class Menu
 * @package app\widgets
 */
class AdminSidebarMenu extends BaseMenu
{
    public $defaultIcon = 'circle-thin';

    /**
     * @inheritdoc
     */
    protected function renderItem($item)
    {
        $linkTemplate = '<a href="{url}">{icon} {label} {right}</a>';

        if (isset($item['url'])) {
            $template = ArrayHelper::getValue($item, 'template', $linkTemplate);
            if (empty($item['icon']) && !empty($this->defaultIcon)) {
                $item['icon'] = $this->defaultIcon;
            }
            $replace = [
                '{url}' => Url::to($item['url']),
                '{label}' => '<span>'.$item['label'].'</span>',
                '{icon}' => empty($item['icon']) ? '' : Html::faIcon($item['icon'], ['tag' => 'i']),
            ];
        } else {
            $template = ArrayHelper::getValue($item, 'template', $this->labelTemplate);
            $replace = [
                '{label}' => '<span>'.$item['label'].'</span>'
            ];
            if (!empty($item['icon'])) {
                $replace['{icon}'] = Html::faIcon($item['icon'], ['tag' => 'i']);
            }
        }

        if (!empty($item['right'])) {
            $rightOptions = ArrayHelper::getValue($item['right'], 'options', []);
            $type = isset($item['right']['type']) ? $item['right']['type'] : 'primary';
            Html::addCssClass($rightOptions, ['label', 'label-' . $type, 'pull-right']);
            $replace['{right}'] = Html::tag('span', $item['right']['label'], $rightOptions);
        } else {
            $replace['{right}'] = isset($item['items']) ? '<i class="fa fa-angle-left pull-right"></i>' : '';
        }

        return strtr($template, $replace);
    }

    /**
     * @inheritdoc
     */
    protected function isItemActive($item)
    {
        if (!isset($item['url']) || !is_array($item['url']) || !isset($item['url'][0])) {
            return false;
        }

        $route = $item['url'][0];
        if ($route[0] !== '/' && Yii::$app->controller) {
            $route = Yii::$app->controller->module->getUniqueId() . '/' . $route;
        }
        $arrayRoute = explode('/', ltrim($route, '/'));
        $arrayThisRoute = explode('/', $this->route);
        foreach ($arrayThisRoute as $index => $route) {
            if (!isset($arrayRoute[$index]) || ($arrayRoute[$index] !== $arrayThisRoute[$index])) {
                return false;
            }
        }

        unset($item['url']['#']);
        if (count($item['url']) > 1) {
            foreach (array_splice($item['url'], 1) as $name => $value) {
                if ($value !== null && (!isset($this->params[$name]) || $this->params[$name] != $value)) {
                    return false;
                }
            }
        }

        return true;
    }
}
