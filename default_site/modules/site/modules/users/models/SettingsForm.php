<?php

namespace site\modules\users\models;

use Yii;
use yii\base\Model;
use app\helpers\ModuleHelper;
use site\modules\users\helpers\Password;
use site\modules\users\Mailer;
use site\modules\users\Module;

class SettingsForm extends Model
{
    /** @var string */
    public $email;

    /** @var string */
    public $username;

    /** @var string */
    public $new_password;

    /** @var string */
    public $current_password;

    /** @var Module */
    protected $module;

    /** @var Mailer */
    protected $mailer;

    /** @var User */
    private $user;

    /** @return User */
    public function getUser()
    {
        if ($this->user == null) {
            $this->user = Yii::$app->user->identity;
        }

        return $this->user;
    }

    /** @inheritdoc */
    public function __construct(Mailer $mailer, $config = [])
    {
        $this->mailer = $mailer;
        $this->module = Yii::$app->getModule(ModuleHelper::USERS);
        $this->setAttributes([
            'username' => $this->getUser()->username,
            'email'    => $this->getUser()->unconfirmed_email ?: $this->getUser()->email,
        ], false);
        parent::__construct($config);
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            'usernameRequired' => ['username', 'required'],
            'usernameTrim' => ['username', 'filter', 'filter' => 'trim'],
            'usernameLength'   => ['username', 'string', 'min' => 3, 'max' => 32],
            'usernamePattern' => ['username', 'match', 'pattern' => User::$usernameRegexp],
            'emailRequired' => ['email', 'required'],
            'emailTrim' => ['email', 'filter', 'filter' => 'trim'],
            'emailPattern' => ['email', 'email'],
            'emailUsernameUnique' => [['email', 'username'], 'unique', 'when' => function ($model, $attribute) {
                return $this->getUser()->$attribute != $model->$attribute;
            }, 'targetClass' => User::className()],
            'newPasswordLength' => ['new_password', 'string', 'min' => 6],
            'currentPasswordRequired' => ['current_password', 'required'],
            'currentPasswordValidate' => ['current_password', function ($attr) {
                if (!Password::validate($this->$attr, $this->getUser()->password_hash)) {
                    $this->addError($attr, Yii::t(ModuleHelper::USERS, 'Current password is not valid'));
                }
            }],
        ];
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'email'            => Yii::t(ModuleHelper::USERS, 'Email'),
            'username'         => Yii::t(ModuleHelper::USERS, 'Username'),
            'new_password'     => Yii::t(ModuleHelper::USERS, 'New password'),
            'current_password' => Yii::t(ModuleHelper::USERS, 'Current password'),
        ];
    }

    /** @inheritdoc */
    public function formName()
    {
        return 'settings-form';
    }

    /**
     * Saves new account settings.
     *
     * @return bool
     */
    public function save()
    {
        if ($this->validate()) {
            $this->getUser()->scenario = 'settings';
            $this->getUser()->username = $this->username;
            $this->getUser()->password = $this->new_password;
            if ($this->email == $this->getUser()->email && $this->getUser()->unconfirmed_email != null) {
                $this->getUser()->unconfirmed_email = null;
            } elseif ($this->email != $this->getUser()->email) {
                switch ($this->module->emailChangeStrategy) {
                    case Module::STRATEGY_INSECURE:
                        $this->insecureEmailChange();
                        break;
                    case Module::STRATEGY_DEFAULT:
                        $this->defaultEmailChange();
                        break;
                    case Module::STRATEGY_SECURE:
                        $this->secureEmailChange();
                        break;
                    default:
                        throw new \OutOfBoundsException('Invalid email changing strategy');
                }
            }

            return $this->getUser()->save();
        }

        return false;
    }

    /**
     * Changes user's email address to given without any confirmation.
     */
    protected function insecureEmailChange()
    {
        $this->getUser()->email = $this->email;
        Yii::$app->session->setFlash('success', Yii::t(ModuleHelper::USERS, 'Your email address has been changed'));
    }

    /**
     * Sends a confirmation message to user's email address with link to confirm changing of email.
     */
    protected function defaultEmailChange()
    {
        $this->getUser()->unconfirmed_email = $this->email;
        /** @var Token $token */
        $token = Yii::createObject([
            'class'   => Token::className(),
            'user_id' => $this->getUser()->id,
            'type'    => Token::TYPE_CONFIRM_NEW_EMAIL,
        ]);
        $token->save(false);
        $this->mailer->sendReconfirmationMessage($this->getUser(), $token);
        Yii::$app->session->setFlash(
            'info',
            Yii::t(ModuleHelper::USERS, 'A confirmation message has been sent to your new email address')
        );
    }

    /**
     * Sends a confirmation message to both old and new email addresses with link to confirm changing of email.
     *
     * @throws \yii\base\InvalidConfigException
     */
    protected function secureEmailChange()
    {
        $this->defaultEmailChange();
        /** @var Token $token */
        $token = Yii::createObject([
            'class'   => Token::className(),
            'user_id' => $this->getUser()->id,
            'type'    => Token::TYPE_CONFIRM_OLD_EMAIL,
        ]);
        $token->save(false);
        $this->mailer->sendReconfirmationMessage($this->getUser(), $token);

        // unset flags if they exist
        $this->getUser()->flags &= ~User::NEW_EMAIL_CONFIRMED;
        $this->getUser()->flags &= ~User::OLD_EMAIL_CONFIRMED;
        $this->getUser()->save(false);

        Yii::$app->session->setFlash(
            'info',
            Yii::t(ModuleHelper::USERS, 'We have sent confirmation links to both old and new email addresses.'
            . ' You must click both links to complete your request')
        );
    }
}
