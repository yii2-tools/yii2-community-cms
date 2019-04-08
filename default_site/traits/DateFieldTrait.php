<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 08.05.16 1:40
 */

namespace app\traits;

use Yii;

trait DateFieldTrait
{
    /**
     * Returns formatted created_at datetime string.
     * @return string
     */
    public function getDate()
    {
        return $this->getFormattedDate(
            date('Ymd') == date('Ymd', $this->created_at)
                ? Yii::t('app', 'Today') . ', HH:mm'
                : 'cccc, d MMMM yyyy, HH:mm'
        );
    }

    /**
     * Returns formatted created_at datetime string.
     * @return string
     */
    public function getShortDate()
    {
        return $this->getFormattedDate(
            date('Ymd') == date('Ymd', $this->created_at)
                ? Yii::t('app', 'Today') . ', HH:mm'
                : 'EEEEEE, dd.MM.YYYY, HH:mm'
        );
    }

    public function getEditDate()
    {
        return $this->getFormattedDate(
            date('Ymd') == date('Ymd', $this->updated_at)
                ? Yii::t('app', 'Today') . ', HH:mm'
                : 'cccc, d MMMM yyyy, HH:mm'
        );
    }

    public function getFormattedDate($format)
    {
        return Yii::$app->getFormatter()->asDatetime($this->created_at, $format);
    }
}
