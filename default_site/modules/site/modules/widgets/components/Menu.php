<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 01.04.16 4:54
 */

namespace site\modules\widgets\components;

use Yii;
use yii\base\Widget;
use design\modules\menu\interfaces\ManagerInterface;

class Menu extends Widget
{
    /**
     * @var ManagerInterface
     */
    public $manager;

    /**
     * @inheritdoc
     */
    public function __construct(ManagerInterface $manager, $config = [])
    {
        $this->manager = $manager;
        parent::__construct($config);
    }

    public function run()
    {
        $content = $this->manager->getContent();

        return $this->render('@site/modules/widgets/views/menu.php', ['content' => $content]);
    }
}
