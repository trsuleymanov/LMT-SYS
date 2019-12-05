<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Trip;

/**
 * TripSearch represents the model behind the search form about `app\models\Trip`.
 */
class TripSearch extends Trip
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'direction_id', 'date_start_sending', 'start_sending_user_id', 'date_sended', 'sended_user_id',
                'created_at', 'updated_at', 'issued_by_operator_id', 'date_issued_by_operator', 'has_free_places',
                'is_reserv'], 'integer'],
            [['name', 'date', 'start_time', 'mid_time', 'end_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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
        $query = Trip::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
            //'date' => $this->date,
            'direction_id' => $this->direction_id,
            'date_start_sending' => $this->date_start_sending,
            'start_sending_user_id' => $this->start_sending_user_id,
            'date_sended' => $this->date_sended,
            'sended_user_id' => $this->sended_user_id,
            'commercial' => $this->commercial,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'date_issued_by_operator' => $this->date_issued_by_operator,
            'issued_by_operator_id' => $this->issued_by_operator_id,
            'has_free_places' => $this->has_free_places,
            'is_reserv' => $this->is_reserv,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'start_time', $this->start_time])
            ->andFilterWhere(['like', 'mid_time', $this->mid_time])
            ->andFilterWhere(['like', 'end_time', $this->end_time]);

        //echo "date__=".$this->date.'<br />';

        if (!empty($this->date)) {
            $date = strtotime($this->date);
            $query->andFilterWhere(['<', $this->tableName().'.date', $date + 86400]);
            $query->andFilterWhere(['>=', $this->tableName().'.date', $date]);
        }

        return $dataProvider;
    }
}
