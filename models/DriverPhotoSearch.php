<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\DriverPhoto;

/**
 * DriverPhotoSearch represents the model behind the search form of `app\models\DriverPhoto`.
 */
class DriverPhotoSearch extends DriverPhoto
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'transport_id', 'driver_id'], 'integer'],
            [['photo_link', 'time_loading_finish', 'photo_created_on_mobile', 'transport_car_reg'], 'safe'],
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
        $query = DriverPhoto::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->session->get('table-rows', 20),
            ],
        ]);

        $dataProvider->setSort([
            'defaultOrder' => [
                'photo_created_on_mobile' => SORT_DESC
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            //exit('wer');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'driver_id' => $this->driver_id,
            'transport_id' => $this->transport_id,
            //'time_loading_finish' => $this->time_loading_finish,
            //'photo_created_on_mobile' => $this->photo_created_on_mobile,
        ]);

        $query
            ->andFilterWhere(['like', 'photo_link', $this->photo_link])
            ->andFilterWhere(['like', 'transport_car_reg', $this->transport_car_reg]);

        if (!is_null($this->photo_created_on_mobile) && strpos($this->photo_created_on_mobile, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->photo_created_on_mobile);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName() . '.photo_created_on_mobile', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }

        if (!is_null($this->time_loading_finish) && strpos($this->time_loading_finish, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->time_loading_finish);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName() . '.time_loading_finish', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }

        return $dataProvider;
    }
}
