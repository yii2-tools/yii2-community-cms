<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 30.04.16 18:02
 */

namespace site\modules\forum\components;

use Yii;
use yii\base\Component;
use yii\base\Event;
use yii\tools\secure\ActiveRecord;
use site\modules\forum\helpers\ForumHelper;
use site\modules\forum\models\Subforum;
use site\modules\forum\models\Topic;
use site\modules\forum\models\Post;

class Observer extends Component
{
    /**
     * @param Event $event field sender will contain instanceof Topic
     */
    public function afterTopicInsert(Event $event)
    {
        ActiveRecord::secure(false);

        /** @var Subforum $subforum */
        $subforum = $event->sender->subforum;

        ++$subforum->topics_num;
        $subforum->save(false);

        ActiveRecord::secure(true);
    }

    /**
     * @param Event $event field sender will contain instanceof Topic
     */
    public function beforeTopicDelete(Event $event)
    {
        ActiveRecord::secure(false);

        /** @var Post[] $posts */
        $posts = $event->sender->posts;

        foreach ($posts as $post) {
            $post->delete();
        }

        ActiveRecord::secure(true);
    }

    /**
     * @param Event $event field sender will contain instanceof Topic
     */
    public function afterTopicDelete(Event $event)
    {
        ActiveRecord::secure(false);

        /** @var Subforum $subforum */
        $subforum = $event->sender->subforum;

        --$subforum->topics_num;
        $subforum->save(false);

        ActiveRecord::secure(true);
    }

    /**
     * @param Event $event field sender will contain instanceof Post
     */
    public function afterPostInsert(Event $event)
    {
        ActiveRecord::secure(false);

        /** @var Post $post */
        $post = $event->sender;
        /** @var Topic $topic */
        $topic = $post->topic;
        /** @var Subforum $subforum */
        $subforum = $topic->subforum;

        if (!$post->is_first) {
            ++$topic->posts_num;
            $topic->last_post_id = $post->id;
            $topic->save(false);

            ++$subforum->posts_num;
        }

        $subforum->last_post_id = $post->id;
        $subforum->save(false);

        ActiveRecord::secure(true);
    }

    /**
     * @param Event $event field sender will contain instanceof Post
     */
    public function afterPostDelete(Event $event)
    {
        ActiveRecord::secure(false);

        /** @var Post $post */
        $post = $event->sender;
        /** @var Topic $topic */
        $topic = $post->topic;
        /** @var Subforum $subforum */
        $subforum = $topic->subforum;

        if (!$post->is_first) {
            --$topic->posts_num;
            if ($topic->last_post_id == $post->id) {
                $topic->last_post_id = ForumHelper::getTopicLastPostId($topic);
            }
            $topic->save(false);

            --$subforum->posts_num;
        }

        if ($subforum->last_post_id == $post->id) {
            $subforum->last_post_id = ForumHelper::getSubforumLastPostId($subforum);
        }
        $subforum->save(false);

        ActiveRecord::secure(true);
    }
}
