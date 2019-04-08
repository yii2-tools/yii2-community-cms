<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 25.01.16 18:34
 */

namespace site\components;

use Yii;
use yii\base\Component;
use app\helpers\SessionHelper;

class Captcha extends Component
{
    const EVENT_CAPTCHA_ENABLED = 'captchaEnabled';
    const EVENT_CAPTCHA_DISABLED = 'captchaDisabled';

    /**
     * @var integer  number of application components which require captcha
     */
    protected $required_counter = 0;

    /**
     * @var integer  timestamp, until captcha considered as valid
     */
    protected $expire = 0;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->required_counter = Yii::$app->session->get(SessionHelper::CAPTCHA_REQUIRED, 0);
        $this->expire = Yii::$app->session->get(SessionHelper::CAPTCHA_EXPIRE, 0);
    }

    /**
     * Checks if captcha required.
     *
     * @return bool
     */
    public function isRequired()
    {
        return $this->required_counter > 0;
    }

    /**
     * Checks if captcha expired.
     *
     * @return bool
     */
    public function isExpired()
    {
        return $this->expire < time();
    }

    /**
     * Set state for captcha checking system.
     *
     * @param $object           object-initiator
     * @param string $reason    additional info about action
     * @param bool $force       ignoring expire time
     */
    public function enableBy($object, $reason = 'not specified', $force = false)
    {
        Yii::trace('Captcha enable requested by: ' . get_class($object) . PHP_EOL
            . 'Reason: ' . $reason . PHP_EOL . 'Force: ' . ($force ? 'true' : 'false'), __METHOD__);

        if (!$force && !$this->isExpired()) {
            Yii::info('Captcha not enabled. Acceptable time has not expired.', __METHOD__);
            return;
        } else {
            $this->expire = 0;
            Yii::$app->session->remove(SessionHelper::CAPTCHA_EXPIRE);
        }

        Yii::info('Captcha enabled.', __METHOD__);
        Yii::$app->session->set(SessionHelper::CAPTCHA_REQUIRED, $this->required_counter = 1);

        $this->trigger(self::EVENT_CAPTCHA_ENABLED);
    }

    /**
     * Set state for captcha checking system
     *
     * @param $object       object-initiator
     * @param int $expire   timestamp, seconds
     */
    public function disableBy($object, $expire = 60)
    {
        Yii::trace('Captcha disabled by: ' . get_class($object) . PHP_EOL . 'Expire: ' . $expire, __METHOD__);

        $this->required_counter = 0;
        Yii::$app->session->remove(SessionHelper::CAPTCHA_REQUIRED);
        Yii::$app->session->set(SessionHelper::CAPTCHA_EXPIRE, $this->expire = time() + $expire);

        $this->trigger(self::EVENT_CAPTCHA_DISABLED);
    }
}
