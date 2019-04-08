<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 23.03.16 17:39
 */

namespace design\modules\content\components;

use Yii;
use yii\caching\ChainedDependency;
use yii\helpers\VarDumper;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\caching\ExpressionDependency;
use design\modules\content\interfaces\ResolverInterface;

/**
 * Used for inspecting text files for the presence of content placeholder variables.
 * @package design\modules\content\components
 */
class Resolver extends Component implements ResolverInterface
{
    /** @var string */
    public $pattern;

    /** @var array */
    public $tags;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (!isset($this->pattern)) {
            throw new InvalidConfigException("Property 'pattern' must be set");
        }
    }

    /**
     * Search and return all placeholder variables which presence in target text file.
     *
     * @param string $filepath  Text file
     * @param bool $cache
     * @return array
     */
    public function inspect($filepath, $cache = true)
    {
        $filepath = Yii::getAlias($filepath);
        Yii::trace("Inspecting view '$filepath' for used placeholders"
            . ' (cache = ' . VarDumper::dumpAsString($cache) . ')', __METHOD__);

        if ($cache && ($placeholderNames = Yii::$app->getCache()->get([__METHOD__, func_get_args()])) !== false) {
            Yii::info('Used placeholders served from cache'
                . PHP_EOL . VarDumper::dumpAsString($placeholderNames), __METHOD__);
            return $placeholderNames;
        }

        $content = file_get_contents($filepath);
        $pattern = $this->pattern;

        if (isset($this->tags) && false !== $this->tags) {
            $pattern = $this->tags[0] . $pattern . $this->tags[1];
        }

        preg_match_all('/' . $pattern . '/', $content, $matches);
        $placeholderNames = isset($matches[1]) ? $matches[1] : [];
        Yii::info('Used placeholders found'
            . PHP_EOL . VarDumper::dumpAsString($placeholderNames), __METHOD__);

        if ($cache) {
            $dependency = Yii::$container->get(ChainedDependency::className(), [], [
                'dependencies' => [
                    Yii::$container->get(ExpressionDependency::className(), [], [
                        'expression' => 'Yii::$app->getModule('
                            . '\site\modules\design\helpers\ModuleHelper::DESIGN_CONTENT)'
                            . '->params["version"]',
                        'reusable' => true,
                    ]),
                    Yii::$container->get(ExpressionDependency::className(), [], [
                        'expression' => "filemtime('$filepath')",
                        'reusable' => true,
                    ]),
                ],
                'reusable' => true,
            ]);
            Yii::$app->getCache()->set([__METHOD__, func_get_args()], $placeholderNames, 0, $dependency);
        }

        return $placeholderNames;
    }
}
