<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 06.05.16 19:40
 */

namespace admin\modules\forum\models\subforum;

use Yii;
use admin\modules\forum\models\Search as BaseSearch;

/**
 * Class Search
 * @package admin\modules\forum\models\subforum
 */
class Search extends BaseSearch
{
    /**
     * @inheritdoc
     */
    public $modelClass = 'site\modules\forum\models\Subforum';

    /**
     * @var string
     */
    public $section_id;

    /**
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            'section' => [['section_id'], 'default'],
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function filter($query)
    {
        $query = parent::filter($query);

        if (!empty($this->section_id)) {
            $query->joinWith(['section s'], true, 'INNER JOIN')
                ->andWhere(['like', 's.title', $this->section_id]);
        }
    }
}
