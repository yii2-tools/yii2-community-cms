<?php

namespace site\modules\users\models;

use Yii;
use yii\base\Model;
use app\helpers\ModuleHelper;
use app\helpers\RouteHelper;
use site\components\validators\CaptchaValidator;
use site\modules\users\Module;

/**
 * Registration form collects user input on registration process, validates it and creates new User model.
 */
class RegistrationForm extends Model
{
    /**
     * @var string User email address
     */
    public $email;

    /**
     * @var string Username
     */
    public $username;

    /**
     * @var string Password
     */
    public $password;

    /**
     * @var string Confirm password
     */
    public $password_repeat;

    /**
     * @var string Captcha
     */
    public $captcha;

    /**
     * @var Module
     */
    protected $module;

    const EVENT_ATTEMPT_REGISTER = 'attemptRegister';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->module = Yii::$app->getModule(ModuleHelper::USERS);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username rules
            'usernameLength'   => ['username', 'string', 'min' => 3, 'max' => 255],
            'usernameTrim'     => ['username', 'filter', 'filter' => 'trim'],
            'usernamePattern'  => ['username', 'match', 'pattern' => User::$usernameRegexp],
            'usernameRequired' => ['username', 'required'],
            'usernameUnique'   => [
                'username',
                'unique',
                'targetClass' => User::className(),
                'message' => Yii::t(ModuleHelper::USERS, 'This username has already been taken')
            ],
            // email rules
            'emailTrim'     => ['email', 'filter', 'filter' => 'trim'],
            'emailRequired' => ['email', 'required'],
            'emailPattern'  => ['email', 'email'],
            'emailUnique'   => [
                'email',
                'unique',
                'targetClass' => User::className(),
                'message' => Yii::t(ModuleHelper::USERS, 'This email address has already been taken')
            ],
            // password rules
            'passwordRequired' => ['password', 'required',
                'skipOnEmpty' => $this->module->enableGeneratingPassword],
            'passwordLength'   => ['password', 'string', 'min' => 6],
            'passwordRepeatRequired' => ['password_repeat', 'required',
                'skipOnEmpty' => $this->module->enableGeneratingPassword],
            'passwordRepeatLength' => ['password_repeat', 'string', 'min' => 6],
            'passwordRepeat' => ['password_repeat', 'compare', 'compareAttribute' => 'password'],
            // captcha rules
            'captchaRequired' => ['captcha', 'required'],
            'captchaValidation' => ['captcha', CaptchaValidator::className(),
                'captchaAction' => RouteHelper::SITE_CAPTCHA],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t(ModuleHelper::USERS, 'Email'),
            'username' => Yii::t(ModuleHelper::USERS, 'Username'),
            'password' => Yii::t(ModuleHelper::USERS, 'Password'),
            'password_repeat' => Yii::t(ModuleHelper::USERS, 'Confirm password'),
            'captcha' => Yii::t(ModuleHelper::SITE, 'Captcha'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 'register-form';
    }

    /**
     * Registers a new user account. If registration was successful it will set flash message.
     *
     * @return bool
     */
    public function register()
    {
        if (!$this->validate()) {
            return false;
        }

        /** @var User $user */
        $user = Yii::createObject(User::className());
        $user->setScenario('register');
        $this->loadAttributes($user);

        if (!$user->register()) {
            return false;
        }

        Yii::$app->session->setFlash(
            'info',
            Yii::t(
                ModuleHelper::USERS,
                'Your account has been created and a message with further instructions has been sent to your email'
            )
        );

        return true;
    }

    /**
     * Loads attributes to the user model. You should override this method if you are going to add new fields to the
     * registration form. You can read more in special guide.
     *
     * By default this method set all attributes of this model to the attributes of User model, so you should properly
     * configure safe attributes of your User model.
     *
     * @param User $user
     */
    protected function loadAttributes(User $user)
    {
        $user->setAttributes($this->attributes);
    }
}
