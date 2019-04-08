<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 22.03.16 1:26
 */

namespace admin\modules\widgets\models;

use integrations\modules\companyName\helpers\CompanyNameHelper as IntegrationHelper;
use integrations\modules\companyName\models\BaseSearch;

/**
 * Class Search
 * @package admin\modules\plugins\models
 */
class Search extends BaseSearch
{
    /**
     * @inheritdoc
     */
    public $method = IntegrationHelper::WIDGETS_GET;
}
