<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 15.04.16 5:14
 */

namespace design\modules\menu\components;

use Yii;
use yii\base\Component;
use yii\tools\interfaces\ContentGeneratorInterface;
use design\modules\menu\interfaces\ManagerInterface;

class Manager extends Component implements ManagerInterface
{
    /**
     * @var array
     */
    private $content = [];

    /**
     * @var ContentGeneratorInterface[]
     */
    private $generators = [];

    /**
     * @inheritdoc
     */
    public function register(ContentGeneratorInterface $generator)
    {
        $this->generators[] = $generator;
    }

    /**
     * Used by site components which provide some additional content to be rendered in menu.
     *
     * @param string $content html code of additional content for menu
     */
    public function addContent($content)
    {
        $this->content[] = $content;
    }

    /**
     * Returns array of content registered as additional menus content
     * (in register order, FIFO)
     *
     * @return array
     */
    public function getContent()
    {
        $this->generateContent();

        return $this->content;
    }

    /**
     * Generates additional content for menu.
     */
    protected function generateContent()
    {
        foreach ($this->generators as $generator) {
            $content = (array)$generator->generateContent();
            foreach ($content as $html) {
                $this->addContent($html);
            }
        }
    }
}
