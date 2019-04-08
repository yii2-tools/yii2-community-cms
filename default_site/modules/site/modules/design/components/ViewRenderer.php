<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 13.03.16 7:30
 */

namespace site\modules\design\components;

use Yii;
use yii\twig\ViewRenderer as BaseViewRenderer;
use site\modules\design\interfaces\PlaceholderRendererInterface;
use site\modules\design\interfaces\ContextInterface;

/**
 * ViewRenderer for design module should be extended from concrete View Renderer
 * @package site\modules\design\components
 */
class ViewRenderer extends BaseViewRenderer
{
    /**
     * Options for template engine sandbox (or similar) mode
     * @var array
     */
    public $sandboxOptions = [];

    /**
     * @var ContextInterface
     */
    public $designContext;

    /**
     * @inheritdoc
     */
    public function __construct(ContextInterface $designContext, $config = [])
    {
        $this->designContext = $designContext;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->ensureSandbox();
        parent::init();
    }

    /**
     * Rendering site templates with global placeholders.
     * @inheritdoc
     */
    public function render($view, $file, $params)
    {
        $params = array_merge($params, $this->designContext->getGlobalPlaceholders($file));
        return parent::render($view, $file, $params);
    }

    /**
     * @inheritdoc
     */
    public function ensureSandbox()
    {
        extract($this->sandboxOptions);
        $policy = new \Twig_Sandbox_SecurityPolicy(
            $allowedTags,
            $allowedFilters,
            $allowedMethods,
            $allowedProperties,
            $allowedFunctions
        );
        $extensions = [new \Twig_Extension_Sandbox($policy, true)];
        $this->extensions = array_merge($this->extensions, $extensions);
    }
}
