<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\DetalleRecibos;

/**
 * DetalleRecibosSearch represents the model behind the search form about `app\models\DetalleRecibos`.
 */
class DetalleRecibosSearch extends DetalleRecibos
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_factura', 'id_forma_pago', 'deleted'], 'integer'],
            [['fecha_pago', 'tipo_pago', 'created_at', 'modified_at'], 'safe'],
            [['valor'], 'number'],
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
        $query = DetalleRecibos::find();

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
            'id_factura' => $this->id_factura,
            'fecha_pago' => $this->fecha_pago,
            'valor' => $this->valor,
            'id_forma_pago' => $this->id_forma_pago,
            'created_at' => $this->created_at,
            'modified_at' => $this->modified_at,
            'deleted' => $this->deleted,
        ]);

        $query->andFilterWhere(['like', 'tipo_pago', $this->tipo_pago]);

        return $dataProvider;
    }
}
