<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ScheduleTrip;

/**
 * ScheduleTripSearch represents the model behind the search form about `app\models\ScheduleTrip`.
 */
class ScheduleTripSearch extends ScheduleTrip
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'schedule_id', ], 'integer'],
            [['name', 'start_time', 'mid_time', 'end_time'], 'safe'],
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
    public function search($params, $schedule_id = '')
    {
        $query = ScheduleTrip::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);

        $dataProvider->setSort([
            'defaultOrder' => ['start_time' => SORT_ASC],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if(!empty($schedule_id)) {
            $query->andFilterWhere([
                'schedule_id' => $schedule_id
            ]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            //'schedule_id' => $this->schedule_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'start_time', $this->start_time])
            ->andFilterWhere(['like', 'mid_time', $this->mid_time])
            ->andFilterWhere(['like', 'end_time', $this->end_time]);

        return $dataProvider;
    }
}
