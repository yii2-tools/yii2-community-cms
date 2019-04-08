<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 05.05.16 18:13
 */

namespace site\modules\forum\helpers;

use site\modules\forum\models\Subforum;
use site\modules\forum\models\Topic;
use site\modules\forum\models\Post;

/**
 * Class ForumHelper
 * @package site\modules\forum\helpers
 * @since 2.0.0
 */
class ForumHelper
{
    /**
     * Performs query to storage and returns last post id for target subforum.
     * @param Subforum $subforum
     * @return int last created post id for target subforum, or 0 if result set is empty
     */
    public static function getSubforumLastPostId(Subforum $subforum)
    {
        return (int) Post::find()
            ->alias('p')
            ->innerJoin(Topic::tableName() . ' t', "p.topic_id = t." . Topic::primaryKey()[0])
            ->innerJoin(Subforum::tableName() . ' s', "t.subforum_id = s." . Subforum::primaryKey()[0])
            ->andWhere(['s.' . Subforum::primaryKey()[0] => $subforum->id])
            ->max('p.' . Post::primaryKey()[0]);
    }

    /**
     * Performs query to storage and returns last post id for target topic.
     * @param Topic $topic
     * @return int last created post id for target topic, or 0 if result set is empty
     */
    public static function getTopicLastPostId(Topic $topic)
    {
        return (int) Post::find()
            ->alias('p')
            ->innerJoin(Topic::tableName() . ' t', "p.topic_id = t." . Topic::primaryKey()[0])
            ->andWhere(['p.is_first' => 0])
            ->andWhere(['t.' . Topic::primaryKey()[0] => $topic->id])
            ->max('p.' . Post::primaryKey()[0]);
    }
}
