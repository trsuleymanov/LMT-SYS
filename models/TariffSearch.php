<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Tariff;

/**
 * TariffSearch represents the model behind the search form about `app\models\Tariff`.
 */
class TariffSearch extends Tariff
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
//            [['id', 'commercial'], 'integer'],
//            [['common_price', 'student_price', 'baby_price', 'aero_price', 'parcel_price', 'loyal_price'], 'number'],
//            [['start_date'], 'safe']

            [['id', 'commercial'], 'integer'],
            [[
                'unprepayment_common_price', 'unprepayment_student_price', 'unprepayment_baby_price',
                'unprepayment_aero_price', 'unprepayment_parcel_price', 'unprepayment_loyal_price', 'unprepayment_reservation_cost',

                'prepayment_common_price', 'prepayment_student_price', 'prepayment_baby_price',
                'prepayment_aero_price', 'prepayment_parcel_price', 'prepayment_loyal_price', 'prepayment_reservation_cost',

                'superprepayment_common_price', 'superprepayment_student_price', 'superprepayment_baby_price',
                'superprepayment_aero_price', 'superprepayment_parcel_price', 'superprepayment_loyal_price', 'superprepayment_reservation_cost',

            ], 'number'],
            [['start_date'], 'safe']
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
        $query = Tariff::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);

        $dataProvider->setSort([
            'defaultOrder' => ['start_date' => SORT_DESC],
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
            'start_date' => $this->start_date,
//            'common_price' => $this->common_price,
//            'student_price' => $this->student_price,
//            'baby_price' => $this->baby_price,
//            'aero_price' => $this->aero_price,
//            'parcel_price' => $this->parcel_price,
//            'loyal_price' => $this->loyal_price,

            'unprepayment_common_price' => $this->unprepayment_common_price,
            'unprepayment_student_price' => $this->unprepayment_student_price,
            'unprepayment_baby_price' => $this->unprepayment_baby_price,
            'unprepayment_aero_price' => $this->unprepayment_aero_price,
            'unprepayment_parcel_price' => $this->unprepayment_parcel_price,
            'unprepayment_loyal_price' => $this->unprepayment_loyal_price,
            'unprepayment_reservation_cost' => $this->unprepayment_reservation_cost,

            'prepayment_common_price' => $this->prepayment_common_price,
            'prepayment_student_price' => $this->prepayment_student_price,
            'prepayment_baby_price' => $this->prepayment_baby_price,
            'prepayment_aero_price' => $this->prepayment_aero_price,
            'prepayment_parcel_price' => $this->prepayment_parcel_price,
            'prepayment_loyal_price' => $this->prepayment_loyal_price,
            'prepayment_reservation_cost' => $this->prepayment_reservation_cost,

            'superprepayment_common_price' => $this->superprepayment_common_price,
            'superprepayment_student_price' => $this->superprepayment_student_price,
            'superprepayment_baby_price' => $this->superprepayment_baby_price,
            'superprepayment_aero_price' => $this->superprepayment_aero_price,
            'superprepayment_parcel_price' => $this->superprepayment_parcel_price,
            'superprepayment_loyal_price' => $this->superprepayment_loyal_price,
            'superprepayment_reservation_cost' => $this->superprepayment_reservation_cost,

            'commercial' => $this->commercial
        ]);

        return $dataProvider;
    }
}
