<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 23.02.16 20:34
 */

namespace admin\modules\plugins\models;

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
    public $method = IntegrationHelper::PLUGINS_GET;
}
