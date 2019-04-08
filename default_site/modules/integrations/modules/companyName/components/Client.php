<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 19.04.16 2:10
 */

namespace integrations\modules\companyName\components;

use Yii;
use yii\base\Component;
use yii\helpers\Json;
use yii\tools\interfaces\RequestInterface;
use integrations\modules\companyName\interfaces\ClientInterface;

/**
 * Class Client
 *
 * This class contains logic ported from M_API_Plugins::call (old engine 1.0)
 * @see <gitlab link>
 *
 * @package integrations\modules\companyName\components
 */
abstract class Client extends Component implements ClientInterface
{
    /**
     * @var RequestInterface
     */
    public $requester;

    /**
     * @inheritdoc
     */
    public function __construct(RequestInterface $requester, $config = [])
    {
        $this->requester = $requester;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function call(array $data)
    {
        return Json::decode($this->requester->request($data['url'], $data['params']));
    }
}
