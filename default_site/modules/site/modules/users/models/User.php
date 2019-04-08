<?php

namespace site\modules\users\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;
use yii\web\Application as WebApplication;
use yii\web\IdentityInterface;
use yii\web\UserEvent;
use app\helpers\ModuleHelper;
use yii\tools\interfaces\ParamsHolder;
use yii\tools\behaviors\CountableBehavior;
use yii\tools\components\ActiveRecord;
use yii\tools\params\ActiveParams;
use site\modules\users\Finder;
use site\modules\users\helpers\Password;
use site\modules\users\Mailer;
use site\modules\users\Module;
use admin\modules\users\helpers\RbacHelper;

class User extends ActiveRecord implements IdentityInterface, ParamsHolder
{
    const BEFORE_CREATE   = 'beforeCreate';
    const AFTER_CREATE    = 'afterCreate';
    const BEFORE_REGISTER = 'beforeRegister';
    const AFTER_REGISTER  = 'afterRegister';

    // following constants are used on secured email changing process
    const OLD_EMAIL_CONFIRMED = 0b1;
    const NEW_EMAIL_CONFIRMED = 0b10;

    // Captcha protection.
    // Count of authentication attempts available without entering captcha.
    // Forms validation counted.
    const MAX_AUTH_ATTEMPTS_COUNT = 4;

    const CACHE_DEPENDENCY_UPDATED_AT = 'SELECT COUNT(*), MAX([[updated_at]]) FROM [[users]] WHERE [[updated_at]] > 0';

    /** @var string Plain password. Used for model validation. */
    public $password;

    /** @var \site\modules\users\Module */
    protected $module;

    /** @var Mailer */
    protected $mailer;

    /** @var Finder */
    protected $finder;

    /** @var Profile|null */
    private $profile;

    /** @var string Default username regexp */
    public static $usernameRegexp = '/^[\p{L}a-zA-Z0-9-\.@]+$/u';

    /** @inheritdoc */
    public function init()
    {
        $this->module = Yii::$app->getModule(ModuleHelper::USERS);
        $this->params = Yii::$container->get(ActiveParams::className(), [], [
            'owner' => $this, 'params' => $this->module->getConfig('params_user')
        ]);
        $this->finder = Yii::$container->get(Finder::className());
        $this->mailer = Yii::$container->get(Mailer::className());

        $this->on(static::AFTER_REGISTER, [$this, 'afterCreateCallback']);
        $this->on(static::AFTER_CREATE, [$this, 'afterCreateCallback']);

        Yii::$app->user->on(\yii\web\User::EVENT_AFTER_LOGIN, [$this->module, 'onAuthSuccess']);
        Yii::$app->user->on(\yii\web\User::EVENT_AFTER_LOGOUT, [$this, 'onLogout']);

        if ($this->module->enableUnconfirmedLogin) {
            $this->on(static::AFTER_REGISTER, [$this, 'loginAfterRegister']);
        }

        parent::init();
    }

    public function loginAfterRegister($event)
    {
        Yii::trace('Perform autologin after successful registration', __METHOD__);
        Yii::$app->user->login($this, $this->module->rememberFor);
    }

    /**
     * @param $event
     */
    public function afterCreateCallback($event)
    {
        $this->updateLastUser($event);
        $this->assignDefaultRole($event);
    }

    /**
     * @param $event
     */
    public function updateLastUser($event)
    {
        $this->module->params['last_user'] = $event->sender->username;
    }

    /**
     * @param $event
     */
    public function assignDefaultRole($event)
    {
        Yii::trace('Assign default role for new user', __METHOD__);

        if ($role = RbacHelper::getDefaultUserRole()) {
            Yii::$app->getAuthManager()->assign(RbacHelper::getDefaultUserRole(), $this->getId());
            return;
        }

        Yii::warning('Default role is not defined or invalid, probably some kind of integrity error', __METHOD__);
    }

    /**
     * @param UserEvent $event
     */
    public function onLogout(UserEvent $event)
    {
        $event->identity->activity_at = 0;
        $event->identity->save(false);
    }

    /**
     * @inheritdoc
     */
    public function getUniqueId()
    {
        $id = $this->getId();
        return ModuleHelper::USERS . '/' . (!empty($id) ? $id : 0);
    }

    /**
     * @return bool Whether the user is confirmed or not.
     */
    public function getIsConfirmed()
    {
        return $this->confirmed_at != null;
    }

    /**
     * @return bool Whether the user is blocked or not.
     */
    public function getIsBlocked()
    {
        return $this->blocked_at != null;
    }

    /**
     * @return bool Whether the user is an admin or not.
     */
    public function getIsAdmin()
    {
        $adminModule = Yii::$app->getModule(ModuleHelper::ADMIN_USERS);
        return in_array($this->getId(), $adminModule->admins) || Yii::$app->getUser()->can(RbacHelper::ADMIN_ACCESS);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'id'])->inverseOf('user');
    }

    /**
     * @param Profile $profile
     */
    public function setProfile(Profile $profile)
    {
        $this->profile = $profile;
    }

    /**
     * @return \yii\rbac\Role[]
     */
    public function getRoles()
    {
        return Yii::$app->getAuthManager()->getRolesByUser($this->getId());
    }

    /**
     * @return Account[] Connected accounts ($provider => $account)
     */
    public function getAccounts()
    {
        $connected = [];
        $accounts  = $this->hasMany(SocialAccount::className(), ['user_id' => 'id'])->inverseOf('user')->all();

        /** @var Account $account */
        foreach ($accounts as $account) {
            $connected[$account->provider] = $account;
        }

        return $connected;
    }

    /** @inheritdoc */
    public function getId()
    {
        return $this->getAttribute('id');
    }

    /** @inheritdoc */
    public function getAuthKey()
    {
        return $this->getAttribute('auth_key');
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'username'          => Yii::t(ModuleHelper::USERS, 'Username'),
            'email'             => Yii::t(ModuleHelper::USERS, 'Email'),
            'registration_ip'   => Yii::t(ModuleHelper::USERS, 'Registration ip'),
            'unconfirmed_email' => Yii::t(ModuleHelper::USERS, 'New email'),
            'password'          => Yii::t(ModuleHelper::USERS, 'Password'),
            'created_at'        => Yii::t(ModuleHelper::USERS, 'Registration date'),
            'confirmed_at'      => Yii::t(ModuleHelper::USERS, 'Confirmation time'),
            'blocked_at'        => Yii::t(ModuleHelper::USERS, 'Ban time'),
            'activity_at'        => Yii::t('app', 'Activity At'),
        ];
    }

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
            ],
            'countable' => [
                'class' => CountableBehavior::className(),
                'counterOwner' => $this->module,
                'counterParam' => 'users_count',
            ],
        ];
    }

    /** @inheritdoc */
    public function scenarios()
    {
        return [
            'register' => ['username', 'email', 'password'],
            'connect'  => ['username', 'email'],
            'create'   => ['username', 'email', 'password'],
            'update'   => ['username', 'email', 'password'],
            'settings' => ['username', 'email', 'password'],
        ];
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            // username rules
            'usernameRequired' => ['username', 'required', 'on' => ['register', 'create', 'connect', 'update']],
            'usernameMatch'    => ['username', 'match', 'pattern' => static::$usernameRegexp],
            'usernameLength'   => ['username', 'string', 'min' => 3, 'max' => 32],
            'usernameUnique'   => ['username', 'unique',
                'message' => Yii::t(ModuleHelper::USERS, 'This username has already been taken')],
            'usernameTrim'     => ['username', 'trim'],

            // email rules
            'emailRequired' => ['email', 'required', 'on' => ['register', 'connect', 'create', 'update']],
            'emailPattern'  => ['email', 'email'],
            'emailLength'   => ['email', 'string', 'max' => 255],
            'emailUnique'   => ['email', 'unique',
                'message' => Yii::t(ModuleHelper::USERS, 'This email address has already been taken')],
            'emailTrim'     => ['email', 'trim'],

            // password rules
            'passwordRequired' => ['password', 'required', 'on' => ['register']],
            'passwordLength'   => ['password', 'string', 'min' => 6, 'on' => ['register', 'create']],
        ];
    }

    /** @inheritdoc */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Creates new user account. It generates password if it is not provided by user.
     *
     * @return bool
     */
    public function create()
    {
        if ($this->getIsNewRecord() == false) {
            throw new \RuntimeException('Calling "' . __CLASS__ . '::' . __METHOD__ . '" on existing user');
        }

        $this->confirmed_at = time();
        $this->password = $this->password == null ? Password::generate(8) : $this->password;

        $this->trigger(static::BEFORE_CREATE);

        if (!$this->save()) {
            return false;
        }

        $this->mailer->sendWelcomeMessage($this, null, true);
        $this->trigger(static::AFTER_CREATE);

        return true;
    }

    /**
     * This method is used to register new user account. If Module::enableConfirmation is set true, this method
     * will generate new confirmation token and use mailer to send it to the user.
     *
     * @return bool
     */
    public function register()
    {
        if ($this->getIsNewRecord() == false) {
            throw new \RuntimeException('Calling "' . __CLASS__ . '::' . __METHOD__ . '" on existing user');
        }

        $this->confirmed_at = $this->module->enableConfirmation ? null : time();
        $this->password     = $this->module->enableGeneratingPassword ? Password::generate(8) : $this->password;

        $this->trigger(static::BEFORE_REGISTER);

        if (!$this->save()) {
            return false;
        }

        if ($this->module->enableConfirmation) {
            /** @var Token $token */
            $token = Yii::createObject(['class' => Token::className(), 'type' => Token::TYPE_CONFIRMATION]);
            $token->link('user', $this);
        }

        $this->mailer->sendWelcomeMessage($this, isset($token) ? $token : null);
        $this->trigger(static::AFTER_REGISTER);

        return true;
    }

    /**
     * Attempts user confirmation.
     *
     * @param string $code Confirmation code.
     *
     * @return boolean
     */
    public function attemptConfirmation($code)
    {
        $token = $this->finder->findTokenByParams($this->id, $code, Token::TYPE_CONFIRMATION);

        if ($token instanceof Token && !$token->isExpired) {
            if ($token->delete()) {
                if (($success = $this->confirm())) {
                    Yii::$app->user->login($this, $this->module->rememberFor);
                }
                Yii::$app->session->setFlash('success', Yii::t(ModuleHelper::USERS, $success
                        ? 'Thank you, registration is now complete.'
                        : 'Something went wrong and your account has not been confirmed.'));
                return true;
            }
        }

        Yii::$app->session->setFlash('danger', Yii::t(
            'user',
            'The confirmation link is invalid or expired. Please try requesting a new one.'
        ));

        return false;
    }

    /**
     * This method attempts changing user email. If user's "unconfirmed_email" field is empty it returns false, else if
     * somebody already has email that equals user's "unconfirmed_email" it returns false, otherwise returns true and
     * updates user's password.
     *
     * @param string $code
     *
     * @return bool
     * @throws \Exception
     */
    public function attemptEmailChange($code)
    {
        /** @var Token $token */
        $token = $this->finder->findToken([
            'user_id' => $this->id,
            'code'    => $code,
        ])->andWhere(['in', 'type', [Token::TYPE_CONFIRM_NEW_EMAIL, Token::TYPE_CONFIRM_OLD_EMAIL]])->one();

        if (empty($this->unconfirmed_email) || $token === null || $token->isExpired) {
            Yii::$app->session->setFlash('danger', Yii::t(ModuleHelper::USERS, 'Your confirmation token is invalid or expired'));
            return;
        }

        $token->delete();

        if (empty($this->unconfirmed_email)) {
            Yii::$app->session->setFlash('danger', Yii::t(ModuleHelper::USERS, 'An error occurred processing your request'));
            return;
        }

        if ($this->finder->findUser(['email' => $this->unconfirmed_email])->exists()) {
            return;
        }

        if ($this->module->emailChangeStrategy == Module::STRATEGY_SECURE) {
            switch ($token->type) {
                case Token::TYPE_CONFIRM_NEW_EMAIL:
                    $this->flags |= static::NEW_EMAIL_CONFIRMED;
                    Yii::$app->session->setFlash(
                        'success',
                        Yii::t(ModuleHelper::USERS, 'Awesome, almost there.'
                        . ' Now you need to click the confirmation link sent to your old email address')
                    );
                    break;
                case Token::TYPE_CONFIRM_OLD_EMAIL:
                    $this->flags |= static::OLD_EMAIL_CONFIRMED;
                    Yii::$app->session->setFlash(
                        'success',
                        Yii::t(ModuleHelper::USERS, 'Awesome, almost there.'
                        . ' Now you need to click the confirmation link sent to your new email address')
                    );
                    break;
            }
        }

        if ($this->module->emailChangeStrategy == Module::STRATEGY_DEFAULT
            || ($this->flags & static::NEW_EMAIL_CONFIRMED && $this->flags & static::OLD_EMAIL_CONFIRMED)) {
            $this->email = $this->unconfirmed_email;
            $this->unconfirmed_email = null;
            Yii::$app->session->setFlash('success', Yii::t(ModuleHelper::USERS, 'Your email address has been changed'));
        }

        $this->save(false);
    }

    /**
     * Confirms the user by setting 'confirmed_at' field to current time.
     */
    public function confirm()
    {
        return (bool)$this->updateAttributes(['confirmed_at' => time()]);
    }

    /**
     * Resets password.
     *
     * @param string $password
     *
     * @return bool
     */
    public function resetPassword($password)
    {
        return (bool)$this->updateAttributes(['password_hash' => Password::hash($password)]);
    }

    /**
     * Blocks the user by setting 'blocked_at' field to current time and regenerates auth_key.
     */
    public function block()
    {
        return (bool)$this->updateAttributes([
            'blocked_at' => time(),
            'auth_key'   => Yii::$app->security->generateRandomString(),
        ]);
    }

    /**
     * UnBlocks the user by setting 'blocked_at' field to null.
     */
    public function unblock()
    {
        return (bool)$this->updateAttributes(['blocked_at' => null]);
    }

    /**
     * Generates new username based on email address, or creates new username
     * like "user1".
     */
    public function generateUsername()
    {
        // try to use name part of email
        $this->username = explode('@', $this->email)[0];
        if ($this->validate(['username'])) {
            return $this->username;
        }

        // generate username like "user1", "user2", etc...
        while (!$this->validate(['username'])) {
            $row = (new Query())
                ->from('$this->tableName()')
                ->select('MAX(id) as id')
                ->one();

            $this->username = 'user' . ++$row['id'];
        }

        return $this->username;
    }

    /** @inheritdoc */
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->setAttribute('auth_key', Yii::$app->security->generateRandomString());
            if (Yii::$app instanceof WebApplication) {
                $this->setAttribute('registration_ip', Yii::$app->request->userIP);
            }
        }

        if (!empty($this->password)) {
            $this->setAttribute('password_hash', Password::hash($this->password));
        }

        return parent::beforeSave($insert);
    }

    /** @inheritdoc */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            if ($this->profile == null) {
                $this->profile = Yii::createObject(Profile::className());
            }
            $this->profile->link('user', $this);
        }
    }

    /** @inheritdoc */
    public static function tableName()
    {
        return '{{%users}}';
    }

    /** @inheritdoc */
    public static function findIdentity($id)
    {
        $identity = Yii::$container->get(static::className());
        $indentityData = static::find()->andWhere(['=', 'id', $id])->asArray()->one();

        if (is_null($indentityData)) {
            return null;
        }

        static::populateRecord($identity, $indentityData);
        $identity->afterFind();

        return $identity;
    }

    /** @inheritdoc */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('Method "' . __CLASS__ . '::' . __METHOD__ . '" is not implemented.');
    }
}
