<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 07.05.16 18:33
 */

namespace admin\modules\forum\components;

use Yii;
use yii\base\Component;
use yii\base\Event;
use yii\tools\secure\ActiveRecord;
use site\modules\forum\models\Subforum;
use site\modules\forum\models\Topic;

class Observer extends Component
{
    /**
     * @param Event $event field sender will contain instanceof Section
     */
    public function beforeSectionDelete(Event $event)
    {
        ActiveRecord::secure(false);

        /** @var Subforum[] $subforums */
        $subforums = $event->sender->subforums;

        foreach ($subforums as $subforum) {
            $subforum->delete();
        }

        ActiveRecord::secure(true);
    }

    /**
     * @param Event $event field sender will contain instanceof Subforum
     */
    public function beforeSubforumDelete(Event $event)
    {
        ActiveRecord::secure(false);

        /** @var Topic[] $topics */
        $topics = $event->sender->topics;

        foreach ($topics as $topic) {
            $topic->delete();
        }

        ActiveRecord::secure(true);
    }
}
