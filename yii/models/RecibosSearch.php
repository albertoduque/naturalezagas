<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Recibos;

/**
 * RecibosSearch represents the model behind the search form about `app\models\Recibos`.
 */
class RecibosSearch extends Recibos
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'valor_descuento', 'valor_retencion', 'valor_pagado', 'valor_subtotal', 'valor_iva', 'id_forma_pago', 'deleted'], 'integer'],
            [['fecha_pago', 'tipo_pago', 'created_at', 'modified_at'], 'safe'],
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
        $query = Recibos::find();

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
            'fecha_pago' => $this->fecha_pago,
            'valor_descuento' => $this->valor_descuento,
            'valor_retencion' => $this->valor_retencion,
            'valor_pagado' => $this->valor_pagado,
            'valor_subtotal' => $this->valor_subtotal,
            'valor_iva' => $this->valor_iva,
            'id_forma_pago' => $this->id_forma_pago,
            'created_at' => $this->created_at,
            'modified_at' => $this->modified_at,
            'deleted' => $this->deleted,
        ]);

        $query->andFilterWhere(['like', 'tipo_pago', $this->tipo_pago]);

        return $dataProvider;
    }
}
