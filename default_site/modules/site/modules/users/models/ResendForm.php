<?php

namespace site\modules\users\models;

use Yii;
use yii\base\Model;
use app\helpers\ModuleHelper;
use app\helpers\RouteHelper;
use site\components\validators\CaptchaValidator;
use site\modules\users\Finder;
use site\modules\users\Mailer;

class ResendForm extends Model
{
    /** @var string */
    public $email;

    /** @var string */
    public $captcha;

    /** @var User */
    private $user;

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

    /**
     * @return User
     */
    public function getUser()
    {
        if ($this->user === null) {
            $this->user = $this->finder->findUserByEmail($this->email);
        }

        return $this->user;
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            'emailRequired' => ['email', 'required'],
            'emailPattern' => ['email', 'email'],
            'emailExist' => ['email', 'exist', 'targetClass' => User::className()],
            'emailConfirmed' => ['email', function () {
                if ($this->getUser() != null && $this->getUser()->getIsConfirmed()) {
                    $this->addError('email', Yii::t(ModuleHelper::USERS, 'This account has already been confirmed'));
                }
            }],
            'captchaRequired' => ['captcha', 'required'],
            'captchaValidation' => ['captcha', CaptchaValidator::className(),
                'captchaAction' => RouteHelper::SITE_CAPTCHA],
        ];
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t(ModuleHelper::USERS, 'Email'),
            'captcha' => Yii::t(ModuleHelper::SITE, 'Captcha'),
        ];
    }

    /** @inheritdoc */
    public function formName()
    {
        return 'resend-form';
    }

    /**
     * Creates new confirmation token and sends it to the user.
     *
     * @return bool
     */
    public function resend()
    {
        if (!$this->validate()) {
            return false;
        }
        /** @var Token $token */
        $token = Yii::createObject([
            'class'   => Token::className(),
            'user_id' => $this->getUser()->id,
            'type'    => Token::TYPE_CONFIRMATION,
        ]);
        $token->save(false);
        $this->mailer->sendConfirmationMessage($this->getUser(), $token);
        Yii::$app->session->setFlash(
            'info',
            Yii::t(ModuleHelper::USERS, 'A message has been sent to your email address.'
            . ' It contains a confirmation link that you must click to complete registration.')
        );

        return true;
    }
}
