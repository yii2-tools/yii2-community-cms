<?php

namespace site\modules\users\models;

use Yii;
use yii\authclient\ClientInterface as BaseClientInterface;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use app\helpers\ModuleHelper;
use app\helpers\RouteHelper;
use site\modules\users\clients\ClientInterface;
use site\modules\users\Finder;
use site\modules\users\models\query\AccountQuery;
use site\modules\users\Module;

class SocialAccount extends ActiveRecord
{
    /** @var Module */
    protected $module;

    /** @var Finder */
    protected static $finder;

    /** @var */
    private $data;

    /** @inheritdoc */
    public function init()
    {
        $this->module = Yii::$app->getModule(ModuleHelper::USERS);
    }

    /** @inheritdoc */
    public static function tableName()
    {
        return '{{%users_social_accounts}}';
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return bool Whether this social account is connected to user.
     */
    public function getIsConnected()
    {
        return $this->user_id != null;
    }

    /**
     * @return mixed Json decoded properties.
     */
    public function getDecodedData()
    {
        if ($this->data == null) {
            $this->data = json_decode($this->data);
        }

        return $this->data;
    }

    /**
     * Returns connect url.
     * @return string
     */
    public function getConnectUrl()
    {
        $code = Yii::$app->security->generateRandomString();
        $this->updateAttributes(['code' => md5($code)]);

        return Url::to([RouteHelper::SITE_USERS_REGISTRATION_CONNECT, 'code' => $code]);
    }

    public function connect(User $user)
    {
        return $this->updateAttributes([
            'username' => null,
            'email'    => null,
            'code'     => null,
            'user_id'  => $user->id,
            //'updated_at' => time(),
        ]);
    }

    /**
     * @return AccountQuery
     */
    public static function find()
    {
        return Yii::createObject(AccountQuery::className(), [get_called_class()]);
    }

    public static function create(BaseClientInterface $client)
    {
        /** @var Account $account */
        $account = Yii::createObject([
            'class'      => static::className(),
            'provider'   => $client->getId(),
            'client_id'  => $client->getUserAttributes()['id'],
            'data'       => json_encode($client->getUserAttributes()),
        ]);

        if ($client instanceof ClientInterface) {
            $account->setAttributes([
                'username' => $client->getUsername(),
                'email'    => $client->getEmail(),
            ], false);
        }

        if (($user = static::fetchUser($account)) instanceof User) {
            $account->user_id = $user->id;
        }

        $account->save(false);

        return $account;
    }

    /**
     * Tries to find an account and then connect that account with current user.
     *
     * @param BaseClientInterface $client
     */
    public static function connectWithUser(BaseClientInterface $client)
    {
        if (Yii::$app->user->isGuest) {
            Yii::$app->session->setFlash('danger', Yii::t(ModuleHelper::USERS, 'Something went wrong'));

            return;
        }

        $account = static::fetchAccount($client);

        if ($account->user === null) {
            $account->link('user', Yii::$app->user->identity);
            Yii::$app->session->setFlash('success', Yii::t(ModuleHelper::USERS, 'Your account has been connected'));
            return;
        }

        Yii::$app->session->setFlash(
            'danger',
            Yii::t(ModuleHelper::USERS, 'This account has already been connected to another user')
        );
    }

    /**
     * Tries to find account, otherwise creates new account.
     *
     * @param BaseClientInterface $client
     *
     * @return Account
     * @throws \yii\base\InvalidConfigException
     */
    protected static function fetchAccount(BaseClientInterface $client)
    {
        $account = static::getFinder()->findAccount()->byClient($client)->one();

        if (null === $account) {
            $account = Yii::createObject([
                'class'      => static::className(),
                'provider'   => $client->getId(),
                'client_id'  => $client->getUserAttributes()['id'],
                'data'       => json_encode($client->getUserAttributes()),
            ]);
            $account->save(false);
        }

        return $account;
    }

    /**
     * Tries to find user or create a new one.
     *
     * @param Account $account
     *
     * @return User|bool False when can't create user.
     */
    protected static function fetchUser(Account $account)
    {
        $user = static::getFinder()->findUserByEmail($account->email);

        if (null !== $user) {
            return $user;
        }

        $user = Yii::createObject([
            'class'    => User::className(),
            'scenario' => 'connect',
            'username' => $account->username,
            'email'    => $account->email,
        ]);

        if (!$user->validate(['email'])) {
            $account->email = null;
        }

        if (!$user->validate(['username'])) {
            $account->username = null;
        }

        return $user->create() ? $user : false;
    }

    /**
     * @return Finder
     */
    protected static function getFinder()
    {
        if (static::$finder === null) {
            static::$finder = Yii::$container->get(Finder::className());
        }

        return static::$finder;
    }
}
