<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 18.04.16 6:08
 */

namespace api\modules\v200\components;

use Yii;
use yii\base\Component;
use site\modules\users\Finder;

/**
 * API component 'users'
 *
 * Port from API v3 (engine 1.0)
 * @see <gitlab link>
 *
 * @package api\modules\v200\components
 */
class Users extends Component
{
    /**
     * @var Finder
     */
    public $finder;

    /**
     * @inheritdoc
     */
    public function __construct(Finder $finder, $config = [])
    {
        $this->finder = $finder;
        parent::__construct($config);
    }

    /**
     * Creates compatible user object for plugins.
     *
     * @param $identity
     * @return \StdClass
     */
    private function createCompatibleUserObject($identity = null)
    {
        if (!$identity) {
            return false;
        }

        $user = new \StdClass();
        $user->user_id = $identity->id;
        $user->nick = $identity->username;

        if ($roles = Yii::$app->getAuthManager()->getRolesByUser($identity->id)) {
            $role = array_shift($roles);
        }

        $user->role_id = isset($role) ? $role->name : 0;

//        if ($user->role_id === 'ADMIN_ACCESS') {
//            $user->role_id = 4;
//        }

        return $user;
    }

    /**
     * @param string $nick
     * @return array
     */
    public function getByNick($nick)
    {
        if (!($identity = $this->finder->findUserByUsername($nick))) {
            return false;
        }

        return $this->createCompatibleUserObject($identity);
    }

    /**
     * @param array $filter_array
     * @return array
     */
    private function parseArgs($filter_array)
    {
        $parsed_filter_array = [];

        foreach ($filter_array as $value) {
            $parsed_filter_array[$value] = true;
        }

        return $parsed_filter_array;
    }

    /**
     * @param array $filter
     * @return array
     */
    public function get($filter)
    {
        $filter = $this->parseArgs((array)$filter);

        if (isset($filter['CURRENT'])) {
            return $this->createCompatibleUserObject(Yii::$app->getUser()->getIdentity());
        }

        return false;
    }

    /**
     * @param array $filter
     * @return int
     */
    public function count($filter)
    {
        $filter = (array)$filter;

        return count($this->get($filter));
    }

    /**
     * @param int $userId
     * @param string $authItemName
     * @return bool
     */
    public function can($userId, $authItemName)
    {
        if ('ADMIN_ACCESS' === $authItemName) {
            if (($identity = Yii::$app->getUser()->getIdentity()) && $identity->isAdmin) {
                return true;
            }
        }

        Yii::$app->getAuthManager()->checkAccess($userId, $authItemName) ? 1 : 0;
    }
}
