<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 30.01.16 11:14
 */

namespace app\components\controllers;

use Yii;
use yii\helpers\Console;
use yii\console\Controller as BaseController;

abstract class ConsoleController extends BaseController
{
    /**
     * @var bool
     */
    public $enableBuffer = false;

    /** @var array */
    protected $buffer = [];

    /**
     * Returns the names of valid options for the action (id)
     * An option requires the existence of a public member variable whose
     * name is the option name.
     * Child classes may override this method to specify possible options.
     *
     * Note that the values setting via options are not available
     * until [[beforeAction()]] is being called.
     *
     * @param string $actionID the action id of the current request
     * @return array the names of the options valid for the action
     */
    public function options($actionID)
    {
        return array_merge(
            parent::options($actionID),
            ['enableBuffer']
        );
    }

    /**
     * Formats a string with ANSI codes
     *
     * You may pass additional parameters using the constants defined in [[\yii\helpers\Console]].
     *
     * Example:
     *
     * ~~~
     * echo $this->ansiFormat('This will be red and underlined.', Console::FG_RED, Console::UNDERLINE);
     * ~~~
     *
     * @param string $string the string to be formatted
     * @return string
     */
    public function ansiFormat($string)
    {
        if (YII_APP_CONSOLE) {
            if ($this->isColorEnabled()) {
                $args = func_get_args();
                array_shift($args);
                $string = Console::ansiFormat($string, $args);
            }
        }
        return $string;
    }

    /**
     * Prints a string to STDOUT
     *
     * You may optionally format the string with ANSI codes by
     * passing additional parameters using the constants defined in [[\yii\helpers\Console]].
     *
     * Example:
     *
     * ~~~
     * $this->stdout('This will be red and underlined.', Console::FG_RED, Console::UNDERLINE);
     * ~~~
     *
     * @param string $string the string to print
     * @return int|boolean Number of bytes printed or false on error
     */
    public function stdout($string)
    {
        if (YII_APP_CONSOLE) {
            if ($this->isColorEnabled()) {
                $args = func_get_args();
                array_shift($args);
                $string = Console::ansiFormat($string, $args);
            }
        }
        if ($this->enableBuffer) {
            $this->buffer[] = [$string, 0];
            return 0;
        }
        if (YII_APP_CONSOLE) {
            return Console::stdout($string);
        } else {
            Yii::info($string, __METHOD__);
        }
    }

    /**
     * Prints a string to STDERR
     *
     * You may optionally format the string with ANSI codes by
     * passing additional parameters using the constants defined in [[\yii\helpers\Console]].
     *
     * Example:
     *
     * ~~~
     * $this->stderr('This will be red and underlined.', Console::FG_RED, Console::UNDERLINE);
     * ~~~
     *
     * @param string $string the string to print
     * @return int|boolean Number of bytes printed or false on error
     */
    public function stderr($string)
    {
        if (YII_APP_CONSOLE) {
            if ($this->isColorEnabled(\STDERR)) {
                $args = func_get_args();
                array_shift($args);
                $string = Console::ansiFormat($string, $args);
            }
        }
        if ($this->enableBuffer) {
            $this->buffer[] = [$string, 1];
            return 0;
        }
        if (YII_APP_CONSOLE) {
            return fwrite(\STDERR, $string);
        } else {
            Yii::error($string, __METHOD__);
        }
    }

    /**
     * @return void
     */
    public function flush()
    {
        if (YII_APP_CONSOLE) {
            foreach ($this->buffer as $blob) {
                if ($blob[1] == 0) {
                    Console::stdout($blob[0]);
                } else {
                    fwrite(\STDERR, $blob[0]);
                }
            }
        } else {
            $logRecord = '';
            foreach ($this->buffer as $blob) {
                $logRecord .= $blob[0];
            }
            Yii::info($logRecord, __METHOD__);
        }
        $this->buffer = '';
    }

    /**
     * @param string $message
     * @param bool $default
     * @return bool
     */
    public function confirm($message, $default = false)
    {
        if (YII_APP_CONSOLE) {
            return parent::confirm($message, $default);
        } else {
            Yii::info('Confirmed: ' . $message, __METHOD__);
            return true;
        }
    }
}
