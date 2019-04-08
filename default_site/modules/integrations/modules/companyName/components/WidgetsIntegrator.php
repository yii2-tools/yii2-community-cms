<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.02.16 15:20
 */

namespace integrations\modules\companyName\components;

use Yii;
use yii\helpers\VarDumper;
use app\modules\integrations\components\DummyWidget;
use app\modules\integrations\traits\PlaceholderIntegratorTrait;
use design\modules\content\helpers\PlaceholderHelper;
use design\modules\content\models\ActivePlaceholder;

/**
 * Class WidgetsIntegrator
 * @package integrations\modules\companyName\components
 */
class WidgetsIntegrator extends Integrator
{
    use PlaceholderIntegratorTrait;

    const MAIN_SERVER_QUERY_ACTION = 10;

    /** @inheritdoc */
    public $dataKey = 'widget_key';

    /** @var string */
    public $dataDirField = 'widget_dir_name';

    /** @inheritdoc */
    public $dataKeyShort = 'wk';

    public function add()
    {
        $this->activate();
    }

    public function updateIntegrationData($data)
    {
        $name = $this->buildPlaceholderName(ActivePlaceholder::TYPE_WIDGET, $data['widget_dir_name']);
        $content = serialize(Yii::$container->get(DummyWidget::className()));
        $row = [
            'name' => $name,
            'type' => ActivePlaceholder::TYPE_WIDGET,
            'status' => PlaceholderHelper::STATUS_ACTIVATION_REQUIRED,
            'content' => $content,
        ];
        Yii::info("Creating new placeholder '$name'" . PHP_EOL . VarDumper::dumpAsString($row), __METHOD__);
        $placeholder = PlaceholderHelper::create($row);
        $placeholder->save(false);

        parent::updateIntegrationData($data);
    }

    public function delete()
    {
        $this->deactivate();
    }

    public function deleteIntegrationData($data)
    {
        $placeholder = ActivePlaceholder::find()
            ->name($this->buildPlaceholderName(ActivePlaceholder::TYPE_WIDGET, $data['widget_dir_name']))
            ->one();
        $this->deactivatePlaceholder($placeholder);

        $this->setIntegrationData($this->config[$this->dataKey], array_merge($data, ['status' => 0]));
    }
}
