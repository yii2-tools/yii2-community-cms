<?php

namespace site\modules\users\models;

use Yii;
use yii\base\Model;
use app\helpers\ModuleHelper;
use app\helpers\RouteHelper;
use site\components\validators\CaptchaValidator;
use site\modules\users\Finder;
use site\modules\users\Mailer;

class RecoveryForm extends Model
{
    /** @var string */
    public $email;

    /** @var string */
    public $password;

    /** @var string */
    public $password_repeat;

    /** @var string */
    public $captcha;

    /** @var User */
    protected $user;

    /** @var \site\modules\users\Module */
    protected $module;

    /** @var Mailer */
    protected $mailer;

    /** @var Finder */
    protected $finder;

    /**
     * @param Mailer $mailer
     * @param Finder $finder
     * @param array  $config
     */
    public function __construct(Mailer $mailer, Finder $finder, $config = [])
    {
        $this->module = Yii::$app->getModule(ModuleHelper::USERS);
        $this->mailer = $mailer;
        $this->finder = $finder;
        parent::__construct($config);
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'email'    => Yii::t(ModuleHelper::USERS, 'Email'),
            'password' => Yii::t(ModuleHelper::USERS, 'Password'),
            'password_repeat' => Yii::t(ModuleHelper::USERS, 'Confirm password'),
            'captcha' => Yii::t(ModuleHelper::SITE, 'Captcha'),
        ];
    }

    /** @inheritdoc */
    public function scenarios()
    {
        return [
            'request' => ['email', 'captcha'],
            'reset'   => ['password', 'password_repeat'],
        ];
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            'emailTrim' => ['email', 'filter', 'filter' => 'trim'],
            'emailRequired' => ['email', 'required'],
            'emailPattern' => ['email', 'email'],
            'emailExist' => [
                'email',
                'exist',
                'targetClass' => User::className(),
                'message' => Yii::t(ModuleHelper::USERS, 'There is no user with this email address'),
            ],
            'emailUnconfirmed' => [
                'email',
                function ($attribute) {
                    $this->user = $this->finder->findUserByEmail($this->email);
                    if ($this->user !== null && $this->module->enableConfirmation && !$this->user->getIsConfirmed()) {
                        $this->addError($attribute, Yii::t(ModuleHelper::USERS, 'You need to confirm your email address'));
                    }
                }
            ],
            'passwordRequired' => ['password', 'required'],
            'passwordLength' => ['password', 'string', 'min' => 6],
            'passwordRepeatRequired' => ['password_repeat', 'required'],
            'passwordRepeatLength' => ['password_repeat', 'string', 'min' => 6],
            'passwordRepeat' => ['password_repeat', 'compare', 'compareAttribute' => 'password'],
            'captchaRequired' => ['captcha', 'required'],
            'captchaValidation' => ['captcha', CaptchaValidator::className(),
                'captchaAction' => RouteHelper::SITE_CAPTCHA],
        ];
    }

    /**
     * Sends recovery message.
     *
     * @return bool
     */
    public function sendRecoveryMessage()
    {
        if ($this->validate()) {
            /** @var Token $token */
            $token = Yii::createObject([
                'class'   => Token::className(),
                'user_id' => $this->user->id,
                'type'    => Token::TYPE_RECOVERY,
            ]);
            $token->save(false);
            $this->mailer->sendRecoveryMessage($this->user, $token);
            Yii::$app->session->setFlash(
                'info',
                Yii::t(ModuleHelper::USERS, 'An email has been sent with instructions for resetting your password')
            );

            return true;
        }

        return false;
    }

    /**
     * Resets user's password.
     *
     * @param Token $token
     *
     * @return bool
     */
    public function resetPassword(Token $token)
    {
        if (!$this->validate() || $token->user === null) {
            return false;
        }

        if ($token->user->resetPassword($this->password)) {
            Yii::$app->session->setFlash('success', Yii::t(ModuleHelper::USERS, 'Your password has been changed successfully.'));
            return $token->delete() !== false;
        }

        Yii::$app->session->setFlash(
            'danger',
            Yii::t(ModuleHelper::USERS, 'An error occurred and your password has not been changed. Please try again later.')
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 'recovery-form';
    }
}
