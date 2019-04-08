<?php

namespace site\modules\users;

use Yii;
use yii\base\Component;
use site\modules\design\helpers\ModuleHelper;
use site\modules\users\models\Token;
use site\modules\users\models\User;

class Mailer extends Component
{
    /** @var string */
    public $viewPath = '@site/modules/users/views/mail';

    /** @var string|array Default: `Yii::$app->params['admin_email']` OR `no-reply@example.com` */
    public $sender;

    /** @var string */
    protected $welcomeSubject;

    /** @var string */
    protected $confirmationSubject;

    /** @var string */
    protected $reconfirmationSubject;

    /** @var string */
    protected $recoverySubject;

    /** @var \site\modules\users\Module */
    protected $module;

    /**
     * @return string
     */
    public function getWelcomeSubject()
    {
        if ($this->welcomeSubject == null) {
            $this->setWelcomeSubject(
                Yii::t(
                    ModuleHelper::USERS,
                    'Welcome - {0}',
                    Yii::$app->getModule(ModuleHelper::DESIGN)->params['title']
                )
            );
        }

        return $this->welcomeSubject;
    }

    /**
     * @param string $welcomeSubject
     */
    public function setWelcomeSubject($welcomeSubject)
    {
        $this->welcomeSubject = $welcomeSubject;
    }

    /**
     * @return string
     */
    public function getConfirmationSubject()
    {
        if ($this->confirmationSubject == null) {
            $this->setConfirmationSubject(
                Yii::t(
                    ModuleHelper::USERS,
                    'Confirm account - {0}',
                    Yii::$app->getModule(ModuleHelper::DESIGN)->params['title']
                )
            );
        }

        return $this->confirmationSubject;
    }

    /**
     * @param string $confirmationSubject
     */
    public function setConfirmationSubject($confirmationSubject)
    {
        $this->confirmationSubject = $confirmationSubject;
    }

    /**
     * @return string
     */
    public function getReconfirmationSubject()
    {
        if ($this->reconfirmationSubject == null) {
            $this->setReconfirmationSubject(
                Yii::t(
                    ModuleHelper::USERS,
                    'Confirm email change - {0}',
                    Yii::$app->getModule(ModuleHelper::DESIGN)->params['title']
                )
            );
        }

        return $this->reconfirmationSubject;
    }

    /**
     * @param string $reconfirmationSubject
     */
    public function setReconfirmationSubject($reconfirmationSubject)
    {
        $this->reconfirmationSubject = $reconfirmationSubject;
    }

    /**
     * @return string
     */
    public function getRecoverySubject()
    {
        if ($this->recoverySubject == null) {
            $this->setRecoverySubject(
                Yii::t(
                    ModuleHelper::USERS,
                    'Complete password reset - {0}',
                    Yii::$app->getModule(ModuleHelper::DESIGN)->params['title']
                )
            );
        }

        return $this->recoverySubject;
    }

    /**
     * @param string $recoverySubject
     */
    public function setRecoverySubject($recoverySubject)
    {
        $this->recoverySubject = $recoverySubject;
    }

    /** @inheritdoc */
    public function init()
    {
        $this->module = Yii::$app->getModule(ModuleHelper::USERS);

        parent::init();
    }

    /**
     * Sends an email to a user after registration.
     *
     * @param User  $user
     * @param Token $token
     * @param bool  $showPassword
     *
     * @return bool
     */
    public function sendWelcomeMessage(User $user, Token $token = null, $showPassword = false)
    {
        return $this->sendMessage(
            $user->email,
            $this->getWelcomeSubject(),
            'welcome',
            ['user' => $user, 'token' => $token, 'module' => $this->module, 'showPassword' => $showPassword]
        );
    }

    /**
     * Sends an email to a user with confirmation link.
     *
     * @param User  $user
     * @param Token $token
     *
     * @return bool
     */
    public function sendConfirmationMessage(User $user, Token $token)
    {
        return $this->sendMessage(
            $user->email,
            $this->getConfirmationSubject(),
            'confirmation',
            ['user' => $user, 'token' => $token]
        );
    }

    /**
     * Sends an email to a user with reconfirmation link.
     *
     * @param User  $user
     * @param Token $token
     *
     * @return bool
     */
    public function sendReconfirmationMessage(User $user, Token $token)
    {
        if ($token->type == Token::TYPE_CONFIRM_NEW_EMAIL) {
            $email = $user->unconfirmed_email;
        } else {
            $email = $user->email;
        }

        return $this->sendMessage(
            $email,
            $this->getReconfirmationSubject(),
            'reconfirmation',
            ['user' => $user, 'token' => $token]
        );
    }

    /**
     * Sends an email to a user with recovery link.
     *
     * @param User  $user
     * @param Token $token
     *
     * @return bool
     */
    public function sendRecoveryMessage(User $user, Token $token)
    {
        return $this->sendMessage(
            $user->email,
            $this->getRecoverySubject(),
            'recovery',
            ['user' => $user, 'token' => $token]
        );
    }

    /**
     * @param string $to
     * @param string $subject
     * @param string $view
     * @param array  $params
     *
     * @return bool
     */
    protected function sendMessage($to, $subject, $view, $params = [])
    {
        $designModule = Yii::$app->getModule(ModuleHelper::DESIGN);
        Yii::$app->getModule(ModuleHelper::DESIGN_PACKS);

        /** @var \yii\mail\BaseMailer $mailer */
        $mailer = Yii::$app->mailer;
        $mailer->viewPath = '@templates/users/mail';
        $viewObj = $designModule->getView();
        $viewObj->theme->pathMap[$mailer->viewPath] = [
            '@design_pack/templates/users/mail',
            '@design_packs_dir/default/templates/users/mail',
        ];
        $mailer->setView($viewObj);

        $ext = $designModule->params['view_extension'];
        if (!pathinfo($mailer->htmlLayout, PATHINFO_EXTENSION)) {
            $mailer->htmlLayout = $mailer->htmlLayout . '.' . $ext;
            $mailer->textLayout = $mailer->textLayout . '.' . $ext;
        }
        $view .= '.' . $ext;

        if ($this->sender === null) {
            $this->sender = isset(Yii::$app->params['admin_email'])
                ? Yii::$app->params['admin_email']
                : 'no-reply@example.com';
        }

        return $mailer->compose(['html' => $view, 'text' => 'text/' . $view], $params)
            ->setTo($to)
            ->setFrom($this->sender)
            ->setSubject($subject)
            ->send();
    }
}
