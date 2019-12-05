<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\OperatorBeelineSubscription;

/**
 * OperatorBeelineSubscriptionSearch represents the model behind the search form of `app\models\OperatorBeelineSubscription`.
 */
class OperatorBeelineSubscriptionSearch extends OperatorBeelineSubscription
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'operator_id', 'expire_at', 'minutes'], 'integer'],
            [['subscription_id', 'mobile_ats_login', 'status', 'name'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = OperatorBeelineSubscription::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->session->get('table-rows', 20)
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'operator_id' => $this->operator_id,
            'expire_at' => $this->expire_at,
            'status' => $this->status,
            'minutes' => $this->minutes,

        ]);

        $query->andFilterWhere(['like', 'subscription_id', $this->subscription_id])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'mobile_ats_login', $this->mobile_ats_login]);

        return $dataProvider;
    }
}
