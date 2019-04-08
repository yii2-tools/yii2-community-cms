<?php

namespace admin\modules\users\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use site\modules\users\Finder;

/**
 * UserSearch represents the model behind the search form about User.
 */
class UserSearch extends Model
{
    /** @var string */
    public $username;

    /** @var string */
    public $email;

    /** @var string */
    public $created_at;

    /** @var string */
    public $registration_ip;

    /** @var string */
    public $confirmed_at;

    /** @var string */
    public $blocked_at;

    /** @var Finder */
    protected $finder;

    /**
     * @param Finder $finder
     * @param array  $config
     */
    public function __construct(Finder $finder, $config = [])
    {
        $this->finder = $finder;
        parent::__construct($config);
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            'fieldsSafe' => [
                ['username', 'email', 'registration_ip', 'created_at', 'confirmed_at', 'blocked_at'], 'safe'
            ],
            'timestampDefault' => [
                ['created_at', 'confirmed_at', 'blocked_at'], 'default',
                'value' => null
            ],
        ];
    }

    /**
     * @param $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = $this->finder->getUserQuery();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        if (!empty($this->created_at)) {
            $date = strtotime($this->created_at);
            $query->andFilterWhere(['between', 'created_at', $date, $date + 3600 * 24]);
        }

        if (!empty($this->confirmed_at)) {
            $isNotConfirmed = ['is', 'confirmed_at', new Expression('NULL')];
            $query->andFilterWhere($this->confirmed_at == 1 ? ['not', $isNotConfirmed] : $isNotConfirmed);
        }

        if (!empty($this->blocked_at)) {
            $isBlocked = ['is', 'blocked_at', new Expression('NULL')];
            $query->andFilterWhere($this->blocked_at == 1 ? ['not', $isBlocked] : $isBlocked);
        }

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['registration_ip' => $this->registration_ip]);

        return $dataProvider;
    }
}
