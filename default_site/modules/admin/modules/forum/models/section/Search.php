<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 06.05.16 19:15
 */

namespace admin\modules\forum\models\section;

use Yii;
use admin\modules\forum\models\Search as BaseSearch;

/**
 * Class Search
 * @package admin\modules\forum\models\section
 */
class Search extends BaseSearch
{
    public $modelClass = 'site\modules\forum\models\Section';
}
