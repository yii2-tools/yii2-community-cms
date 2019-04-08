<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 14.03.16 22:39
 */

namespace app\controllers;

use Yii;
use yii\helpers\Console;
use yii\console\controllers\ServeController as BaseServeController;

class ServeController extends BaseServeController
{
    /**
     * @var string path to php .ini config file
     */
    public $config;

    /**
     * @inheritdoc
     */
    public function actionIndex($address = 'localhost')
    {
        $documentRoot = Yii::getAlias($this->docroot);

        if (strpos($address, ':') === false) {
            $address = $address . ':' . $this->port;
        }

        if (!is_dir($documentRoot)) {
            $this->stdout("Document root \"$documentRoot\" does not exist.\n", Console::FG_RED);
            return self::EXIT_CODE_NO_DOCUMENT_ROOT;
        }

        if ($this->isAddressTaken($address)) {
            $this->stdout("http://$address is taken by another process.\n", Console::FG_RED);
            return self::EXIT_CODE_ADDRESS_TAKEN_BY_ANOTHER_PROCESS;
        }

        if ($this->router !== null && !file_exists($this->router)) {
            $this->stdout("Routing file \"$this->router\" does not exist.\n", Console::FG_RED);
            return self::EXIT_CODE_NO_ROUTING_FILE;
        }

        $this->stdout("Server started on http://{$address}/\n");
        $this->stdout("Document root is \"{$documentRoot}\"\n");
        if ($this->router) {
            $this->stdout("Routing file is \"$this->router\"\n");
        }
        $this->stdout("Quit the server with CTRL-C or COMMAND-C.\n");

        $exec = '"' . PHP_BINARY . '"' . " -S {$address} -t \"{$documentRoot}\"";

        if ($this->config) {
            $exec .= " -c \"{$this->config}\"";
        }

        $exec .= " $this->router";

        passthru($exec);
    }

    public function options($actionID)
    {
        return array_merge(parent::options($actionID), [
            'config',
        ]);
    }
}
