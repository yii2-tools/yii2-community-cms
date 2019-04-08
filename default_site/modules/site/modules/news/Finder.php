<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 08.05.16 0:53
 */

namespace site\modules\news;

use Yii;
use yii\base\Object;
use yii\data\Pagination;
use site\modules\news\models\NewsRecord;

class Finder extends Object
{
    /**
     * @param array|string $condition
     * @param bool $secure
     * @param bool $all
     * @param bool $pagination
     * @param bool $order creation order
     * @return array|null|NewsRecord|NewsRecord[]
     */
    public function findNews($condition = null, $secure = true, $all = false, $pagination = false, $order = true)
    {
        $query = NewsRecord::find()->where($condition)->secure($secure);

        if ($pagination) {
            /** @var Pagination $pagination */
            $pagination = Yii::$container->get(Pagination::className());

            $offset = !empty($pagination->offset) ? $pagination->offset : 0;
            $limit = !empty($pagination->limit) ? $pagination->limit : Module::NEWS_PER_PAGE;

            $query->offset($offset)->limit($limit);
        }

        if ($order) {
            $query->orderBy(['id' => SORT_DESC]);
        }

        return $all ? $query->all() : $query->one();
    }
}
