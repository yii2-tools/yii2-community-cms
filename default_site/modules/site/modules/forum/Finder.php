<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 30.04.16 17:56
 */

namespace site\modules\forum;

use Yii;
use yii\base\Object;
use site\modules\forum\models\Section;
use site\modules\forum\models\Subforum;
use site\modules\forum\models\Topic;
use site\modules\forum\models\Post;

//
// WARNING: Secure behavior requires monitoring of rbac assigments COUNT(*) and 'updated_at' field.
//
class Finder extends Object
{
    /**
     * @param array|string $condition
     * @param bool $secure
     * @param bool $all
     * @return array|null|Section|Section[]
     */
    public function findSection($condition = null, $secure = true, $all = false)
    {
        $query = Section::find()->where($condition)->secure($secure);

        return $all ? $query->all() : $query->one();
    }

    /**
     * @param array|string $condition
     * @param bool $secure
     * @param bool $all
     * @return array|null|Subforum|Subforum[]
     */
    public function findSubforum($condition = null, $secure = true, $all = false)
    {
        $query = Subforum::find()->where($condition)->secure($secure);

        return $all ? $query->all() : $query->one();
    }

    /**
     * @param array|string $condition
     * @param bool $secure
     * @param bool $all
     * @return array|null|Topic|Topic[]
     */
    public function findTopic($condition = null, $secure = true, $all = false)
    {
        $query = Topic::find()->where($condition)->secure($secure);

        return $all ? $query->all() : $query->one();
    }

    /**
     * @param array|string $condition
     * @param bool $secure
     * @param bool $all
     * @return array|null|Post|Post[]
     */
    public function findPost($condition = null, $secure = true, $all = false)
    {
        $query = Post::find()->where($condition)->secure($secure);

        return $all ? $query->all() : $query->one();
    }
}
